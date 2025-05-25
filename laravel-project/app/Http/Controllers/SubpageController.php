<?php

namespace App\Http\Controllers;

use App\Models\Subpage;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubpageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Subpage::with('conference')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $conferences = Conference::all();
        return view('admin.subpages.create', compact('conferences'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conference_id' => 'required|exists:conferences,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['is_active'] = $request->has('is_active');

        $subpage = Subpage::create($data);

        if ($request->expectsJson()) {
            return response()->json($subpage->load('conference'), 201);
        }

        return redirect()->route('admin.edit-subpages')->with('success', 'Subpage created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subpage = Subpage::with('conference')->find($id);
        
        if (!$subpage) {
            return response()->json(['message' => 'Subpage not found'], 404);
        }

        return $subpage;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $subpage = Subpage::findOrFail($id);
        $conferences = Conference::all();
        return view('admin.subpages.edit', compact('subpage', 'conferences'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subpage = Subpage::find($id);
        
        if (!$subpage) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Subpage not found'], 404);
            }
            return back()->with('error', 'Subpage not found');
        }

        $validator = Validator::make($request->all(), [
            'conference_id' => 'required|exists:conferences,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['is_active'] = $request->has('is_active');

        $subpage->update($data);

        if ($request->expectsJson()) {
            return response()->json($subpage->load('conference'));
        }

        return redirect()->route('admin.edit-subpages')->with('success', 'Subpage updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subpage = Subpage::find($id);
        
        if (!$subpage) {
            return response()->json(['message' => 'Subpage not found'], 404);
        }

        $subpage->delete();

        return response()->json(null, 204);
    }
}
