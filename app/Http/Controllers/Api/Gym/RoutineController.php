<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\RoutineExercise;
use App\Models\TrainerNote;
use App\Models\WorkoutRoutine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoutineController extends Controller
{
    /** GET /api/gym/members/{member}/routines */
    public function index(Member $member): JsonResponse
    {
        $routines = WorkoutRoutine::with(['trainer:id,name', 'exercises'])
            ->where('member_id', $member->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($routines);
    }

    /** POST /api/gym/members/{member}/routines */
    public function store(Request $request, Member $member): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'goal'       => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after:start_date',
            'notes'      => 'nullable|string',
            'exercises'  => 'nullable|array',
            'exercises.*.name'          => 'required|string|max:255',
            'exercises.*.muscle_group'  => 'nullable|string|max:100',
            'exercises.*.sets'          => 'nullable|integer|min:1',
            'exercises.*.reps'          => 'nullable|string|max:50',
            'exercises.*.rest_seconds'  => 'nullable|string|max:50',
            'exercises.*.weight'        => 'nullable|string|max:50',
            'exercises.*.day_of_week'   => 'nullable|integer|between:1,7',
            'exercises.*.sort_order'    => 'nullable|integer',
            'exercises.*.notes'         => 'nullable|string',
        ]);

        $routine = DB::transaction(function () use ($validated, $member, $request) {
            // Deactivate previous active routine
            WorkoutRoutine::where('member_id', $member->id)
                ->where('active', true)
                ->update(['active' => false]);

            $routine = WorkoutRoutine::create([
                'tenant_id'  => $member->tenant_id,
                'member_id'  => $member->id,
                'trainer_id' => $request->user()->id,
                'name'       => $validated['name'],
                'goal'       => $validated['goal'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date'   => $validated['end_date'] ?? null,
                'version'    => (WorkoutRoutine::where('member_id', $member->id)->max('version') ?? 0) + 1,
                'active'     => true,
                'notes'      => $validated['notes'] ?? null,
            ]);

            foreach ($validated['exercises'] ?? [] as $idx => $exerciseData) {
                RoutineExercise::create(array_merge($exerciseData, [
                    'workout_routine_id' => $routine->id,
                    'sort_order' => $exerciseData['sort_order'] ?? $idx,
                ]));
            }

            return $routine;
        });

        return response()->json($routine->load('exercises', 'trainer:id,name'), 201);
    }

    /** GET /api/gym/routines/{routine} */
    public function show(WorkoutRoutine $routine): JsonResponse
    {
        return response()->json($routine->load(['exercises', 'trainer:id,name', 'notes.trainer:id,name']));
    }

    /** POST /api/gym/members/{member}/trainer-notes */
    public function storeNote(Request $request, Member $member): JsonResponse
    {
        $validated = $request->validate([
            'workout_routine_id' => 'nullable|integer|exists:workout_routines,id',
            'type'               => 'nullable|in:observation,progress,follow_up,incident',
            'content'            => 'required|string',
            'noted_at'           => 'nullable|date',
        ]);

        $note = TrainerNote::create([
            'tenant_id'          => $member->tenant_id,
            'member_id'          => $member->id,
            'trainer_id'         => $request->user()->id,
            'workout_routine_id' => $validated['workout_routine_id'] ?? null,
            'type'               => $validated['type'] ?? 'observation',
            'content'            => $validated['content'],
            'noted_at'           => $validated['noted_at'] ?? today(),
        ]);

        return response()->json($note->load('trainer:id,name'), 201);
    }
}
