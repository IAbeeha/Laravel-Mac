<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    //
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('image');
        $imageName = time().'.'.$image->extension();
        $image->move(public_path('uploads'), $imageName);

        $imageUrl = asset('uploads/' . $imageName);
        error_log("done");
        return response()->json(['image_url' => $imageUrl]);
    }
}
