<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoUploadController extends Controller
{
    public function uploadVideo(Request $request)
    {  $i = 0;
        info(++$i);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads', $fileName, 'public');

            return response()->json(['message' => 'File uploaded successfully!']);
        }

        return response()->json(['message' => 'Invalid file upload.'], 400);
    }
}