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
        // Quick check: is MailHog API reachable? If not, skip this integration test.
        try {
            $health = Http::timeout(2)->get('http://127.0.0.1:8025/api/v2/messages');
        } catch (\Exception $e) {
            $this->markTestSkipped('Mailhog not available at http://127.0.0.1:8025 - skipping integration test.');
            return;
        }

        if ($health->failed()) {
            $this->markTestSkipped('Mailhog API not reachable - skipping integration test.');
            return;
        }

        // Clear existing messages in MailHog
        try {
            Http::delete('http://127.0.0.1:8025/api/v1/messages');
        } catch (\Exception $e) {
            // ignore; we'll still try
        }

        // Configure Laravel mailer to use MailHog's SMTP
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

        // Poll MailHog API for up to ~5 seconds for the expected message
        $found = false;
        $attempts = 0;
        while ($attempts++ < 25) {
            try {
                $resp = Http::timeout(2)->get('http://127.0.0.1:8025/api/v2/messages');
            } catch (\Exception $e) {
                usleep(200000);
                continue;
            }

            if (! $resp->ok()) {
                usleep(200000);
                continue;
            }

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

            usleep(200000);
        }

        $this->assertTrue($found, 'Expected email not found in Mailhog within timeout. Make sure Mailhog is running on 127.0.0.1:8025 and SMTP on 127.0.0.1:1025.');
    }
}

