<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    public function process(Request $request)
    {
        if ($request->hasFile('filepond')) {
            try {
                $file = $request->file('filepond');
                
                // Convert the file to base64
                $base64 = base64_encode(file_get_contents($file->getRealPath()));
                $mime = $file->getMimeType();
                $base64String = 'data:' . $mime . ';base64,' . $base64;
                
                // For tracking purposes, generate a unique ID for this upload
                $uniqueId = time() . '_' . Str::random(10);
                
                // Store the base64 string in the session temporarily
                session([$uniqueId => $base64String]);
                
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
            
            // Remove the base64 data from the session
            if (session()->has($uniqueId)) {
                session()->forget($uniqueId);
            }
            
            return response()->json('File deleted', 200);
        } catch (\Exception $e) {
            \Log::error('Error deleting temporary file: ' . $e->getMessage());
            return response()->json('Error deleting file: ' . $e->getMessage(), 500);
        }
    }
    
    public function load(Request $request, $uniqueId)
    {
        if (session()->has($uniqueId)) {
            $base64String = session($uniqueId);
            return response($base64String)->header('Content-Type', 'text/plain');
        }
        
        return response()->json('File not found', 404);
    }
    
    /**
     * Get the base64 string from session storage
     */
    public function getBase64FromSession($uniqueId)
    {
        if (!session()->has($uniqueId)) {
            throw new \Exception("Temporary image data not found for ID: {$uniqueId}");
        }
        
        return session($uniqueId);
    }
}
