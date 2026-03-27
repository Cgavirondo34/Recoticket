<?php
namespace App\Http\Controllers\Buyer;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Auth::user()->tickets()
            ->with(['event', 'ticketType'])
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('buyer.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) abort(403);
        $ticket->load(['event', 'ticketType', 'order']);
        return view('buyer.tickets.show', compact('ticket'));
    }
}
