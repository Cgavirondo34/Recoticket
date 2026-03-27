<?php
namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    public function __construct(private TicketService $ticketService) {}

    public function index()
    {
        return view('organizer.scan');
    }

    public function scan(Request $request)
    {
        $request->validate(['ticket_code' => 'required|string']);
        $result = $this->ticketService->validateTicket($request->ticket_code, Auth::user());
        return view('organizer.scan', compact('result'));
    }
}
