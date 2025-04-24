<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    // Temporary directory for storing uploads
    private const TEMP_DIR = 'temp_uploads';

    public function process(Request $request)
    {
        if ($request->hasFile('filepond')) {
            try {
                $file = $request->file('filepond');
                
                // Generate a unique filename
                $uniqueId = time() . '_' . Str::random(10);
                $extension = $file->getClientOriginalExtension();
                $filename = $uniqueId . '.' . $extension;
                
                // Store the file in a temporary directory
                $path = $file->storeAs(self::TEMP_DIR, $filename, 'public');
                
                \Log::info('Image stored in filesystem: ' . $path);
                
                // Return the unique ID as the server ID
                return response()->json($uniqueId, 200);
            } catch (\Exception $e) {
                \Log::error('Error processing file upload: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                return response()->json('Error processing file: ' . $e->getMessage(), 500);
            }
        }
        
        return response()->json('No file provided', 400);
    }
    
    public function revert(Request $request)
    {
        try {
            $uniqueId = $request->getContent();
            
            // Find and delete the file
            $files = Storage::disk('public')->files(self::TEMP_DIR);
            foreach ($files as $file) {
                if (strpos($file, $uniqueId) !== false) {
                    Storage::disk('public')->delete($file);
                    \Log::info('File removed from storage: ' . $file);
                    break;
                }
            }
            
            return response()->json('File deleted', 200);
        } catch (\Exception $e) {
            \Log::error('Error deleting temporary file: ' . $e->getMessage());
            return response()->json('Error deleting file: ' . $e->getMessage(), 500);
        }
    }
    
    public function load(Request $request, $uniqueId)
    {
        try {
            \Log::info('Attempting to load file with ID: ' . $uniqueId);
            
            // Find the file with the uniqueId prefix
            $files = Storage::disk('public')->files(self::TEMP_DIR);
            $filePath = null;
            
            foreach ($files as $file) {
                if (strpos($file, $uniqueId) !== false) {
                    $filePath = $file;
                    break;
                }
            }
            
            if ($filePath) {
                $fileContents = Storage::disk('public')->get($filePath);
                $mimeType = Storage::disk('public')->mimeType($filePath);
                
                // Convert to base64 for FilePond display
                $base64 = base64_encode($fileContents);
                $base64String = 'data:' . $mimeType . ';base64,' . $base64;
                
                return response($base64String)->header('Content-Type', 'text/plain');
            }
            
            \Log::warning('File not found in storage: ' . $uniqueId);
            return response()->json('File not found', 404);
        } catch (\Exception $e) {
            \Log::error('Error loading file: ' . $e->getMessage());
            return response()->json('Error loading file: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get the file URL or base64 string for permanent storage
     */
    public function getBase64FromSession($uniqueId)
    {
        \Log::info('Attempting to get file with ID for permanent storage: ' . $uniqueId);
        
        try {
            // Find the file with the uniqueId prefix
            $files = Storage::disk('public')->files(self::TEMP_DIR);
            $filePath = null;
            
            \Log::info('Searching through files in temp dir: ' . json_encode($files));
            
            foreach ($files as $file) {
                if (strpos($file, $uniqueId) !== false) {
                    $filePath = $file;
                    \Log::info('Found matching file: ' . $filePath);
                    break;
                }
            }
            
            if (!$filePath) {
                \Log::error('Image file not found for ID: ' . $uniqueId);
                throw new \Exception("Temporary image data not found for ID: {$uniqueId}");
            }
            
            // Instead of using base64 which might be too large, store as a URL
            $publicUrl = 'storage/' . $filePath;
            \Log::info('Generated public URL for profile image: ' . $publicUrl);
            
            // Do NOT delete the temp file yet - we'll copy it to a permanent location
            $permanentDir = 'uploads/profiles';
            $newFilename = 'profile_' . $uniqueId . '_' . basename($filePath);
            
            // Ensure the permanent directory exists
            if (!Storage::disk('public')->exists($permanentDir)) {
                Storage::disk('public')->makeDirectory($permanentDir);
                \Log::info('Created permanent directory: ' . $permanentDir);
            }
            
            // Copy to permanent location
            $fileContents = Storage::disk('public')->get($filePath);
            Storage::disk('public')->put($permanentDir . '/' . $newFilename, $fileContents);
            \Log::info('Copied file to permanent location: ' . $permanentDir . '/' . $newFilename);
            
            // Now delete the temp file
            Storage::disk('public')->delete($filePath);
            \Log::info('Deleted temporary file: ' . $filePath);
            
            // Return the permanent URL
            return asset('storage/' . $permanentDir . '/' . $newFilename);
        } catch (\Exception $e) {
            \Log::error('Error in getBase64FromSession: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            throw $e;
        }
    }
}
