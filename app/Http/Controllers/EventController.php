<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['publicIndex', 'show']);
        $this->middleware('admin')->except(['publicIndex', 'show']);
    }

    public function index()
    {
        $today = Carbon::today();

        // UPCOMING: end_date is today or later
        $upcomingEvents = Event::whereDate('end_date', '>=', $today)
            ->orderBy('start_date')
            ->get();

        // PAST: end_date is before today (yesterday and earlier)
        $pastEvents = Event::whereDate('end_date', '<', $today)
            ->orderByDesc('start_date')
            ->get();

        return view('events.index', compact('upcomingEvents', 'pastEvents'));
    }

    public function create()
    {
        return view('events.create');
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'required|string',
        'location'    => 'required|string|max:255',
        'status'      => 'required|in:upcoming,past',
        'start_date'  => 'required|date',
        'end_date'    => 'required|date|after_or_equal:start_date',
        'images.*'    => 'image|max:2048'
    ]);

    $event = Event::create($validated);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('events', 'public');

            $event->images()->create([
                'image_path' => $path
            ]);
        }
    }

    return redirect()
        ->route('events.index')
        ->with('success', 'Event created successfully.');
}

    public function edit($id)
    {
        $event = Event::with('images')->findOrFail($id);

        return view('events.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::with('images')->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string',
            'description' => 'required|string',
        ]);

        $event->update([
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('events', 'public');

                $event->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->route('events.index')
            ->with('success', 'Event updated successfully!');
    }

    public function destroy($id)
    {
        $event = Event::with('images')->findOrFail($id);

        foreach ($event->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $event->delete();

        return back()->with('success', 'Event deleted.');
    }

    public function publicIndex()
    {
        $events = Event::with('images')
            ->where('is_public', true)
            ->where('event_date', '>=', Carbon::today())
            ->orderBy('event_date')
            ->get();

        return view('events.public', compact('events'));
    }

    public function show($id)
    {
        $event = Event::with('images')
            ->where('is_public', true)
            ->findOrFail($id);

        return view('events.show', compact('event'));
    }
}
