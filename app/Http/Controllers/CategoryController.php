<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
public function index()
{
    $categories = Category::where('is_archived', false)
        ->latest()
        ->get();

    // group by name
    $grouped = $categories->groupBy('name');

    // build durations only if multiple exist per name
    $durations = [];

    foreach ($grouped as $name => $items) {

        if ($items->count() > 1) {

            foreach ($items as $cat) {
                if (!$cat->start_date || !$cat->end_date) continue;

                $start = \Carbon\Carbon::parse($cat->start_date);
                $end = \Carbon\Carbon::parse($cat->end_date);

                $label = $start->format('M Y') . ' - ' . $end->format('M Y');

                $durations[$name][] = $label;
            }
        }
    }

    return view('admin.categories', [
        'categories' => $categories,
        'durations' => $durations
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'deadline' => 'nullable|date',
    ]);

    // prevent duplicate SAME NAME + SAME DURATION
    $exists = Category::where('name', $request->name)
        ->where('start_date', $request->start_date)
        ->where('end_date', $request->end_date)
        ->exists();

    if ($exists) {
        return back()->with('error', 'This duration already exists for this category.');
    }

    Category::create($request->only([
        'name',
        'start_date',
        'end_date',
        'deadline'
    ]));

    return back()->with('success', 'Category added successfully');
}




    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:categories,id',
            'name' => 'required',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'deadline' => 'nullable|date',
        ]);

        $category = Category::findOrFail($request->id);

        $category->update([
            'name' => $request->name,
            'start_date' => $request->start_date ?: null,
            'end_date' => $request->end_date ?: null,
            'deadline' => $request->deadline ?: null,
        ]);

        return back()->with('success', 'Category updated successfully');
    }

    public function archive($id)
    {
        $cat = Category::findOrFail($id);
        $cat->update(['is_archived' => true]);

        return back()->with('success', 'Category archived');
    }

    public function toggle($id)
    {
        $cat = Category::findOrFail($id);
        $cat->update(['is_active' => !$cat->is_active]);

        return back()->with('success', 'Category status updated');
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return back()->with('success', 'Category deleted permanently');
    }
}
