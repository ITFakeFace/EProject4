<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class ImageController extends Controller
{
  public function upload(Request $request)
  {
    if ($request->hasFile('image')) {
      $image = $request->file('image');
      $path = $image->store('images/upload', 'public');
      return response()->json(['path' => $path], 200);
    }
    return response()->json(['error' => 'No image uploaded'], 400);
  }

  public function getImage($path)
  {
    // Construct the full path within the public directory
    $fullPath = public_path($path);

    if (!File::exists($fullPath)) {
      abort(404);
    }

    $file = File::get($fullPath);
    $type = File::mimeType($fullPath);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
  }
}
