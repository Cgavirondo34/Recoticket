<?php
namespace App\Http\Controllers;
use App\Models\Event;

class EventController extends Controller
{
    public function show(string $slug)
    {
        $event = Event::with(['category', 'venue', 'organizer', 'ticketTypes' => function($q) {
            $q->where('status', 'active');
        }])->where('slug', $slug)->where('status', 'published')->firstOrFail();

        return view('events.show', compact('event'));
    }
}
