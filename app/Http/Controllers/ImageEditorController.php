<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageEditorController extends Controller
{
    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'image'         => 'required|file|mimes:webp,jpeg,png,gif|max:20480',
            'original_path' => 'nullable|string|max:500',
        ]);

        try {
            $file         = $request->file('image');
            $originalPath = $request->input('original_path', '');

            // Determine target directory from original path
            $directory = 'edited';
            if ($originalPath) {
                $parts     = explode('/', $originalPath);
                $directory = count($parts) > 1 ? $parts[0] : 'edited';
            }

            // Generate new filename (keep same name if original path given)
            if ($originalPath && Storage::disk('public')->exists($originalPath)) {
                $savePath = $originalPath;
            } else {
                $filename = Str::ulid() . '.webp';
                $savePath = $directory . '/' . $filename;
            }

            // Save (overwrites if same path)
            Storage::disk('public')->put($savePath, file_get_contents($file->getRealPath()));

            return response()->json([
                'success' => true,
                'path'    => $savePath,
                'url'     => Storage::disk('public')->url($savePath),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
