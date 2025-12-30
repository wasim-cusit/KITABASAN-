<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('teacher.settings.index');
    }

    public function update(Request $request)
    {
        // Update teacher settings
        // TODO: Implement settings update logic

        return redirect()->route('teacher.settings.index')->with('success', 'Settings updated successfully.');
    }
}
