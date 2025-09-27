<?php

namespace Tests\Feature;

use App\Models\Meet;
use App\Models\User\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MeetControllerTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        
        // Create test users
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_index_returns_meets_list()
    {
        $meets = Meet::factory(3)->create();

        $response = $this->getJson('/api/meets');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => ['id', 'title', 'youtube_link', 'description', 'scheduled_at', 'participants_count', 'is_participant', 'created_at']
                        ]
                    ]
                ]);
    }

    public function test_show_returns_single_meet()
    {
        $meet = Meet::factory()->create();

        $response = $this->getJson("/api/meets/{$meet->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => ['id', 'title', 'youtube_link', 'description', 'scheduled_at', 'participants', 'participants_count', 'is_participant', 'created_at']
                ]);
    }

    public function test_admin_can_create_meet()
    {
        $meetData = [
            'title' => 'Test Meeting',
            'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'description' => 'This is a test meeting',
            'scheduled_at' => now()->addDay()->toISOString(),
        ];

        $response = $this->actingAs($this->admin)
                         ->postJson('/api/meets', $meetData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Meet created successfully'
                ]);

        $this->assertDatabaseHas('meets', [
            'title' => 'Test Meeting',
            'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ]);
    }

    public function test_non_admin_cannot_create_meet()
    {
        $meetData = [
            'title' => 'Test Meeting',
            'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ];

        $response = $this->actingAs($this->user)
                         ->postJson('/api/meets', $meetData);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_meet()
    {
        $meet = Meet::factory()->create();
        
        $updateData = [
            'title' => 'Updated Meeting Title',
            'youtube_link' => 'https://www.youtube.com/watch?v=updated',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->admin)
                         ->putJson("/api/meets/{$meet->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Meet updated successfully'
                ]);

        $this->assertDatabaseHas('meets', [
            'id' => $meet->id,
            'title' => 'Updated Meeting Title'
        ]);
    }

    public function test_admin_can_delete_meet()
    {
        $meet = Meet::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->deleteJson("/api/meets/{$meet->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Meet deleted successfully'
                ]);

        $this->assertSoftDeleted('meets', ['id' => $meet->id]);
    }

    public function test_user_can_join_meet()
    {
        $meet = Meet::factory()->create();

        $response = $this->actingAs($this->user)
                         ->postJson("/api/meets/{$meet->id}/join");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Successfully joined the meet'
                ]);

        $this->assertDatabaseHas('meet_user', [
            'meet_id' => $meet->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_cannot_join_same_meet_twice()
    {
        $meet = Meet::factory()->create();
        $meet->users()->attach($this->user->id);

        $response = $this->actingAs($this->user)
                         ->postJson("/api/meets/{$meet->id}/join");

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'You are already a participant of this meet'
                ]);
    }

    public function test_user_can_leave_meet()
    {
        $meet = Meet::factory()->create();
        $meet->users()->attach($this->user->id);

        $response = $this->actingAs($this->user)
                         ->deleteJson("/api/meets/{$meet->id}/leave");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Successfully left the meet'
                ]);

        $this->assertDatabaseMissing('meet_user', [
            'meet_id' => $meet->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_cannot_leave_meet_they_havent_joined()
    {
        $meet = Meet::factory()->create();

        $response = $this->actingAs($this->user)
                         ->deleteJson("/api/meets/{$meet->id}/leave");

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'You are not a participant of this meet'
                ]);
    }

    public function test_create_meet_requires_valid_data()
    {
        $response = $this->actingAs($this->admin)
                         ->postJson('/api/meets', [
                             'title' => '',
                             'youtube_link' => 'invalid-url'
                         ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'youtube_link']);
    }

    public function test_guest_cannot_join_or_leave_meet()
    {
        $meet = Meet::factory()->create();

        $joinResponse = $this->postJson("/api/meets/{$meet->id}/join");
        $leaveResponse = $this->deleteJson("/api/meets/{$meet->id}/leave");

        $joinResponse->assertStatus(401);
        $leaveResponse->assertStatus(401);
    }
}
