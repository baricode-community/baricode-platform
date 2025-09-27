<?php

namespace Tests\Unit;

use App\Models\Meet;
use App\Models\User\User;
use Tests\TestCase;

class MeetTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_meet_can_be_created()
    {
        $meetData = [
            'title' => 'Test Meeting',
            'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'description' => 'This is a test meeting',
            'scheduled_at' => now()->addDay(),
        ];

        $meet = Meet::create($meetData);

        $this->assertInstanceOf(Meet::class, $meet);
        $this->assertEquals('Test Meeting', $meet->title);
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $meet->youtube_link);
        $this->assertEquals('This is a test meeting', $meet->description);
    }

    public function test_meet_can_have_many_users()
    {
        $meet = Meet::factory()->create();
        $users = User::factory(3)->create();

        $meet->users()->attach($users->pluck('id'));

        $this->assertEquals(3, $meet->users()->count());
        $this->assertInstanceOf(User::class, $meet->users()->first());
    }

    public function test_user_can_have_many_meets()
    {
        $user = User::factory()->create();
        $meets = Meet::factory(2)->create();

        $user->meets()->attach($meets->pluck('id'));

        $this->assertEquals(2, $user->meets()->count());
        $this->assertInstanceOf(Meet::class, $user->meets()->first());
    }

    public function test_meet_participants_count_method()
    {
        $meet = Meet::factory()->create();
        $users = User::factory(5)->create();

        $meet->users()->attach($users->pluck('id'));

        $this->assertEquals(5, $meet->participantsCount());
    }

    public function test_meet_is_participant_method()
    {
        $meet = Meet::factory()->create();
        $participantUser = User::factory()->create();
        $nonParticipantUser = User::factory()->create();

        $meet->users()->attach($participantUser->id);

        $this->assertTrue($meet->isParticipant($participantUser));
        $this->assertFalse($meet->isParticipant($nonParticipantUser));
    }

    public function test_meet_has_fillable_attributes()
    {
        $meet = new Meet();
        $fillable = $meet->getFillable();

        $expectedFillable = [
            'title',
            'youtube_link',
            'description',
            'scheduled_at'
        ];

        foreach ($expectedFillable as $field) {
            $this->assertContains($field, $fillable);
        }
    }

    public function test_meet_casts_scheduled_at_to_datetime()
    {
        $meet = Meet::factory()->create([
            'scheduled_at' => '2025-12-25 15:30:00'
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $meet->scheduled_at);
    }

    public function test_meet_uses_soft_deletes()
    {
        $meet = Meet::factory()->create();
        $meetId = $meet->id;

        $meet->delete();

        // Should not be found in normal queries
        $this->assertNull(Meet::find($meetId));
        
        // Should be found when including trashed
        $this->assertNotNull(Meet::withTrashed()->find($meetId));
    }
}
