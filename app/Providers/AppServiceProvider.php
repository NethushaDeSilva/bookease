<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        Mail::extend('brevo', function () {
            $key = config('services.brevo.key');

            if (! is_string($key) || trim($key) === '') {
                throw new \InvalidArgumentException('Brevo email delivery requires BREVO_API_KEY.');
            }

            return (new BrevoTransportFactory())->create(
                new Dsn('brevo+api', 'default', $key)
            );
        });

        RateLimiter::for(
            'api',
            function (Request $request): Limit {
                return Limit::perMinute(60)->by(
                    $request->user()?->id
                        ?: $request->ip()
                );
            }
        );
    }
}
