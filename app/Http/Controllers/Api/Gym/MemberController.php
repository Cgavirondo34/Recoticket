<?php

namespace App\Http\Controllers\Api\Gym;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\Gym\MembershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(private readonly MembershipService $membershipService) {}

    /** GET /api/gym/members */
    public function index(Request $request): JsonResponse
    {
        $query = Member::query()
            ->with(['trainer:id,name', 'activeMembership.plan'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                  ->orWhere('dni', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            }))
            ->when($request->trainer_id, fn($q, $id) => $q->where('trainer_id', $id))
            ->orderBy('full_name');

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    /** POST /api/gym/members */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name'         => 'required|string|max:255',
            'dni'               => 'nullable|string|max:20',
            'phone'             => 'nullable|string|max:20',
            'whatsapp'          => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:255',
            'birth_date'        => 'nullable|date',
            'trainer_id'        => 'nullable|integer|exists:users,id',
            'status'            => 'nullable|in:active,expired,suspended,prospect,inactive',
            'notes'             => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone'   => 'nullable|string|max:20',
        ]);

        $validated['status'] = $validated['status'] ?? 'prospect';
        $validated['tenant_id'] = $request->user()->tenant_id;

        $member = Member::create($validated);

        return response()->json($member->load('trainer'), 201);
    }

    /** GET /api/gym/members/{member} */
    public function show(Member $member): JsonResponse
    {
        return response()->json($member->load([
            'trainer:id,name',
            'activeMembership.plan',
            'memberships.plan',
            'gymPayments.paymentMethod',
            'activeRoutine.exercises',
            'trainerNotes',
        ]));
    }

    /** PUT /api/gym/members/{member} */
    public function update(Request $request, Member $member): JsonResponse
    {
        $validated = $request->validate([
            'full_name'         => 'sometimes|required|string|max:255',
            'dni'               => 'nullable|string|max:20',
            'phone'             => 'nullable|string|max:20',
            'whatsapp'          => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:255',
            'birth_date'        => 'nullable|date',
            'trainer_id'        => 'nullable|integer|exists:users,id',
            'status'            => 'nullable|in:active,expired,suspended,prospect,inactive',
            'notes'             => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone'   => 'nullable|string|max:20',
        ]);

        $member->update($validated);

        return response()->json($member->fresh()->load('trainer', 'activeMembership.plan'));
    }

    /** DELETE /api/gym/members/{member} */
    public function destroy(Member $member): JsonResponse
    {
        $member->delete();
        return response()->json(null, 204);
    }
}
