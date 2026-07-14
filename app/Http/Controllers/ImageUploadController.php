<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    /**
     * Upload an image file and return its public URL.
     * Used by rich textareas and cell-level image uploads.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5 MB max
        ], [
            'image.required' => 'Aucune image fournie.',
            'image.image'    => 'Le fichier doit être une image.',
            'image.mimes'    => 'Formats acceptés : JPEG, PNG, GIF, WebP.',
            'image.max'      => 'L\'image ne doit pas dépasser 5 Mo.',
        ]);

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $filename = 'attachment_' . now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $extension;

        // Store in storage/app/public/attachments/
        $path = $file->storeAs('attachments', $filename, 'public');

        return response()->json([
            'url'  => Storage::url($path),
            'name' => $filename,
        ]);
    }
}
