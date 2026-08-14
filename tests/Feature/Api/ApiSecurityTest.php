<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * An API request without a token must be rejected.
     */
    public function test_api_rejects_request_without_token(): void
    {
        $response = $this->getJson(
            '/api/v1/services'
        );

        $response
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * A token without the required ability must be rejected.
     */
    public function test_api_rejects_token_without_required_ability(): void
    {
        $customer = User::factory()
            ->customer()
            ->create();

        Sanctum::actingAs(
            $customer,
            ['bookings:read']
        );

        $response = $this->getJson(
            '/api/v1/services'
        );

        $response->assertForbidden();
    }

    /**
     * A token with the correct ability can retrieve paginated services.
     */
    public function test_correct_ability_returns_paginated_services(): void
    {
        $customer = User::factory()
            ->customer()
            ->create();

        Sanctum::actingAs(
            $customer,
            ['services:read']
        );

        $response = $this->getJson(
            '/api/v1/services'
        );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);
    }

    /**
 * A suspended user cannot access the API.
 */
public function test_suspended_user_cannot_access_api(): void
{
    $customer = User::factory()
        ->customer()
        ->suspended()
        ->create();

    Sanctum::actingAs(
        $customer,
        ['services:read']
    );

    $response = $this->getJson(
        '/api/v1/services'
    );

    $response
        ->assertForbidden()
        ->assertJson([
            'message' =>
                'Your account has been suspended.',
        ]);
}
}
