<?php

namespace Tests\Feature;

use App\Ai\Agents\NoteSummarizer;
use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_note_can_be_summarized(): void
    {
        $user = User::factory()->create();

        $note = Note::factory()->create([
            'user_id' => $user->id,
            'body' => 'This is a long body of text that needs summarizing.',
        ]);

        NoteSummarizer::fake([
            'This is a short summary.',
        ]);

        $note->generateSummary(app(NoteSummarizer::class));

        $note->refresh();

        $this->assertEquals('This is a short summary.', $note->tldr);
        $this->assertEquals(
            'This is a long body of text that needs summarizing.',
            $note->body
        );

        NoteSummarizer::assertPrompted(
            'This is a long body of text that needs summarizing.'
        );
    }

    public function test_user_cannot_summarize_someone_elses_note(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $noteByB = Note::factory()->create([
            'user_id' => $userB->id,
        ]);

        $this->actingAs($userA)
            ->post(route('notes.summarize', $noteByB))
            ->assertForbidden();
    }

    public function test_existing_summary_prevents_ai_call(): void
    {
        $note = Note::factory()->create([
            'body' => 'Some content',
            'tldr' => 'Existing summary',
        ]);

        NoteSummarizer::fake();

        $note->generateSummary(app(NoteSummarizer::class));

        NoteSummarizer::assertNotPrompted('Some content');
    }

    public function test_handling_ai_service_failure(): void
    {
        $user = User::factory()->create();

        $note = Note::factory()->create([
            'user_id' => $user->id,
            'body' => 'Some content to summarize',
        ]);

        $this->mock(NoteSummarizer::class, function ($mock) {
            $mock->shouldReceive('prompt')
                ->withAnyArgs()
                ->andThrow(new \Exception('API Link Down'));
        });

        $response = $this->actingAs($user)
            ->post(route('notes.summarize', $note));

        $response->assertUnprocessable();
        $response->assertJsonFragment([
            'message' => 'API Link Down',
        ]);
    }
}
