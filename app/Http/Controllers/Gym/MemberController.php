<?php

namespace App\Http\Controllers\Gym;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\Trainer;
use App\Services\Gym\MemberService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(protected MemberService $memberService) {}

    public function index(Request $request)
    {
        $query = Member::with(['currentPlan', 'trainer'])->orderBy('full_name');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $members = $query->paginate(20)->withQueryString();

        return view('gym.members.index', compact('members'));
    }

    public function create()
    {
        $plans    = MembershipPlan::where('active', true)->orderBy('name')->get();
        $trainers = Trainer::where('active', true)->orderBy('full_name')->get();
        return view('gym.members.create', compact('plans', 'trainers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'   => 'required|string|max:255',
            'dni'         => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'whatsapp'    => 'nullable|string|max:50',
            'birth_date'  => 'nullable|date',
            'trainer_id'  => 'nullable|exists:trainers,id',
            'notes'       => 'nullable|string',
            'plan_id'     => 'nullable|exists:membership_plans,id',
            'starts_at'   => 'nullable|date',
            'auto_renew'  => 'boolean',
        ]);

        $member = Member::create([
            'full_name'  => $data['full_name'],
            'dni'        => $data['dni'] ?? null,
            'email'      => $data['email'] ?? null,
            'phone'      => $data['phone'] ?? null,
            'whatsapp'   => $data['whatsapp'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'trainer_id' => $data['trainer_id'] ?? null,
            'notes'      => $data['notes'] ?? null,
            'status'     => 'active',
        ]);

        if (!empty($data['plan_id'])) {
            $plan = MembershipPlan::findOrFail($data['plan_id']);
            $this->memberService->assignPlan(
                $member, $plan,
                $data['starts_at'] ?? null,
                (bool) ($data['auto_renew'] ?? false)
            );
        }

        return redirect()->route('gym.members.show', $member)
            ->with('success', 'Socio creado exitosamente.');
    }

    public function show(Member $member)
    {
        $member->load(['currentPlan', 'trainer', 'memberships.plan', 'payments.paymentMethod', 'routineAssignments.routine']);
        return view('gym.members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $plans    = MembershipPlan::where('active', true)->orderBy('name')->get();
        $trainers = Trainer::where('active', true)->orderBy('full_name')->get();
        return view('gym.members.edit', compact('member', 'plans', 'trainers'));
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'full_name'  => 'required|string|max:255',
            'dni'        => 'nullable|string|max:50',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:50',
            'whatsapp'   => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'trainer_id' => 'nullable|exists:trainers,id',
            'status'     => 'required|in:active,expired,suspended',
            'notes'      => 'nullable|string',
        ]);

        $member->update($data);

        return redirect()->route('gym.members.show', $member)
            ->with('success', 'Socio actualizado.');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('gym.members.index')
            ->with('success', 'Socio eliminado.');
    }
}
