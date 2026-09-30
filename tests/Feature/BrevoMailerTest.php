<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoApiTransport;
use Tests\TestCase;

class BrevoMailerTest extends TestCase
{
    public function test_brevo_uses_the_https_api_transport(): void
    {
        config(['services.brevo.key' => 'test-key-not-a-real-secret']);

        $this->assertInstanceOf(
            BrevoApiTransport::class,
            Mail::mailer('brevo')->getSymfonyTransport()
        );
    }

    public function test_missing_key_fails_instead_of_silently_logging_email(): void
    {
        config(['services.brevo.key' => null]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Brevo email delivery requires BREVO_API_KEY.');

        Mail::mailer('brevo');
    }
}
