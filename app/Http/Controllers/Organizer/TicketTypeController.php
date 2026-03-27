<?php
namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketTypeController extends Controller
{
    private function getEvent(int $eventId): Event
    {
        $organizer = Auth::user()->organizer;
        if (!$organizer) abort(403);
        $event = Event::findOrFail($eventId);
        if ($event->organizer_id !== $organizer->id) abort(403);
        return $event;
    }

    public function index(Event $event)
    {
        $event = $this->getEvent($event->id);
        $ticketTypes = $event->ticketTypes()->get();
        return view('organizer.ticket-types.index', compact('event', 'ticketTypes'));
    }

    public function create(Event $event)
    {
        $event = $this->getEvent($event->id);
        return view('organizer.ticket-types.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $event = $this->getEvent($event->id);
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:1',
            'sale_start'  => 'nullable|date',
            'sale_end'    => 'nullable|date',
            'status'      => 'in:active,inactive',
        ]);
        $data['event_id'] = $event->id;
        TicketType::create($data);
        return redirect()->route('organizer.events.ticket-types.index', $event)->with('success', 'Tipo de entrada creado.');
    }

    public function edit(Event $event, TicketType $ticketType)
    {
        $event = $this->getEvent($event->id);
        if ($ticketType->event_id !== $event->id) abort(403);
        return view('organizer.ticket-types.edit', compact('event', 'ticketType'));
    }

    public function update(Request $request, Event $event, TicketType $ticketType)
    {
        $event = $this->getEvent($event->id);
        if ($ticketType->event_id !== $event->id) abort(403);
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:1',
            'sale_start'  => 'nullable|date',
            'sale_end'    => 'nullable|date',
            'status'      => 'in:active,inactive',
        ]);
        $ticketType->update($data);
        return redirect()->route('organizer.events.ticket-types.index', $event)->with('success', 'Tipo de entrada actualizado.');
    }

    public function destroy(Event $event, TicketType $ticketType)
    {
        $event = $this->getEvent($event->id);
        if ($ticketType->event_id !== $event->id) abort(403);
        $ticketType->delete();
        return redirect()->route('organizer.events.ticket-types.index', $event)->with('success', 'Tipo de entrada eliminado.');
    }
}
