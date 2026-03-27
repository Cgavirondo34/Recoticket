<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['organizer', 'category'])->orderByDesc('created_at')->paginate(20);
        return view('admin.events.index', compact('events'));
    }

    public function publish(Event $event)
    {
        $newStatus = $event->status === 'published' ? 'draft' : 'published';
        $event->update(['status' => $newStatus]);
        return back()->with('success', 'Estado del evento actualizado.');
    }
}
