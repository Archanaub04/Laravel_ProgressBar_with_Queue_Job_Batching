<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FileUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessFileUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileName;
    protected $totalChunks;

    public function __construct($fileName, $totalChunks)
    {
        $this->fileName = $fileName;
        $this->totalChunks = $totalChunks;
    }

    public function handle()
    { 
        try {
            $directory = public_path('uploads');

            // Ensure the uploads directory exists
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $finalPath = $directory . '/' . $this->fileName;
            $outputFile = fopen($finalPath, 'wb');

            if (!$outputFile) {
                Log::error("Failed to open file for writing: {$finalPath}");
                return;
            }

            for ($i = 0; $i < $this->totalChunks; $i++) {
                $tempPath = public_path("uploads/temp_{$this->fileName}_part{$i}");

                if (!file_exists($tempPath)) {
                    Log::error("Chunk file missing: {$tempPath}");
                    continue; // Skip this part if it's missing
                }

                fwrite($outputFile, file_get_contents($tempPath));
                unlink($tempPath); // Delete chunk after merging
            }

            // Get the MIME type
            if (extension_loaded('fileinfo')) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($finalPath);
            } else {
                $mimeType = mime_content_type($finalPath) ?: 'application/octet-stream';
            }

            // Save file record in DB
            FileUpload::create([
                'name' => $this->fileName,
                'path' => "/uploads/{$this->fileName}",
                'type' => $mimeType,
            ]);

            fclose($outputFile);

            // Log::info("File uploaded successfully: {$finalPath}");
        } catch (\Exception $e) {
            Log::error("File processing failed: " . $e->getMessage());
        }
    }
}
