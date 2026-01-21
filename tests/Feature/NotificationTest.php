<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\ActivityLog;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Make sure to create users and posts for testing
    }

    /**
     * Test storing a notification with valid data
     */
    public function test_store_notification_with_user(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/notifications', [
            'channel' => 'email',
            'message' => 'Test notification message',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
        ], [
            'X-Client-Key' => env('APP_CLIENT_KEY', 'your-secret-key'),
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'channel',
                'message',
                'notifiable_type',
                'notifiable_id',
                'created_at',
            ],
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'channel' => 'email',
            'message' => 'Test notification message',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
        ]);
    }

    /**
     * Test storing a notification with post
     */
    public function test_store_notification_with_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->postJson('/api/notifications', [
            'channel' => 'sms',
            'message' => 'Post notification',
            'notifiable_type' => 'App\Models\Post',
            'notifiable_id' => $post->id,
        ], [
            'X-Client-Key' => env('APP_CLIENT_KEY', 'your-secret-key'),
        ]);

        $response->assertStatus(201);
    }

    /**
     * Test missing X-Client-Key header
     */
    public function test_missing_client_key_header(): void
    {
        $response = $this->postJson('/api/notifications', [
            'channel' => 'email',
            'message' => 'Test message',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => 1,
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorized: Missing X-Client-Key header.',
        ]);
    }

    /**
     * Test invalid channel
     */
    public function test_invalid_channel(): void
    {
        $response = $this->postJson('/api/notifications', [
            'channel' => 'invalid_channel',
            'message' => 'Test message',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => 1,
        ], [
            'X-Client-Key' => env('APP_CLIENT_KEY', 'your-secret-key'),
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test non-existent notifiable_id
     */
    public function test_non_existent_notifiable_id(): void
    {
        $response = $this->postJson('/api/notifications', [
            'channel' => 'email',
            'message' => 'Test message',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => 99999,
        ], [
            'X-Client-Key' => env('APP_CLIENT_KEY', 'your-secret-key'),
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test message exceeding 255 characters
     */
    public function test_message_exceeds_max_length(): void
    {
        $user = User::factory()->create();
        $longMessage = str_repeat('a', 256);

        $response = $this->postJson('/api/notifications', [
            'channel' => 'email',
            'message' => $longMessage,
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
        ], [
            'X-Client-Key' => env('APP_CLIENT_KEY', 'your-secret-key'),
        ]);

        $response->assertStatus(422);
    }
}
