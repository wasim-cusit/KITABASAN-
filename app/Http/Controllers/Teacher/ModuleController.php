<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function store(Request $request, $bookId)
    {
        $book = Book::findOrFail($bookId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_index' => 'nullable|integer',
        ]);

        $module = Module::create([
            'book_id' => $bookId,
            'title' => $request->title,
            'description' => $request->description,
            'order_index' => $request->order_index ?? 0,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Module created successfully.');
    }

    public function update(Request $request, $bookId, $moduleId)
    {
        $book = Book::findOrFail($bookId);
        $module = Module::findOrFail($moduleId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $module->book_id !== $bookId) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_index' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $module->update([
            'title' => $request->title,
            'description' => $request->description,
            'order_index' => $request->order_index ?? $module->order_index,
            'is_active' => $request->has('is_active') ? $request->is_active : $module->is_active,
        ]);

        return redirect()->back()->with('success', 'Module updated successfully.');
    }

    public function destroy($bookId, $moduleId)
    {
        $book = Book::findOrFail($bookId);
        $module = Module::findOrFail($moduleId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $module->book_id !== $bookId) {
            abort(403, 'Unauthorized');
        }

        $module->delete();

        return redirect()->back()->with('success', 'Module deleted successfully.');
    }
}
