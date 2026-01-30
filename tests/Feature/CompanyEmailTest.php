<?php

namespace Tests\Feature;

use App\Mail\CompanyCreated;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_creation_sends_email_to_configured_recipient(): void
    {
        Mail::fake();

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

        Mail::assertSent(CompanyCreated::class, function (CompanyCreated $mail) use ($payload) {
            // Mailable should contain the company with expected attributes
            $company = $mail->company;

            return $company->name === $payload['name']
                && $company->email === $payload['email'];
        });
    }
}

