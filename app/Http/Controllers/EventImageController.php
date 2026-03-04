<?php

namespace App\Http\Controllers;
use App\Models\EventImage;
use Illuminate\Support\Facades\Storage;


use Illuminate\Http\Request;

class EventImageController extends Controller
{
  public function destroy(EventImage $image)
{
    Storage::disk('public')->delete($image->image_path);
    $image->delete();

    return back()->with('success', 'Image deleted.');
}
}
