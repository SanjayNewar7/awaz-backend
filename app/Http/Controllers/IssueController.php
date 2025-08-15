<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class IssueController extends Controller
{
    /**
     * Save base64 image to storage (copied from AuthController for simplicity)
     */
    private function saveBase64Image($base64Image, $pathPrefix)
    {
        try {
            if (empty($base64Image)) {
                throw new \Exception('Empty image data');
            }

            if (strpos($base64Image, ';base64,') !== false) {
                list(, $base64Image) = explode(';', $base64Image);
                list(, $base64Image) = explode(',', $base64Image);
            }

            $imageData = base64_decode($base64Image);
            if ($imageData === false) {
                throw new \Exception('Invalid base64 image data');
            }

            if (@imagecreatefromstring($imageData) === false) {
                throw new \Exception('Invalid image format');
            }

            $filename = $pathPrefix . uniqid() . '.jpg';
            $storagePath = 'public/images/' . $filename;

            if (!Storage::put($storagePath, $imageData)) {
                throw new \Exception('Failed to save image to storage');
            }

            return 'images/' . $filename;
        } catch (\Exception $e) {
            Log::error('Image processing error: ' . $e->getMessage());
            throw $e;
        }
    }
// In IssueController.php
public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'heading' => 'required|string|max:255',
            'description' => 'required|string',
            'report_type' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'required|string|max:50',
            'area_name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'photo1' => 'nullable|string',
            'photo2' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Save images to storage if provided
        $photo1Path = null;
        $photo2Path = null;

        if ($request->photo1) {
            $photo1Path = $this->saveBase64Image($request->photo1, 'issues/photo1_');
        }

        if ($request->photo2) {
            $photo2Path = $this->saveBase64Image($request->photo2, 'issues/photo2_');
        }

        $issue = Issue::create([
            'user_id' => auth()->id(),
            'heading' => $request->heading,
            'description' => $request->description,
            'report_type' => $request->report_type,
            'district' => $request->district,
            'ward' => $request->ward,
            'area_name' => $request->area_name,
            'location' => $request->location,
            'photo1' => $photo1Path,
            'photo2' => $photo2Path,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Issue created successfully',
            'issue' => $issue
        ], 201);
    } catch (\Exception $e) {
        Log::error('Issue creation error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Issue creation failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
