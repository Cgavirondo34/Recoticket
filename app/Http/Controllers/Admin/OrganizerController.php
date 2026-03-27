<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Organizer;

class OrganizerController extends Controller
{
    public function index()
    {
        $organizers = Organizer::with('user')->orderByDesc('created_at')->paginate(20);
        return view('admin.organizers.index', compact('organizers'));
    }

    public function verify(Organizer $organizer)
    {
        $organizer->update(['verified' => !$organizer->verified]);
        $msg = $organizer->verified ? 'Organizador verificado.' : 'Verificación retirada.';
        return back()->with('success', $msg);
    }
}
