<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyMailhogIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_creation_delivers_email_to_mailhog(): void
    {
        // Determine which mail catcher is available: MailHog (8025) or MailCatcher (1080)
        $service = null;
        try {
            $resp = Http::timeout(2)->get('http://127.0.0.1:8025/api/v2/messages');
            if ($resp->ok()) {
                $service = 'mailhog';
            }
        } catch (\Exception $e) {
            // ignore
        }

        if (! $service) {
            try {
                $resp = Http::timeout(2)->get('http://127.0.0.1:1080/messages.json');
                if ($resp->ok()) {
                    $service = 'mailcatcher';
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        if (! $service) {
            $this->markTestSkipped('No MailHog or MailCatcher instance detected (checked 127.0.0.1:8025 and 127.0.0.1:1080) - skipping integration test.');
            return;
        }

        // Clear existing messages (best-effort)
        try {
            if ($service === 'mailhog') {
                Http::delete('http://127.0.0.1:8025/api/v1/messages');
            } else {
                Http::delete('http://127.0.0.1:1080/messages');
            }
        } catch (\Exception $e) {
            // ignore
        }

        // Configure Laravel mailer to use local SMTP (both MailHog and MailCatcher listen on 1025)
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', '127.0.0.1');
        Config::set('mail.mailers.smtp.port', 1025);
        Config::set('mail.mailers.smtp.encryption', null);
        Config::set('mail.mailers.smtp.username', null);
        Config::set('mail.mailers.smtp.password', null);

        // Use a known from address for the assertion
        Config::set('mail.from.address', 'integration@test.local');
        Config::set('mail.from.name', 'Integration Test');

        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'name' => 'Initech',
            'email' => 'hello@initech.test',
            'logo' => 'logos/initech.png',
            'website' => 'https://initech.test',
        ];

        $response = $this->post('/company', $payload);
        $response->assertStatus(201);

        // Poll the appropriate API for up to ~5 seconds for the expected message
        $found = false;
        $attempts = 0;
        while ($attempts++ < 25) {
            try {
                if ($service === 'mailhog') {
                    $resp = Http::timeout(2)->get('http://127.0.0.1:8025/api/v2/messages');
                    if (! $resp->ok()) { usleep(200000); continue; }

                    $items = $resp->json('items', []);
                    foreach ($items as $item) {
                        $subject = $item['Content']['Headers']['Subject'][0] ?? '';
                        $headersTo = $item['Content']['Headers']['To'][0] ?? '';

                        if (strpos($subject, 'New company created') !== false
                            && strpos($headersTo, 'integration@test.local') !== false) {
                            $found = true;
                            break 2;
                        }
                    }
                } else {
                    // MailCatcher: /messages.json returns an array of messages
                    $resp = Http::timeout(2)->get('http://127.0.0.1:1080/messages.json');
                    if (! $resp->ok()) { usleep(200000); continue; }

                    $items = $resp->json() ?: [];
                    foreach ($items as $item) {
                        $subject = $item['subject'] ?? ($item['mime']['headers']['subject'][0] ?? '');
                        $recipients = $item['recipients'] ?? ($item['mime']['headers']['to'][0] ?? '');

                        if (strpos($subject, 'New company created') !== false
                            && strpos($recipients, 'integration@test.local') !== false) {
                            $found = true;
                            break 2;
                        }
                    }
                }
            } catch (\Exception $e) {
                // ignore and retry
            }

            usleep(200000);
        }

        $this->assertTrue($found, 'Expected email not found in MailHog/MailCatcher within timeout. Make sure MailHog (127.0.0.1:8025) or MailCatcher (127.0.0.1:1080) is running and SMTP on 127.0.0.1:1025.');
    }
}

