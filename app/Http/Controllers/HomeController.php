<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['category', 'venue', 'ticketTypes'])
            ->where('status', 'published')
            ->orderByDesc('featured')
            ->orderBy('start_date');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        $events = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('home', compact('events', 'categories'));
    }
}
