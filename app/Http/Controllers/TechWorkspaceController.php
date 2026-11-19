<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TechWorkspaceController extends Controller
{
    // 1. Technician Command Center (Live Ops: KPI Bar, Active Tasks, Open Queue)
    public function index()
    {
        $techId = Auth::id();

        $myTasks = Ticket::with(['company', 'asset'])
            ->where('tech_id', $techId)
            ->where('status', 'In Progress')
            ->orderBy('priority', 'asc')
            ->latest('assigned_at')
            ->get();

        $openQueue = Ticket::with(['company', 'asset'])
            ->where('status', 'Open')
            ->whereNull('tech_id')
            ->orderBy('priority', 'asc')
            ->oldest()
            ->get();

        $resolvedTickets = Ticket::where('tech_id', $techId)
            ->where('status', 'Resolved')
            ->get(['id', 'priority', 'sla_breached', 'assigned_at', 'resolved_at']);

        $activeCount = $myTasks->count();
        $openQueueCount = $openQueue->count();
        $resolvedCount = $resolvedTickets->count();

        $performanceScore = $resolvedTickets->sum(function (Ticket $ticket) {
            $basePoints = match ($ticket->priority) {
                'P1' => 30,
                'P2' => 20,
                default => 10,
            };

            $slaBonus = !$ticket->sla_breached ? 5 : 0;

            return $basePoints + $slaBonus;
        });

        $slaRate = $resolvedCount > 0
            ? (int) round(($resolvedTickets->where('sla_breached', false)->count() / $resolvedCount) * 100)
            : 100;

        $durationsInMinutes = $resolvedTickets
            ->filter(fn (Ticket $ticket) => $ticket->assigned_at && $ticket->resolved_at)
            ->map(fn (Ticket $ticket) => max(0, (int) $ticket->assigned_at->diffInMinutes($ticket->resolved_at, false)));

        $avgResolutionTime = $durationsInMinutes->isNotEmpty()
            ? $this->formatDuration((int) round($durationsInMinutes->avg()))
            : 'N/A';

        $rankTitle = match (true) {
            $performanceScore >= 300 => 'Elite Incident Commander',
            $performanceScore >= 150 => 'Senior Ops Engineer',
            $performanceScore >= 50  => 'Incident Specialist',
            default                  => 'Field Responder',
        };

        return view('tech.workspace', compact(
            'myTasks',
            'openQueue',
            'activeCount',
            'openQueueCount',
            'resolvedCount',
            'performanceScore',
            'slaRate',
            'avgResolutionTime',
            'rankTitle'
        ));
    }

    // 2. Dedicated Resolved History Page
    public function history()
    {
        $resolvedHistory = Ticket::with(['company', 'asset'])
            ->where('tech_id', Auth::id())
            ->where('status', 'Resolved')
            ->orderByDesc('resolved_at')
            ->get();

        return view('tech.history', compact('resolvedHistory'));
    }

    // 3. Claim an Open Ticket (Assign to Me)
    public function claim(Ticket $ticket)
    {
        if (!is_null($ticket->tech_id) || $ticket->status !== 'Open') {
            return back()->withErrors(['error' => 'This ticket has already been claimed by another technician.']);
        }

        $ticket->update([
            'tech_id'     => Auth::id(),
            'status'      => 'In Progress',
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'Ticket TCK-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT) . ' claimed and moved to your active tasks.');
    }

    // 4. Release / Unassign Ticket Back to Global Open Queue
    public function release(Ticket $ticket)
    {
        if ($ticket->tech_id !== Auth::id() || $ticket->status !== 'In Progress') {
            abort(403, 'Unauthorized action.');
        }

        $ticket->update([
            'tech_id'     => null,
            'status'      => 'Open',
            'assigned_at' => null,
        ]);

        return back()->with('success', 'Ticket TCK-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT) . ' released back to the global open queue.');
    }

    // 5. Escalate Ticket with Mandatory Reason
    public function escalate(Request $request, Ticket $ticket)
    {
        if ($ticket->tech_id !== Auth::id() || $ticket->status !== 'In Progress') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'escalation_reason' => 'required|string|min:10|max:500',
        ]);

        $ticket->update([
            'status'        => 'Escalated',
            'worklog_notes' => '[ESCALATED BY ' . Auth::user()->name . ']: ' . $request->escalation_reason,
        ]);

        return redirect()->route('tech.workspace')
            ->with('warning', 'Ticket TCK-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT) . ' has been escalated for Tier 2 / Management review.');
    }

    // 6. Show Ticket Resolution & Worklog Screen
    public function showResolve(Ticket $ticket)
    {
        if ($ticket->tech_id !== Auth::id() || $ticket->status !== 'In Progress') {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $ticket->load(['company', 'asset', 'client']);

        return view('tech.resolve-ticket', compact('ticket'));
    }

    // 7. Save Technical Resolution, Actions Taken & Mark Ticket as Resolved
    public function resolve(Request $request, Ticket $ticket)
    {
        if ($ticket->tech_id !== Auth::id() || $ticket->status !== 'In Progress') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'root_cause'      => 'nullable|string|max:255',
            'actions_taken'   => 'nullable|array',
            'actions_taken.*' => 'string|max:100',
            'worklog_notes'   => 'required|string|min:10',
        ]);

        $ticket->update([
            'root_cause'    => $request->root_cause,
            'actions_taken' => $request->input('actions_taken', []),
            'worklog_notes' => $request->worklog_notes,
            'status'        => 'Resolved',
            'resolved_at'   => now(),
        ]);

        return redirect()->route('tech.workspace')
            ->with('success', 'Ticket TCK-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT) . ' marked as resolved and worklog saved!');
    }

    // 8. Technician Settings Page
    public function settings()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('tech.settings', compact('user'));
    }

    // 9. Update Technician Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|min:8|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    // 10. Update Technician Avatar
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');

            $user->update([
                'avatar' => $path,
            ]);
        }

        return back()->with('success', 'Profile photo updated successfully!');
    }

    private function formatDuration(int $totalMinutes): string
    {
        if ($totalMinutes <= 0) {
            return '< 1m';
        }

        if ($totalMinutes < 60) {
            return "{$totalMinutes}m";
        }

        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
    }
}
