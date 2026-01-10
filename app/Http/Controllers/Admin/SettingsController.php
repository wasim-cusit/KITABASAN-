<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use App\Models\SystemSetting;
use App\Models\PaymentMethod;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $themeSettings = ThemeSetting::getGrouped();
        $systemSettings = SystemSetting::getGrouped();
        $paymentMethods = PaymentMethod::orderBy('order')->get();
        $languages = Language::orderBy('order')->get();
        
        return view('admin.settings.index', compact('themeSettings', 'systemSettings', 'paymentMethods', 'languages'));
    }

    public function update(Request $request)
    {
        // Update theme settings
        if ($request->has('theme_settings')) {
            $validated = $request->validate([
                'theme_settings' => 'array',
                'theme_settings.*' => 'nullable',
            ]);

            foreach ($validated['theme_settings'] ?? [] as $key => $value) {
                $setting = ThemeSetting::where('key', $key)->first();
                
                if ($setting) {
                    // Handle file uploads for image types
                    if ($setting->type === 'image' && $request->hasFile("theme_settings.{$key}")) {
                        $file = $request->file("theme_settings.{$key}");
                        
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

            ThemeSetting::clearCache();
        }

        // Update system settings
        if ($request->has('system_settings')) {
            $validated = $request->validate([
                'system_settings' => 'array',
                'system_settings.*' => 'nullable',
            ]);

            foreach ($validated['system_settings'] ?? [] as $key => $value) {
                $setting = SystemSetting::where('key', $key)->first();
                
                if ($setting) {
                    // Handle JSON type
                    if ($setting->type === 'json' && is_array($value)) {
                        $value = json_encode($value);
                    }

                    SystemSetting::setValue($key, $value);
                }
            }

            SystemSetting::clearCache();
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
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
