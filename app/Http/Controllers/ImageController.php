<?php
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
 
class ImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
 
        $path = $request->file('image')->store('images', 'public');
        if ($path === false) {
            return response()->json(['message' => 'Upload failed'], 500);
        }
        return response()->json([
            'message' => 'Uploaded successfully',
            'path' => $path,
            'url' => asset('storage/' . $path),
        ], 201);
    }
 
    public function destroy(string $filename)
    {
        $path = 'images/' . $filename;
        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'Not found'], 404);
        }
        Storage::disk('public')->delete($path);
        return response()->json(['message' => 'Deleted']);
    }
}

