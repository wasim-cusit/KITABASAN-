<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = ThemeSetting::getGrouped();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = ThemeSetting::where('key', $key)->first();
            
            if ($setting) {
                // Handle file uploads for image types
                if ($setting->type === 'image' && $request->hasFile("settings.{$key}")) {
                    $file = $request->file("settings.{$key}");
                    
                    // Delete old file if exists
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    
                    // Store new file
                    $path = $file->store('theme', 'public');
                    $value = $path;
                }

                // Handle JSON type
                if ($setting->type === 'json' && is_array($value)) {
                    $value = json_encode($value);
                }

                ThemeSetting::setValue($key, $value);
            }
        }

        // Clear cache
        ThemeSetting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Theme settings updated successfully.');
    }

    public function updateSetting(Request $request, $key)
    {
        $request->validate([
            'value' => 'required',
        ]);

        $setting = ThemeSetting::where('key', $key)->firstOrFail();

        $value = $request->value;

        // Handle file uploads for image types
        if ($setting->type === 'image' && $request->hasFile('value')) {
            $file = $request->file('value');
            
            // Delete old file if exists
            if ($setting->value) {
                Storage::disk('public')->delete($setting->value);
            }
            
            // Store new file
            $path = $file->store('theme', 'public');
            $value = $path;
        }

        // Handle JSON type
        if ($setting->type === 'json' && is_array($value)) {
            $value = json_encode($value);
        }

        ThemeSetting::setValue($key, $value);

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully.',
        ]);
    }
}
