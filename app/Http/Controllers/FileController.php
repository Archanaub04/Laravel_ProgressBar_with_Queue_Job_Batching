<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Jobs\ProcessFileUpload;
use Illuminate\Bus\Batch;
use Throwable;

class FileController extends Controller
{
    public function uploadChunk(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'fileName' => 'required|string',
            'chunkIndex' => 'required|integer',
            'totalChunks' => 'required|integer',
        ]);

        $file = $request->file('file');
        $fileName = $request->input('fileName');
        $chunkIndex = $request->input('chunkIndex');

        // Define upload directory
        $uploadDir = public_path('uploads');

        // Ensure the upload directory exists
        if (!File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0777, true, true);
        }

        // Create temp file path for chunk storage
        $tempPath = $uploadDir . '/temp_' . $fileName . '_part' . $chunkIndex;
        file_put_contents($tempPath, file_get_contents($file));

        // If last chunk, queue job for final processing
        if ($chunkIndex == ($request->input('totalChunks') - 1)) {
            ProcessFileUpload::dispatch($fileName, $request->input('totalChunks'));
        }

        return response()->json(['message' => 'Chunk uploaded successfully!']);
    }
}
