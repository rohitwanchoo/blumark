<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WatermarkJob;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WatermarkJob>
 */
class WatermarkJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['text', 'image']);

        $settings = [
            'type' => $type,
            'position' => fake()->randomElement(['center', 'diagonal', 'tiled']),
            'opacity' => fake()->numberBetween(10, 100),
            'rotation' => fake()->numberBetween(-180, 180),
        ];

        if ($type === 'text') {
            $settings['text'] = fake()->randomElement(['CONFIDENTIAL', 'DRAFT', 'SAMPLE', 'DO NOT COPY']);
            $settings['font_size'] = fake()->numberBetween(24, 72);
            $settings['color'] = '#' . fake()->hexColor();
        } else {
            $settings['scale'] = fake()->numberBetween(20, 100);
        }

        return [
            'user_id' => User::factory(),
            'original_filename' => fake()->word() . '.pdf',
            'original_path' => 'private/watermark/uploads/' . Str::uuid() . '.pdf',
            'output_path' => null,
            'watermark_image_path' => $type === 'image' ? 'private/watermark/images/' . Str::uuid() . '.png' : null,
            'status' => WatermarkJob::STATUS_PENDING,
            'error_message' => null,
            'settings' => $settings,
            'page_count' => null,
            'file_size' => fake()->numberBetween(10000, 10000000),
            'processed_at' => null,
        ];
    }

    /**
     * Indicate that the job is processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WatermarkJob::STATUS_PROCESSING,
        ]);
    }

    /**
     * Indicate that the job is done.
     */
    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WatermarkJob::STATUS_DONE,
            'output_path' => 'private/watermark/outputs/' . Str::uuid() . '.pdf',
            'page_count' => fake()->numberBetween(1, 50),
            'processed_at' => now(),
        ]);
    }

    /**
     * Indicate that the job has failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WatermarkJob::STATUS_FAILED,
            'error_message' => fake()->sentence(),
            'processed_at' => now(),
        ]);
    }

    /**
     * Indicate that the job uses text watermark.
     */
    public function textWatermark(): static
    {
        return $this->state(function (array $attributes) {
            $settings = $attributes['settings'];
            $settings['type'] = 'text';
            $settings['text'] = fake()->randomElement(['CONFIDENTIAL', 'DRAFT', 'SAMPLE']);
            $settings['font_size'] = fake()->numberBetween(24, 72);
            $settings['color'] = '#' . fake()->hexColor();

            return [
                'settings' => $settings,
                'watermark_image_path' => null,
            ];
        });
    }

    /**
     * Indicate that the job uses image watermark.
     */
    public function imageWatermark(): static
    {
        return $this->state(function (array $attributes) {
            $settings = $attributes['settings'];
            $settings['type'] = 'image';
            $settings['scale'] = fake()->numberBetween(20, 100);
            unset($settings['text'], $settings['font_size'], $settings['color']);

            return [
                'settings' => $settings,
                'watermark_image_path' => 'private/watermark/images/' . Str::uuid() . '.png',
            ];
        });
    }
}
