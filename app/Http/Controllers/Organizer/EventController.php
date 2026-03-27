<?php
namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    private function organizerOrFail()
    {
        $organizer = Auth::user()->organizer;
        if (!$organizer) abort(403, 'No organizer profile.');
        return $organizer;
    }

    public function index()
    {
        $organizer = $this->organizerOrFail();
        $events = $organizer->events()->with('category', 'venue')->orderByDesc('created_at')->paginate(10);
        return view('organizer.events.index', compact('events'));
    }

    public function create()
    {
        $categories = Category::all();
        $venues = Venue::all();
        return view('organizer.events.create', compact('categories', 'venues'));
    }

    public function store(Request $request)
    {
        $organizer = $this->organizerOrFail();
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category_id'    => 'required|exists:categories,id',
            'venue_id'       => 'nullable|exists:venues,id',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after:start_date',
            'total_capacity' => 'nullable|integer|min:1',
            'cover_image'    => 'nullable|url',
            'status'         => 'in:draft,published',
        ]);

        $data['organizer_id'] = $organizer->id;
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);

        Event::create($data);
        return redirect()->route('organizer.events.index')->with('success', 'Evento creado.');
    }

    public function edit(Event $event)
    {
        $organizer = $this->organizerOrFail();
        if ($event->organizer_id !== $organizer->id) abort(403);
        $categories = Category::all();
        $venues = Venue::all();
        return view('organizer.events.edit', compact('event', 'categories', 'venues'));
    }

    public function update(Request $request, Event $event)
    {
        $organizer = $this->organizerOrFail();
        if ($event->organizer_id !== $organizer->id) abort(403);
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category_id'    => 'required|exists:categories,id',
            'venue_id'       => 'nullable|exists:venues,id',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after:start_date',
            'total_capacity' => 'nullable|integer|min:1',
            'cover_image'    => 'nullable|url',
            'status'         => 'in:draft,published,cancelled,ended',
        ]);
        $event->update($data);
        return redirect()->route('organizer.events.index')->with('success', 'Evento actualizado.');
    }

    public function destroy(Event $event)
    {
        $organizer = $this->organizerOrFail();
        if ($event->organizer_id !== $organizer->id) abort(403);
        $event->delete();
        return redirect()->route('organizer.events.index')->with('success', 'Evento eliminado.');
    }
}
