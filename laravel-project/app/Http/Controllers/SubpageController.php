<?php

namespace App\Http\Controllers;

use App\Models\Subpage;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubpageController extends Controller
{
    public function index($conferenceId)
    {
        try {
            $conference = Conference::findOrFail($conferenceId);

            $subpages = Subpage::where('conference_id', $conferenceId)
                ->orderBy('order')
                ->get();

            return response()->json($subpages);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Conference not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch subpages'], 500);
        }
    }

    public function store(Request $request, $conferenceId)
    {
        if (!Auth::user()->canManageConference($conferenceId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer|min:0',
            'is_published' => 'boolean'
        ]);

        $slug = Str::slug($request->title);

        $count = 1;
        $originalSlug = $slug;
        while (Subpage::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $subpage = Subpage::create([
            'conference_id' => $conferenceId,
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'order' => $request->order,
            'is_published' => $request->is_published ?? true
        ]);

        return response()->json($subpage, 201);
    }

    public function show($conferenceId, $subpageId)
    {
        $subpage = Subpage::where('conference_id', $conferenceId)
            ->where('id', $subpageId)
            ->firstOrFail();

        return response()->json($subpage);
    }

    public function update(Request $request, $conferenceId, $subpageId)
    {
        try {
            \Log::info('Updating subpage', [
                'conferenceId' => $conferenceId,
                'subpageId' => $subpageId,
                'requestData' => $request->all()
            ]);

            $subpage = Subpage::where('conference_id', $conferenceId)
                      ->where('id', $subpageId)
                      ->firstOrFail();

            if (!Auth::user()->canManageSubpage($subpage->id)) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'order' => 'required|integer|min:0',
                'is_published' => 'boolean'
            ]);

            $slug = Str::slug($request->title);

            if ($slug !== $subpage->slug) {
                $count = 1;
                $originalSlug = $slug;
                while (Subpage::where('slug', $slug)
                    ->where('id', '!=', $subpageId)
                    ->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            $subpage->update([
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'order' => $request->order,
                'is_published' => $request->is_published ?? $subpage->is_published
            ]);

            \Log::info('Subpage updated successfully', ['subpage' => $subpage]);

            return response()->json($subpage);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Subpage not found', [
                'conferenceId' => $conferenceId,
                'subpageId' => $subpageId
            ]);
            return response()->json(['message' => 'Subpage not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating subpage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Failed to update subpage'], 500);
        }
    }

    public function destroy($conferenceId, $subpageId)
    {
        $subpage = Subpage::where('conference_id', $conferenceId)
                      ->where('id', $subpageId)
                      ->firstOrFail();

        if (!Auth::user()->canManageSubpage($subpage->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subpage->delete();
        return response()->json(null, 204);
        }
}
