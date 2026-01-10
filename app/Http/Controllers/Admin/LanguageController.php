<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::orderBy('order')->get();
        return view('admin.settings.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.settings.languages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code',
            'native_name' => 'nullable|string|max:255',
            'flag' => 'nullable|string|max:10',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'direction' => 'required|in:ltr,rtl',
            'description' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'code' => strtolower($request->code),
            'native_name' => $request->native_name ?? $request->name,
            'flag' => $request->flag,
            'is_active' => $request->has('is_active') ? true : false,
            'direction' => $request->direction,
            'description' => $request->description,
            'order' => Language::max('order') + 1,
        ];

        // If this is set as default, remove default from others
        if ($request->has('is_default') && $request->is_default) {
            Language::where('is_default', true)->update(['is_default' => false]);
            $data['is_default'] = true;
        } else {
            $data['is_default'] = false;
        }

        Language::create($data);

        return redirect()->route('admin.settings.languages.index')
            ->with('success', 'Language created successfully.');
    }

    public function edit($id)
    {
        $language = Language::findOrFail($id);
        return view('admin.settings.languages.edit', compact('language'));
    }

    public function update(Request $request, $id)
    {
        $language = Language::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code,' . $id,
            'native_name' => 'nullable|string|max:255',
            'flag' => 'nullable|string|max:10',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'direction' => 'required|in:ltr,rtl',
            'description' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'code' => strtolower($request->code),
            'native_name' => $request->native_name ?? $request->name,
            'flag' => $request->flag,
            'is_active' => $request->has('is_active') ? true : false,
            'direction' => $request->direction,
            'description' => $request->description,
        ];

        // Handle default language
        if ($request->has('is_default') && $request->is_default) {
            Language::where('is_default', true)->where('id', '!=', $id)->update(['is_default' => false]);
            $data['is_default'] = true;
        } else {
            // If unchecking default, ensure at least one language is default
            if ($language->is_default) {
                $otherDefault = Language::where('is_default', true)->where('id', '!=', $id)->first();
                if (!$otherDefault) {
                    // Set first active language as default
                    $firstActive = Language::where('is_active', true)->where('id', '!=', $id)->first();
                    if ($firstActive) {
                        $firstActive->update(['is_default' => true]);
                    }
                }
            }
            $data['is_default'] = false;
        }

        $language->update($data);

        return redirect()->route('admin.settings.languages.index')
            ->with('success', 'Language updated successfully.');
    }

    public function destroy($id)
    {
        $language = Language::findOrFail($id);

        // Prevent deleting default language
        if ($language->is_default) {
            return redirect()->back()
                ->with('error', 'Cannot delete the default language. Please set another language as default first.');
        }

        $language->delete();

        return redirect()->route('admin.settings.languages.index')
            ->with('success', 'Language deleted successfully.');
    }

    public function setDefault($id)
    {
        $language = Language::findOrFail($id);
        $language->setAsDefault();

        return redirect()->back()
            ->with('success', 'Default language updated.');
    }

    public function toggleStatus($id)
    {
        $language = Language::findOrFail($id);

        // Prevent deactivating default language
        if ($language->is_default && !$language->is_active) {
            return redirect()->back()
                ->with('error', 'Cannot deactivate the default language.');
        }

        $language->update(['is_active' => !$language->is_active]);

        return redirect()->back()
            ->with('success', 'Language status updated.');
    }
}
