<?php

namespace Tests\Feature;

use App\Exceptions\TemplateNotFoundException;
use App\Jobs\SendWhatsAppMessage;
use App\Jobs\SendWhatsAppTemplate;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WhatsAppServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    // ─── SERVICE: DISABLED ─────────────────────────────────

    public function test_disabled_service_returns_not_sent()
    {
        config(['services.whatsapp.enabled' => false]);

        $service = new WhatsAppService();
        $result = $service->sendMessage('08123456789', 'Hello');

        $this->assertFalse($result['success']);
        $this->assertEquals('disabled', $result['mode']);
    }

    public function test_is_enabled_returns_config_value()
    {
        config(['services.whatsapp.enabled' => false]);
        $this->assertFalse((new WhatsAppService())->isEnabled());

        config(['services.whatsapp.enabled' => true]);
        $this->assertTrue((new WhatsAppService())->isEnabled());
    }

    // ─── SERVICE: SEND MESSAGE ─────────────────────────────

    public function test_send_message_success()
    {
        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.api_url' => 'https://api.fonnte.test',
            'services.whatsapp.api_token' => 'test-token',
        ]);

        Http::fake([
            'api.fonnte.test/*' => Http::response(['status' => true, 'detail' => 'sent'], 200),
        ]);

        $service = new WhatsAppService();
        $result = $service->sendMessage('08123456789', 'Test message');

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['status_code']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.test/send'
                && $request['target'] === '628123456789'
                && $request['message'] === 'Test message';
        });
    }

    public function test_send_message_normalizes_phone_number()
    {
        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.api_url' => 'https://api.fonnte.test',
            'services.whatsapp.api_token' => 'test-token',
        ]);

        Http::fake(['*' => Http::response(['status' => true], 200)]);

        $service = new WhatsAppService();

        // 0xxx -> 62xxx
        $service->sendMessage('081234567890', 'test');
        Http::assertSent(fn($r) => $r['target'] === '6281234567890');
    }

    public function test_send_message_handles_api_failure()
    {
        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.api_url' => 'https://api.fonnte.test',
            'services.whatsapp.api_token' => 'test-token',
        ]);

        Http::fake([
            'api.fonnte.test/*' => Http::response(['status' => false], 500),
        ]);

        $service = new WhatsAppService();
        $result = $service->sendMessage('08123456789', 'Test');

        $this->assertFalse($result['success']);
        $this->assertEquals(500, $result['status_code']);
    }

    public function test_send_message_handles_connection_exception()
    {
        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.api_url' => 'https://api.fonnte.test',
            'services.whatsapp.api_token' => 'test-token',
            'services.whatsapp.timeout' => 1,
        ]);

        Http::fake([
            'api.fonnte.test/*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
            },
        ]);

        $service = new WhatsAppService();
        $result = $service->sendMessage('08123456789', 'Test');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Connection timeout', $result['error']);
    }

    // ─── SERVICE: TEMPLATE ─────────────────────────────────

    public function test_send_template_renders_and_sends()
    {
        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.api_url' => 'https://api.fonnte.test',
            'services.whatsapp.api_token' => 'test-token',
            'whatsapp-templates' => [
                'test_greeting' => 'Halo {name}, selamat datang!',
            ],
        ]);

        Http::fake(['*' => Http::response(['status' => true], 200)]);

        $service = new WhatsAppService();
        $result = $service->sendTemplate('08123456789', 'test_greeting', ['name' => 'Ahmad']);

        $this->assertTrue($result['success']);
        Http::assertSent(fn($r) => $r['message'] === 'Halo Ahmad, selamat datang!');
    }

    public function test_send_template_throws_for_invalid_template()
    {
        config([
            'services.whatsapp.enabled' => true,
            'whatsapp-templates' => [],
        ]);

        $service = new WhatsAppService();

        $this->expectException(TemplateNotFoundException::class);
        $service->sendTemplate('08123456789', 'nonexistent_template');
    }

    // ─── SERVICE: BULK SEND ────────────────────────────────

    public function test_bulk_send_dispatches_jobs()
    {
        Queue::fake();

        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.api_url' => 'https://api.fonnte.test',
            'services.whatsapp.api_token' => 'test-token',
        ]);

        $service = new WhatsAppService();
        $result = $service->sendBulk([
            ['phone' => '08111111111', 'message' => 'Msg 1'],
            ['phone' => '08222222222', 'message' => 'Msg 2'],
            ['phone' => '08333333333', 'message' => 'Msg 3'],
        ], 1);

        $this->assertTrue($result['success']);
        $this->assertEquals(3, $result['dispatched']);

        Queue::assertPushed(SendWhatsAppMessage::class, 3);
    }

    // ─── SERVICE: ACCOUNT INFO ─────────────────────────────

    public function test_get_account_info_disabled()
    {
        config(['services.whatsapp.enabled' => false]);

        $service = new WhatsAppService();
        $result = $service->getAccountInfo();

        $this->assertFalse($result['success']);
    }

    public function test_get_account_info_success()
    {
        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.api_url' => 'https://api.fonnte.test',
            'services.whatsapp.api_token' => 'test-token',
        ]);

        Http::fake([
            'api.fonnte.test/device' => Http::response(['device' => 'active'], 200),
        ]);

        $service = new WhatsAppService();
        $result = $service->getAccountInfo();

        $this->assertTrue($result['success']);
        $this->assertEquals('active', $result['data']['device']);
    }

    // ─── JOB: SendWhatsAppMessage ──────────────────────────

    public function test_send_whatsapp_message_job_dispatches()
    {
        Queue::fake();

        SendWhatsAppMessage::dispatch('08123456789', 'Hello from job', [], 'test-context');

        Queue::assertPushed(SendWhatsAppMessage::class, function ($job) {
            return true; // Just check it was dispatched
        });
    }

    public function test_send_whatsapp_message_job_executes()
    {
        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.api_url' => 'https://api.fonnte.test',
            'services.whatsapp.api_token' => 'test-token',
        ]);

        Http::fake(['*' => Http::response(['status' => true], 200)]);

        $job = new SendWhatsAppMessage('08123456789', 'Job test message', [], 'unit-test');
        $job->handle(app(WhatsAppService::class));

        Http::assertSent(fn($r) => $r['message'] === 'Job test message');
    }

    // ─── JOB: SendWhatsAppTemplate ─────────────────────────

    public function test_send_whatsapp_template_job_dispatches()
    {
        Queue::fake();

        SendWhatsAppTemplate::dispatch('08123456789', 'test_template', ['name' => 'Ahmad'], 'test');

        Queue::assertPushed(SendWhatsAppTemplate::class);
    }

    public function test_send_whatsapp_template_job_executes()
    {
        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.api_url' => 'https://api.fonnte.test',
            'services.whatsapp.api_token' => 'test-token',
            'whatsapp-templates' => [
                'welcome' => 'Selamat datang {name}!',
            ],
        ]);

        Http::fake(['*' => Http::response(['status' => true], 200)]);

        $job = new SendWhatsAppTemplate('08123456789', 'welcome', ['name' => 'Ahmad'], 'unit-test');
        $job->handle(app(WhatsAppService::class));

        Http::assertSent(fn($r) => $r['message'] === 'Selamat datang Ahmad!');
    }
}
