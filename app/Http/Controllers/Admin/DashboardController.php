<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'   => User::count(),
            'events'  => Event::count(),
            'tickets' => Ticket::count(),
            'revenue' => Order::where('status', 'approved')->sum('total'),
        ];
        return view('admin.dashboard', compact('stats'));
    }
}
