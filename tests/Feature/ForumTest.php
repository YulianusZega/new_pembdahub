<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\ForumLike;
use App\Models\ForumMember;
use App\Models\Reputation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    public function test_user_can_view_forum_board()
    {
        $user = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($user)
            ->get(route('forum.index'));

        $response->assertStatus(200);
        $response->assertSee('Hub Komunitas');
    }

    public function test_user_can_create_thread_and_earns_reputation_points()
    {
        $user = User::factory()->create(['role' => 'siswa']);

        // Check starting points
        $startPoints = $user->reputation->total_points ?? 0;

        $response = $this->actingAs($user)
            ->post(route('forum.store'), [
                'title' => 'Mengapa Project IoT itu Seru?',
                'content' => 'Mari kita bahas pembuatan micro-controller ESP32 menggunakan Laravel IoT API.',
                'category' => 'diskusi',
            ]);

        $this->assertDatabaseHas('forum_threads', [
            'title' => 'Mengapa Project IoT itu Seru?',
            'category' => 'diskusi',
            'user_id' => $user->id,
        ]);

        $user->refresh();
        $endPoints = $user->reputation->total_points ?? 0;

        // Creating thread gives +15 points
        $this->assertEquals($startPoints + 15, $endPoints);
        $response->assertRedirect();
    }

    public function test_user_can_reply_and_earns_points()
    {
        $author = User::factory()->create(['role' => 'siswa']);
        $thread = ForumThread::create([
            'user_id' => $author->id,
            'title' => 'Belajar Coding',
            'content' => 'Belajar coding bareng di lab yuk.',
            'category' => 'diskusi',
        ]);

        $replier = User::factory()->create(['role' => 'siswa']);
        $startPoints = $replier->reputation->total_points ?? 0;

        $response = $this->actingAs($replier)
            ->post(route('forum.reply', $thread), [
                'content' => 'Aku ikut! Mau jam berapa?',
            ]);

        $this->assertDatabaseHas('forum_replies', [
            'forum_thread_id' => $thread->id,
            'user_id' => $replier->id,
            'content' => 'Aku ikut! Mau jam berapa?',
        ]);

        $replier->refresh();
        $endPoints = $replier->reputation->total_points ?? 0;

        // Commenting gives +5 points
        $this->assertEquals($startPoints + 5, $endPoints);
        $response->assertRedirect();
    }

    public function test_upvoting_thread_rewards_both_liker_and_author()
    {
        $author = User::factory()->create(['role' => 'siswa']);
        $thread = ForumThread::create([
            'user_id' => $author->id,
            'title' => 'Tips Pintar Belajar',
            'content' => 'Ini tips-tips untuk konsentrasi belajar.',
            'category' => 'diskusi',
        ]);

        $liker = User::factory()->create(['role' => 'siswa']);
        $likerStart = $liker->reputation->total_points ?? 0;
        $authorStart = $author->reputation->total_points ?? 0;

        $response = $this->actingAs($liker)
            ->post(route('forum.like', $thread));

        $response->assertJson([
            'success' => true,
            'liked' => true,
            'likes_count' => 1,
        ]);

        $liker->refresh();
        $author->refresh();

        // Liker gets +2 points, Author gets +10 points
        $this->assertEquals($likerStart + 2, $liker->reputation->total_points);
        $this->assertEquals($authorStart + 10, $author->reputation->total_points);

        // Toggle like off (Unlike)
        $this->actingAs($liker)->post(route('forum.like', $thread));
        
        $liker->refresh();
        $author->refresh();

        // Points reversed back to start
        $this->assertEquals($likerStart, $liker->reputation->total_points);
        $this->assertEquals($authorStart, $author->reputation->total_points);
    }

    public function test_author_can_select_best_answer_and_rewards_commenter()
    {
        $author = User::factory()->create(['role' => 'siswa']);
        $thread = ForumThread::create([
            'user_id' => $author->id,
            'title' => 'Pertanyaan CSS',
            'content' => 'Bagaimana menengahkan div?',
            'category' => 'diskusi',
        ]);

        $replier = User::factory()->create(['role' => 'siswa']);
        $reply = ForumReply::create([
            'forum_thread_id' => $thread->id,
            'user_id' => $replier->id,
            'content' => 'Gunakan display: flex dan justify-content: center.',
        ]);

        $replierStart = $replier->reputation->total_points ?? 0;

        $response = $this->actingAs($author)
            ->post(route('forum.reply.accept', $reply));

        $reply->refresh();
        $replier->refresh();

        $this->assertTrue($reply->is_accepted);
        // Accepting best answer gives +15 points to comment author
        $this->assertEquals($replierStart + 15, $replier->reputation->total_points);
    }

    public function test_project_collaboration_joining_approving_and_completion()
    {
        $creator = User::factory()->create(['role' => 'siswa']);
        $thread = ForumThread::create([
            'user_id' => $creator->id,
            'title' => 'Proyek Robotika SMK',
            'content' => 'Ayo buat robot pembersih debu pintar.',
            'category' => 'project_idea',
            'status' => 'seeking_members',
        ]);

        $applicant = User::factory()->create(['role' => 'siswa']);
        $appStart = $applicant->reputation->total_points ?? 0;
        $creatorStart = $creator->reputation->total_points ?? 0;

        // 1. Applicant registers/joins (gets +10 points)
        $this->actingAs($applicant)
            ->post(route('forum.join', $thread), ['notes' => 'Saya tertarik memprogram sensor ultrasonik.']);

        $this->assertDatabaseHas('forum_members', [
            'forum_thread_id' => $thread->id,
            'user_id' => $applicant->id,
            'status' => 'pending',
        ]);

        $applicant->refresh();
        $this->assertEquals($appStart + 10, $applicant->reputation->total_points);

        // 2. Creator approves applicant (creator gets +5 points)
        $member = ForumMember::where('forum_thread_id', $thread->id)->where('user_id', $applicant->id)->first();
        $this->actingAs($creator)
            ->post(route('forum.member.approve', $member));

        $member->refresh();
        $creator->refresh();
        $this->assertEquals('approved', $member->status);
        $this->assertEquals($creatorStart + 5, $creator->reputation->total_points);

        // 3. Creator completes the project (both get +50 points)
        $creatorPreCompletion = $creator->reputation->total_points;
        $appPreCompletion = $applicant->reputation->total_points;

        $this->actingAs($creator)
            ->post(route('forum.status.update', $thread), ['status' => 'completed']);

        $thread->refresh();
        $creator->refresh();
        $applicant->refresh();

        $this->assertEquals('completed', $thread->status);
        $this->assertEquals($creatorPreCompletion + 50, $creator->reputation->total_points);
        $this->assertEquals($appPreCompletion + 50, $applicant->reputation->total_points);
    }
}
