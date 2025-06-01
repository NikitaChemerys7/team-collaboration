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
        return response()->json(Conference::all());
    }

    public function getEditableConferences(Request $request)
    {
        $user = $request->user();
        
        if ($user->isAdmin()) {
            return response()->json(Conference::all());
        }
        
        $managedYears = $user->managedYears()->pluck('year');
        
        return response()->json(
            Conference::whereIn('year', $managedYears)
                ->orderBy('year', 'desc')
                ->orderBy('date', 'desc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
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

        if (!$user->isAdmin()) {
            if (!$user->canManageYear($request->year)) {
                return response()->json([
                    'error' => 'You do not have permission to create conferences for this year.'
                ], 403);
            }
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
        $user = auth()->user();
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


        if (!$user->isAdmin() && !$user->canManageYear($request->year)) {
            return response()->json([
                'error' => 'You do not have permission to update conferences for this year.'
            ], 403);
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

    public function getEditors($year)
    {
        $editors = User::whereHas('managedYears', function($query) use ($year) {
            $query->where('year', $year);
        })->select('id', 'name', 'email')->get();

        return response()->json([
            'editors' => $editors
        ]);
    }

    public function assignEditor(Request $request, $year)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        
        if ($user->managedYears()->where('year', $year)->exists()) {
            return response()->json(['message' => 'User is already assigned to this year'], 422);
        }

        $user->managedYears()->create([
            'year' => $year,
            'granted_by_user_id' => auth()->id(),
            'granted_at' => now()
        ]);

        return response()->json(['message' => 'Editor assigned successfully']);
    }

    public function removeEditor(Request $request, $year)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->managedYears()->where('year', $year)->delete();

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
