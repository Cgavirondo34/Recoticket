<?php
namespace App\Http\Controllers\Organizer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $organizer = Auth::user()->organizer;
        if (!$organizer) return redirect('/')->with('error', 'No organizer profile found.');

        $events = $organizer->events()->withCount('tickets')->get();
        $totalTickets = $events->sum('tickets_count');
        $totalRevenue = $organizer->events()
            ->join('orders', 'events.id', '=', 'orders.event_id')
            ->where('orders.status', 'approved')
            ->sum('orders.total');

        return view('organizer.dashboard', compact('organizer', 'events', 'totalTickets', 'totalRevenue'));
    }
}
