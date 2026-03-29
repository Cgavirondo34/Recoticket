<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Member;
use App\Models\Routine;
use App\Models\RoutineAssignment;
use Illuminate\Http\Request;

class RoutineController extends Controller
{
    public function index()
    {
        $routines = Routine::withCount('activeAssignments')->orderBy('name')->paginate(20);
        return view('gym.routines.index', compact('routines'));
    }

    public function create()
    {
        $exercises = Exercise::orderBy('name')->get();
        $members   = Member::active()->orderBy('full_name')->get(['id', 'full_name']);
        return view('gym.routines.create', compact('exercises', 'members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'goal'                  => 'nullable|string',
            'notes'                 => 'nullable|string',
            'exercises'             => 'nullable|array',
            'exercises.*.id'        => 'required|exists:exercises,id',
            'exercises.*.sets'      => 'required|integer|min:1',
            'exercises.*.reps'      => 'required|string|max:50',
            'exercises.*.notes'     => 'nullable|string',
        ]);

        $routine = Routine::create([
            'name'       => $data['name'],
            'goal'       => $data['goal'] ?? null,
            'notes'      => $data['notes'] ?? null,
            'created_by' => auth()->id(),
            'active'     => true,
        ]);

        if (!empty($data['exercises'])) {
            foreach ($data['exercises'] as $order => $exercise) {
                $routine->exercises()->attach($exercise['id'], [
                    'sets'  => $exercise['sets'],
                    'reps'  => $exercise['reps'],
                    'notes' => $exercise['notes'] ?? null,
                    'order' => $order,
                ]);
            }
        }

        return redirect()->route('gym.routines.show', $routine)
            ->with('success', 'Rutina creada.');
    }

    public function show(Routine $routine)
    {
        $routine->load(['exercises', 'assignments.member']);
        $members = Member::active()->orderBy('full_name')->get(['id', 'full_name']);
        return view('gym.routines.show', compact('routine', 'members'));
    }

    public function edit(Routine $routine)
    {
        $routine->load('exercises');
        $exercises = Exercise::orderBy('name')->get();
        return view('gym.routines.edit', compact('routine', 'exercises'));
    }

    public function update(Request $request, Routine $routine)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'goal'              => 'nullable|string',
            'notes'             => 'nullable|string',
            'exercises'         => 'nullable|array',
            'exercises.*.id'    => 'required|exists:exercises,id',
            'exercises.*.sets'  => 'required|integer|min:1',
            'exercises.*.reps'  => 'required|string|max:50',
            'exercises.*.notes' => 'nullable|string',
        ]);

        $routine->update([
            'name'  => $data['name'],
            'goal'  => $data['goal'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $routine->exercises()->detach();
        foreach ($data['exercises'] ?? [] as $order => $exercise) {
            $routine->exercises()->attach($exercise['id'], [
                'sets'  => $exercise['sets'],
                'reps'  => $exercise['reps'],
                'notes' => $exercise['notes'] ?? null,
                'order' => $order,
            ]);
        }

        return redirect()->route('gym.routines.show', $routine)
            ->with('success', 'Rutina actualizada.');
    }

    public function destroy(Routine $routine)
    {
        $routine->delete();
        return redirect()->route('gym.routines.index')
            ->with('success', 'Rutina eliminada.');
    }

    /**
     * Assign a routine to a member.
     */
    public function assign(Request $request, Routine $routine)
    {
        $data = $request->validate([
            'member_id'      => 'required|exists:members,id',
            'assigned_at'    => 'required|date',
            'ends_at'        => 'nullable|date|after:assigned_at',
            'trainer_notes'  => 'nullable|string',
        ]);

        // Deactivate previous active assignment for this member+routine combo
        RoutineAssignment::where('member_id', $data['member_id'])
            ->where('routine_id', $routine->id)
            ->where('active', true)
            ->update(['active' => false]);

        RoutineAssignment::create(array_merge($data, [
            'routine_id' => $routine->id,
            'active'     => true,
        ]));

        return back()->with('success', 'Rutina asignada al socio.');
    }
}
