<?php
namespace App\Http\Controllers\Buyer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tickets = Auth::user()->tickets()
            ->with(['event', 'ticketType'])
            ->whereHas('event', fn($q) => $q->where('start_date', '>=', now()))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('buyer.dashboard', compact('tickets'));
    }
}
