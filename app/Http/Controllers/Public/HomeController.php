<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCourses = Book::where('status', 'published')
            ->with(['subject.grade', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $freeCourses = Book::where('status', 'published')
            ->where('is_free', true)
            ->with(['subject.grade', 'teacher'])
            ->latest()
            ->limit(6)
            ->get();

        $totalCourses = Book::where('status', 'published')->count();
        $totalStudents = User::role('student')->count();
        $totalTeachers = User::role('teacher')->count();

        return view('public.home', compact('featuredCourses', 'freeCourses', 'totalCourses', 'totalStudents', 'totalTeachers'));
    }
}
