<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppTemplate implements ShouldQueue
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
        protected string $templateName,
        protected array $variables = [],
        protected ?string $context = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        try {
            $result = $whatsAppService->sendTemplate($this->phone, $this->templateName, $this->variables);

            Log::info('WhatsApp template sent via queue', [
                'phone' => $this->phone,
                'template' => $this->templateName,
                'context' => $this->context,
                'success' => $result['success'] ?? false,
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp template failed via queue', [
                'phone' => $this->phone,
                'template' => $this->templateName,
                'context' => $this->context,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::critical('WhatsApp template permanently failed', [
            'phone' => $this->phone,
            'template' => $this->templateName,
            'context' => $this->context,
            'error' => $exception?->getMessage(),
        ]);
    }
}
