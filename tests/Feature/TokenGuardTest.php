<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

/**
 * Regression test for CVE-2026-114509 (GHSA-f9x3-7vq9-cph6):
 * Laravel's TokenGuard could be bypassed to authenticate as an arbitrary
 * user when a non-string API credential was supplied under certain
 * MySQL/MariaDB configurations.
 *
 * NOTE: the app's own migrations do not create an api_token column, so this
 * test adds the column to the in-memory test database only, to exercise the
 * guard the way a deployment with API tokens would. It does not change the
 * production schema.
 */
class TokenGuardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::table('users', function (Blueprint $table) {
            $table->string('api_token', 80)->nullable()->unique();
        });
    }

    public function test_string_token_authenticates(): void
    {
        $user = User::factory()->create(['api_token' => 'secret-token-123']);

        $response = $this->getJson('/api/user?api_token=secret-token-123');

        $response->assertStatus(200);
        $response->assertJsonPath('id', $user->id);
        $response->assertJsonPath('email', $user->email);
    }

    public function test_non_string_token_does_not_authenticate(): void
    {
        User::factory()->create(['api_token' => 'secret-token-123']);

        // Array credential: the CVE bypass vector. Must never yield a user.
        $response = $this->getJson('/api/user?api_token[]=secret-token-123');

        $response->assertStatus(401);
    }

    public function test_wrong_token_does_not_authenticate(): void
    {
        User::factory()->create(['api_token' => 'secret-token-123']);

        $response = $this->getJson('/api/user?api_token=nope');

        $response->assertStatus(401);
    }

    public function test_missing_token_does_not_authenticate(): void
    {
        User::factory()->create(['api_token' => 'secret-token-123']);

        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}
