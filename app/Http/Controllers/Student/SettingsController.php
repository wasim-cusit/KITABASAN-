<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('student.settings.index');
    }

    public function update(Request $request)
    {
        // Update student settings
        // TODO: Implement settings update logic

        return redirect()->route('student.settings.index')->with('success', 'Settings updated successfully.');
    }
}
