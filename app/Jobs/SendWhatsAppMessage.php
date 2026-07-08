<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Number of seconds to wait before retrying.
     */
    public int $backoff = 30;

    /**
     * Delete the job if its models no longer exist.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $phone,
        protected string $message,
        protected array $options = [],
        protected ?string $context = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        try {
            $result = $whatsAppService->sendMessage($this->phone, $this->message, $this->options);

            Log::info('WhatsApp message sent via queue', [
                'phone' => $this->phone,
                'context' => $this->context,
                'success' => $result['success'] ?? false,
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp message failed via queue', [
                'phone' => $this->phone,
                'context' => $this->context,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::critical('WhatsApp message permanently failed', [
            'phone' => $this->phone,
            'context' => $this->context,
            'error' => $exception?->getMessage(),
        ]);
    }
}
