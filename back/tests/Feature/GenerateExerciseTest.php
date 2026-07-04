<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateExerciseTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_endpoint_accepts_topic_payload_and_returns_exercise_json(): void
    {
        $user = User::factory()->create();

        $this->mock(GptService::class, function ($mock) {
            $mock->shouldReceive('askJson')
                ->once()
                ->andReturn([
                    'title' => 'JavaScript Closures',
                    'topic' => 'closures',
                ]);
        });

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/generate', [
                'topic' => 'JavaScript closures',
            ]);

        $response->assertOk()
            ->assertJson([
                'title' => 'JavaScript Closures',
                'topic' => 'closures',
            ]);
    }

    public function test_generate_endpoint_returns_bad_gateway_when_ai_service_fails(): void
    {
        $user = User::factory()->create();

        $this->mock(GptService::class, function ($mock) {
            $mock->shouldReceive('askJson')
                ->once()
                ->andThrow(new \RuntimeException('KIConnect connection failed: SSL issue'));
        });

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/generate', [
                'topic' => 'JavaScript closures',
            ]);

        $response->assertStatus(502)
            ->assertJsonPath('error', 'KIConnect connection failed: SSL issue');
    }
}
