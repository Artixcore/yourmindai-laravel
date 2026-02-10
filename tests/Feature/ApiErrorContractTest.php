<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiErrorContractTest extends TestCase
{
    use RefreshDatabase;

    /**
     * API 404 (e.g. route not found) returns JSON with contract: success, message, errors, code.
     */
    public function test_api_404_returns_contract_shape(): void
    {
        $response = $this->getJson('/api/nonexistent-route-for-testing');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
                'code',
            ])
            ->assertJson([
                'success' => false,
                'code' => 404,
            ])
            ->assertJsonPath('errors', []);
    }

    /**
     * API 500 (unhandled exception) returns JSON with contract and safe message.
     */
    public function test_api_500_returns_contract_shape(): void
    {
        $response = $this->getJson('/api/test/server-error');

        $response->assertStatus(500)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
                'code',
            ])
            ->assertJson([
                'success' => false,
                'code' => 500,
            ])
            ->assertJsonPath('errors', []);
    }
}
