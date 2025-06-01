<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Subpage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SubpageController extends Controller
{
    public function index($conferenceId)
    {
        try {
            $conference = Conference::find($conferenceId);
            if (!$conference) {
                return response()->json(['message' => 'Conference not found'], 404);
            }

            return response()->json($conference->subpages);
        } catch (\Exception $e) {
            Log::error('Error in SubpageController@index: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    public function store(Request $request, $conferenceId)
    {
        try {
            $conference = Conference::find($conferenceId);
            if (!$conference) {
                return response()->json(['message' => 'Conference not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'order' => 'required|integer|min:0',
                'is_published' => 'required|boolean',
                'slug' => 'required|string|max:255|unique:subpages,slug'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();
            $data['conference_id'] = $conferenceId;
            
            $subpage = Subpage::create($data);
            return response()->json($subpage, 201);
        } catch (\Exception $e) {
            Log::error('Error in SubpageController@store: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    public function show($conferenceId, $subpageId)
    {
        try {
            $conference = Conference::find($conferenceId);
            if (!$conference) {
                return response()->json(['message' => 'Conference not found'], 404);
            }

            $subpage = $conference->subpages()
                ->where(function($query) use ($subpageId) {
                    $query->where('id', $subpageId)
                        ->orWhere('slug', $subpageId);
                })
                ->first();

            if (!$subpage) {
                return response()->json(['message' => 'Subpage not found'], 404);
            }

            return response()->json($subpage);
        } catch (\Exception $e) {
            Log::error('Error in SubpageController@show: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    public function update(Request $request, $conferenceId, $subpageId)
    {
        try {
            $conference = Conference::find($conferenceId);
            if (!$conference) {
                return response()->json(['message' => 'Conference not found'], 404);
            }

            $subpage = $conference->subpages()
                ->where(function($query) use ($subpageId) {
                    $query->where('id', $subpageId)
                        ->orWhere('slug', $subpageId);
                })
                ->first();

            if (!$subpage) {
                return response()->json(['message' => 'Subpage not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'order' => 'required|integer|min:0',
                'is_published' => 'required|boolean',
                'slug' => 'required|string|max:255|unique:subpages,slug,' . $subpage->id
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $subpage->update($request->all());
            return response()->json($subpage);
        } catch (\Exception $e) {
            Log::error('Error in SubpageController@update: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    public function destroy($conferenceId, $subpageId)
    {
        try {
            $conference = Conference::find($conferenceId);
            if (!$conference) {
                return response()->json(['message' => 'Conference not found'], 404);
            }

            $subpage = $conference->subpages()
                ->where(function($query) use ($subpageId) {
                    $query->where('id', $subpageId)
                        ->orWhere('slug', $subpageId);
                })
                ->first();

            if (!$subpage) {
                return response()->json(['message' => 'Subpage not found'], 404);
            }

            $subpage->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error in SubpageController@destroy: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
} 