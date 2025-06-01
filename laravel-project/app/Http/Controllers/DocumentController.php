<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function index()
    {
        return Document::all();
    }

    public function store(Request $request)
    {
        Log::info('Document upload request received', [
            'has_file' => $request->hasFile('file'),
            'all_data' => $request->all(),
            'files' => $request->files->all()
        ]);

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            Log::error('Document validation failed', [
                'errors' => $validator->errors()
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        Log::info('File details', [
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize()
        ]);

        $fileName = time() . '_' . $file->getClientOriginalName();
        $uploadPath = public_path('documents');

        if (!file_exists($uploadPath)) {
            Log::info('Creating upload directory', ['path' => $uploadPath]);
            mkdir($uploadPath, 0755, true);
        }

        try {
            $file->move($uploadPath, $fileName);
            Log::info('File moved successfully', [
                'path' => $uploadPath . '/' . $fileName
            ]);

            $document = Document::create([
                'name' => $request->input('name'),
                'url' => '/documents/' . $fileName
            ]);

            Log::info('Document created in database', [
                'document' => $document
            ]);

            return response()->json($document, 201);
        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'File upload failed'], 500);
        }
    }

    public function destroy(string $id)
    {
        $document = Document::find($id);
        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        $filePath = public_path($document->url);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $document->delete();
        return response()->json(null, 204);
    }
}
