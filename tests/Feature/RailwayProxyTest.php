<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RailwayProxyTest extends TestCase
{
    public function test_railway_forwarded_https_is_used_for_generated_urls(): void
    {
        $previous = getenv('RAILWAY_ENVIRONMENT_ID');
        putenv('RAILWAY_ENVIRONMENT_ID=test-railway');

        try {
            $this->refreshApplication();
            Route::get('/test-proxy', fn () => url('/login'));

            $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
                ->withHeaders([
                    'Host' => 'bookease.example.com',
                    'X-Forwarded-Proto' => 'https',
                    'X-Forwarded-Port' => '443',
                ])
                ->get('http://bookease.example.com/test-proxy')
                ->assertOk()
                ->assertSee('https://bookease.example.com/login', false);
        } finally {
            putenv($previous === false ? 'RAILWAY_ENVIRONMENT_ID' : 'RAILWAY_ENVIRONMENT_ID='.$previous);
        }
    }
}
