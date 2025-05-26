<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Conference::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'year' => 'required|integer',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'heroImage' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'files' => 'nullable|array',
            'speakers' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['hero_image'] = $data['heroImage'] ?? null;
        unset($data['heroImage']);

        $conference = Conference::create($data);
        return response()->json($conference, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $conference = Conference::find($id);
        if (!$conference) {
            return response()->json(['message' => 'Conference not found'], 404);
        }
        return $conference;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $conference = Conference::find($id);
        if (!$conference) {
            return response()->json(['message' => 'Conference not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'year' => 'required|integer',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'heroImage' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'files' => 'nullable|array',
            'speakers' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['hero_image'] = $data['heroImage'] ?? null;
        unset($data['heroImage']);

        $conference->update($data);
        return response()->json($conference);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $conference = Conference::find($id);
        if (!$conference) {
            return response()->json(['message' => 'Conference not found'], 404);
        }

        $conference->delete();
        return response()->json(null, 204);
    }

    public function getEditors($id)
    {
        $conference = \App\Models\Conference::findOrFail($id);
        return response()->json([
            'editors' => $conference->editors()->select('id', 'name', 'email')->get()
        ]);
    }

    public function assignEditor(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $conference = Conference::findOrFail($id);
        $user = User::findOrFail($request->user_id);

        $user->managedConferences()->syncWithoutDetaching([
            $conference->id => [
                'granted_at' => now(),
                'granted_by_user_id' => auth()->id(),
            ]
        ]);

        return response()->json(['message' => 'Editor assigned successfully']);
    }

    public function removeEditor(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $conference = Conference::findOrFail($id);
        $user = User::findOrFail($request->user_id);

        $user->managedConferences()->detach($conference->id);

        return response()->json(['message' => 'Editor removed']);
    }
}
