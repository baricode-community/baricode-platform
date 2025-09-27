<?php

namespace Tests\Feature;

use App\Models\Meet;
use App\Models\User\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Livewire\Volt\Volt;

class MeetLivewireTest extends TestCase
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

    public function test_meets_index_page_loads_successfully()
    {
        $meets = Meet::factory(3)->create();

        $response = $this->get(route('meets.index'));

        $response->assertStatus(200)
                ->assertSee('Daftar Meet');
    }

    public function test_meets_index_displays_meets()
    {
        $meet = Meet::factory()->create([
            'title' => 'Test Meeting',
            'description' => 'Test Description'
        ]);

        $response = $this->get(route('meets.index'));

        $response->assertStatus(200)
                ->assertSee('Test Meeting')
                ->assertSee('Test Description');
    }

    public function test_meet_show_page_loads_successfully()
    {
        $meet = Meet::factory()->create();

        $response = $this->get(route('meets.show', $meet));

        $response->assertStatus(200)
                ->assertSee($meet->title);
    }

    public function test_user_can_search_meets()
    {
        $meet1 = Meet::factory()->create(['title' => 'Laravel Meeting']);
        $meet2 = Meet::factory()->create(['title' => 'PHP Meeting']);

        Volt::test('meets.index')
            ->set('search', 'Laravel')
            ->assertSee('Laravel Meeting')
            ->assertDontSee('PHP Meeting');
    }

    public function test_user_can_join_meet()
    {
        $meet = Meet::factory()->create();

        Volt::test('meets.show', ['meet' => $meet])
            ->actingAs($this->user)
            ->call('joinMeet')
            ->assertHasNoErrors()
            ->assertSet('meet.users', function ($users) use ($meet) {
                return $users->contains('id', $this->user->id);
            });

        $this->assertDatabaseHas('meet_user', [
            'meet_id' => $meet->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_can_leave_meet()
    {
        $meet = Meet::factory()->create();
        $meet->users()->attach($this->user->id, ['joined_at' => now()]);

        Volt::test('meets.show', ['meet' => $meet])
            ->actingAs($this->user)
            ->call('leaveMeet')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('meet_user', [
            'meet_id' => $meet->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_cannot_join_same_meet_twice()
    {
        $meet = Meet::factory()->create();
        $meet->users()->attach($this->user->id, ['joined_at' => now()]);

        Volt::test('meets.show', ['meet' => $meet])
            ->actingAs($this->user)
            ->call('joinMeet')
            ->assertSessionHas('error', 'Anda sudah terdaftar dalam meet ini.');
    }

    public function test_guest_must_login_to_join_meet()
    {
        $meet = Meet::factory()->create();

        Volt::test('meets.show', ['meet' => $meet])
            ->call('joinMeet')
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_create_meet()
    {
        Volt::test('admin.courses.meet-management')
            ->actingAs($this->admin)
            ->set('title', 'Test Meeting')
            ->set('youtube_link', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
            ->set('description', 'This is a test meeting')
            ->call('createMeet')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('meets', [
            'title' => 'Test Meeting',
            'youtube_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ]);
    }

    public function test_admin_can_update_meet()
    {
        $meet = Meet::factory()->create();

        Volt::test('admin.courses.meet-management')
            ->actingAs($this->admin)
            ->set('editingMeet', $meet->id)
            ->set('title', 'Updated Meeting Title')
            ->set('youtube_link', 'https://www.youtube.com/watch?v=updated')
            ->set('description', 'Updated description')
            ->call('updateMeet')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('meets', [
            'id' => $meet->id,
            'title' => 'Updated Meeting Title'
        ]);
    }

    public function test_admin_can_delete_meet()
    {
        $meet = Meet::factory()->create();

        Volt::test('admin.courses.meet-management')
            ->actingAs($this->admin)
            ->set('meetToDelete', $meet->id)
            ->call('deleteMeet')
            ->assertHasNoErrors();

        $this->assertSoftDeleted('meets', ['id' => $meet->id]);
    }

    public function test_admin_can_manage_participants()
    {
        $meet = Meet::factory()->create();
        $users = User::factory(3)->create();

        Volt::test('admin.courses.meet-management')
            ->actingAs($this->admin)
            ->set('selectedMeet', $meet)
            ->set('selectedUsers', $users->pluck('id')->toArray())
            ->call('updateParticipants')
            ->assertHasNoErrors();

        foreach ($users as $user) {
            $this->assertDatabaseHas('meet_user', [
                'meet_id' => $meet->id,
                'user_id' => $user->id
            ]);
        }
    }

    public function test_meets_require_valid_data()
    {
        Volt::test('admin.courses.meet-management')
            ->actingAs($this->admin)
            ->set('title', '')
            ->set('youtube_link', 'invalid-url')
            ->call('createMeet')
            ->assertHasErrors(['title', 'youtube_link']);
    }

    public function test_non_admin_cannot_access_admin_meet_management()
    {
        $response = $this->actingAs($this->user)
                         ->get(route('admin.meets'));

        $response->assertStatus(403);
    }

    public function test_meet_sorting_works()
    {
        $meet1 = Meet::factory()->create(['title' => 'Alpha Meeting', 'scheduled_at' => now()->addDay()]);
        $meet2 = Meet::factory()->create(['title' => 'Beta Meeting', 'scheduled_at' => now()->addDays(2)]);

        Volt::test('meets.index')
            ->call('sortBy', 'title')
            ->assertSeeInOrder(['Alpha Meeting', 'Beta Meeting']);
    }

    public function test_meet_pagination_works()
    {
        Meet::factory(15)->create();

        $response = $this->get(route('meets.index'));

        $response->assertStatus(200)
                ->assertSee('Next');
    }
}
