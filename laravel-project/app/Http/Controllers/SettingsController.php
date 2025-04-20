<?php

namespace App\Http\Controllers;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Settings::all(), 200); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:50|unique:settings,key',
            'value' => 'required|string|max:255',
        ]);

        $settings = Settings::create($validated);
        return response()->json($settings, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($key)
    {
        $settings = Settings::where('key', $key)->firstOrFail();
        return response()->json($settings, 200); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $key)
    {
        $settings = Settings::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'value' => 'required|string|max:255',
        ]);

        $settings->value = $validated['value'];
        $settings->save();

        return response()->json($settings);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($key)
    {
        $settings = Settings::where('key', $key)->firstOrFail();
        $settings->delete();

        return response()->json(['message' => 'Nastavenie odstránené']);
    }
}
