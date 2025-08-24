<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait ImageDuplicator
{
    protected static function bootImageDuplicator()
    {
        static::saved(function ($model) {
            // Use model-specific image fields if defined, else default
            $imageFields = property_exists($model, 'imageFields') ? static::$imageFields : [
                'photo1', 'photo2', 'image1', 'image2', 'citizenship_front_image', 'citizenship_back_image', 'profile_image'
            ];

            foreach ($imageFields as $field) {
                if ($model->wasChanged($field) && $model->$field) {
                    $wrongPath = $model->$field; // e.g., 'public/images/comments/filename.jpg'
                    // Remove 'public/' prefix and keep subfolder (e.g., 'images/comments/filename.jpg')
                    $correctPath = preg_replace('#^public/(images/.*)$#', '$1', $wrongPath);

                    // Skip if already in correct path
                    if ($wrongPath === $correctPath) {
                        continue;
                    }

                    // Check if file exists in wrong path
                    if (Storage::disk('public')->exists($wrongPath)) {
                        try {
                            // Ensure the destination directory exists
                            $destinationDir = dirname($correctPath);
                            if (!Storage::disk('public')->exists($destinationDir)) {
                                Storage::disk('public')->makeDirectory($destinationDir);
                            }

                            // Copy to correct path (duplication)
                            Storage::disk('public')->copy($wrongPath, $correctPath);

                            // Update the model's field to use the correct path
                            $model->forceFill([$field => $correctPath]);
                            $model->saveQuietly(); // Save without triggering events again

                            Log::info("Duplicated image from {$wrongPath} to {$correctPath} for model " . get_class($model) . " ID: {$model->getKey()}");
                        } catch (\Exception $e) {
                            Log::error("Failed to duplicate image for {$field} in " . get_class($model) . " ID: {$model->getKey()} - Error: " . $e->getMessage());
                        }
                    } else {
                        Log::warning("Image not found at {$wrongPath} for {$field} in " . get_class($model) . " ID: {$model->getKey()}");
                    }
                }
            }
        });
    }
}

