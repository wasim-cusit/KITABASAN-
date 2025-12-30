<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $totalCourses = Book::where('status', 'published')->count();
        $totalStudents = User::role('student')->count();
        $totalTeachers = User::role('teacher')->count();

        return view('public.about.index', compact('totalCourses', 'totalStudents', 'totalTeachers'));
    }
}
