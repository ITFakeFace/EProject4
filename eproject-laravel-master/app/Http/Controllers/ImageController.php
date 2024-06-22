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

  public function getImage($filename)
  {
    echo "Get File: $filename";
    $filename = "\images\user\avatar\21062024\c0ad41f3-5bda-46f7-a0da-e37dbec3883e.jpeg";
    $path = storage_path('app/public' . $filename);

    echo $path;
    if (!File::exists($path)) {
      abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
  }
}
