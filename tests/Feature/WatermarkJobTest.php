<?php

namespace Tests\Feature;

use App\Jobs\ProcessWatermarkPdf;
use App\Models\User;
use App\Models\WatermarkJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WatermarkJobTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    public function test_upload_rejects_non_pdf_files(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('document.txt', 100);

        $response = $this->post(route('jobs.store'), [
            'pdf_file' => $file,
            'watermark_type' => 'text',
            'watermark_text' => 'CONFIDENTIAL',
            'position' => 'diagonal',
            'opacity' => 50,
            'font_size' => 48,
            'color' => '#888888',
            'rotation' => -45,
        ]);

        $response->assertSessionHasErrors('pdf_file');
        $this->assertDatabaseCount('watermark_jobs', 0);
    }

    public function test_upload_rejects_files_exceeding_size_limit(): void
    {
        $this->actingAs($this->user);

        // Create a file that exceeds the limit (default 50MB)
        $maxMb = config('watermark.max_upload_mb', 50);
        $file = UploadedFile::fake()->create('large.pdf', ($maxMb + 1) * 1024);

        $response = $this->post(route('jobs.store'), [
            'pdf_file' => $file,
            'watermark_type' => 'text',
            'watermark_text' => 'CONFIDENTIAL',
            'position' => 'diagonal',
            'opacity' => 50,
            'font_size' => 48,
            'color' => '#888888',
            'rotation' => -45,
        ]);

        $response->assertSessionHasErrors('pdf_file');
        $this->assertDatabaseCount('watermark_jobs', 0);
    }

    public function test_upload_creates_job_and_dispatches_queue(): void
    {
        Queue::fake();

        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->post(route('jobs.store'), [
            'pdf_file' => $file,
            'watermark_type' => 'text',
            'watermark_text' => 'CONFIDENTIAL',
            'position' => 'diagonal',
            'opacity' => 50,
            'font_size' => 48,
            'color' => '#888888',
            'rotation' => -45,
        ]);

        $response->assertRedirect();

        // Assert job was created in database
        $this->assertDatabaseCount('watermark_jobs', 1);

        $job = WatermarkJob::first();
        $this->assertEquals($this->user->id, $job->user_id);
        $this->assertEquals('document.pdf', $job->original_filename);
        $this->assertEquals('pending', $job->status);
        $this->assertEquals('text', $job->settings['type']);
        $this->assertEquals('CONFIDENTIAL', $job->settings['text']);
        $this->assertEquals('diagonal', $job->settings['position']);
        $this->assertEquals(50, $job->settings['opacity']);

        // Assert queue job was dispatched
        Queue::assertPushed(ProcessWatermarkPdf::class, function ($queueJob) use ($job) {
            return $queueJob->watermarkJob->id === $job->id;
        });
    }

    public function test_upload_validates_watermark_settings(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        // Test invalid opacity
        $response = $this->post(route('jobs.store'), [
            'pdf_file' => $file,
            'watermark_type' => 'text',
            'watermark_text' => 'CONFIDENTIAL',
            'position' => 'diagonal',
            'opacity' => 150, // Invalid: exceeds 100
            'font_size' => 48,
            'color' => '#888888',
            'rotation' => -45,
        ]);

        $response->assertSessionHasErrors('opacity');

        // Test invalid color format
        $response = $this->post(route('jobs.store'), [
            'pdf_file' => $file,
            'watermark_type' => 'text',
            'watermark_text' => 'CONFIDENTIAL',
            'position' => 'diagonal',
            'opacity' => 50,
            'font_size' => 48,
            'color' => 'red', // Invalid: not hex format
            'rotation' => -45,
        ]);

        $response->assertSessionHasErrors('color');
    }

    public function test_download_denied_for_other_users(): void
    {
        // Create a job for user
        $job = WatermarkJob::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'done',
            'output_path' => 'private/watermark/outputs/test.pdf',
        ]);

        // Store a fake output file
        Storage::put($job->output_path, 'fake pdf content');

        // Try to download as another user
        $this->actingAs($this->otherUser);

        $response = $this->get(route('jobs.download', $job));

        $response->assertForbidden();
    }

    public function test_owner_can_download_completed_job(): void
    {
        // Create a completed job for user
        $job = WatermarkJob::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'done',
            'original_filename' => 'test.pdf',
            'output_path' => 'private/watermark/outputs/output.pdf',
        ]);

        // Store a fake output file
        Storage::put($job->output_path, 'fake pdf content');

        $this->actingAs($this->user);

        $response = $this->get(route('jobs.download', $job));

        $response->assertOk();
        $response->assertDownload('test-watermarked.pdf');
    }

    public function test_download_denied_for_pending_job(): void
    {
        // Create a pending job
        $job = WatermarkJob::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('jobs.download', $job));

        $response->assertNotFound();
    }

    public function test_job_status_endpoint_returns_correct_data(): void
    {
        $job = WatermarkJob::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'processing',
        ]);

        $this->actingAs($this->user);

        $response = $this->getJson(route('jobs.status', $job));

        $response->assertOk()
            ->assertJson([
                'id' => $job->id,
                'status' => 'processing',
                'can_download' => false,
            ]);
    }

    public function test_job_status_denied_for_other_users(): void
    {
        $job = WatermarkJob::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->otherUser);

        $response = $this->getJson(route('jobs.status', $job));

        $response->assertForbidden();
    }

    public function test_user_can_delete_own_job(): void
    {
        $job = WatermarkJob::factory()->create([
            'user_id' => $this->user->id,
            'original_path' => 'private/watermark/uploads/test.pdf',
            'output_path' => 'private/watermark/outputs/test.pdf',
        ]);

        Storage::put($job->original_path, 'content');
        Storage::put($job->output_path, 'content');

        $this->actingAs($this->user);

        $response = $this->delete(route('jobs.destroy', $job));

        $response->assertRedirect(route('jobs.index'));
        $this->assertDatabaseMissing('watermark_jobs', ['id' => $job->id]);
        Storage::assertMissing($job->original_path);
        Storage::assertMissing($job->output_path);
    }

    public function test_user_cannot_delete_others_job(): void
    {
        $job = WatermarkJob::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->otherUser);

        $response = $this->delete(route('jobs.destroy', $job));

        $response->assertForbidden();
        $this->assertDatabaseHas('watermark_jobs', ['id' => $job->id]);
    }

    public function test_guests_cannot_access_job_routes(): void
    {
        $job = WatermarkJob::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->get(route('jobs.index'))->assertRedirect(route('login'));
        $this->get(route('jobs.show', $job))->assertRedirect(route('login'));
        $this->post(route('jobs.store'))->assertRedirect(route('login'));
        $this->delete(route('jobs.destroy', $job))->assertRedirect(route('login'));
    }
}
