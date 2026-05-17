<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Studio;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('studio')->orderByDesc('created_at')->paginate(10);
        return view('owner.events.index', compact('events'));
    }

    public function create()
    {
        $studios = Studio::all();
        return view('owner.events.create', compact('studios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        Event::create($validated);

        return redirect()->route('owner.events.index')
            ->with('success', __('messages.success'));
    }

    public function edit(Event $event)
    {
        $studios = Studio::all();
        return view('owner.events.edit', compact('event', 'studios'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $event->update($validated);

        return redirect()->route('owner.events.index')
            ->with('success', __('messages.success'));
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('owner.events.index')
            ->with('success', __('messages.success'));
    }
}
