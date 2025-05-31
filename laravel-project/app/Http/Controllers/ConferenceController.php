<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConferenceController extends Controller
{

    public function index()
    {
        return Conference::all();
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'year' => 'required|integer',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'hero_image' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'files' => 'nullable|array',
            'speakers' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $conference = Conference::create($data);
        return response()->json($conference, 201);
    }


    public function show(string $id)
    {
        $conference = Conference::find($id);
        if (!$conference) {
            return response()->json(['message' => 'Conference not found'], 404);
        }
        return $conference;
    }


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
            'hero_image' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'files' => 'nullable|array',
            'speakers' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $conference->update($data);
        return response()->json($conference);
    }


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

    public function uploadHeroImage(Request $request, $id)
    {
        $conference = Conference::findOrFail($id);

        $request->validate([
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($conference->hero_image) {
            $imagePath = public_path($conference->hero_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($request->hasFile('hero_image')) {
            $image = $request->file('hero_image');
            $imageName = time() . '_' . $image->getClientOriginalName();

            $uploadPath = public_path('images/conferences');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $image->move($uploadPath, $imageName);

            $conference->hero_image = 'images/conferences/' . $imageName;
            $conference->save();

            return response()->json([
                'message' => 'Hero image updated successfully',
                'hero_image' => $conference->hero_image
            ]);
        }

        return response()->json([
            'message' => 'No image uploaded',
            'hero_image' => $conference->hero_image
        ]);
    }

    public function removeHeroImage($id)
    {
        $conference = Conference::findOrFail($id);

        if ($conference->hero_image) {
            $imagePath = public_path($conference->hero_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $conference->hero_image = null;
            $conference->save();
        }

        return response()->json([
            'message' => 'Hero image removed successfully'
        ]);
    }
}
