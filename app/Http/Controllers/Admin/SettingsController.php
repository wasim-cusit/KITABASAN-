<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        // Update system settings
        // TODO: Implement settings update logic

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }
}
