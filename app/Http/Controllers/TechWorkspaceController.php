<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechWorkspaceController extends Controller
{
    // 1. شاشة الفني الرئيسية (مهامي + الطابور العام)
    public function index()
    {
        $myTasks = Ticket::with(['company', 'asset'])
            ->where('tech_id', Auth::id())
            ->where('status', 'In Progress')
            ->orderBy('priority', 'asc')
            ->latest('assigned_at')
            ->get();

        $openQueue = Ticket::with(['company', 'asset'])
            ->where('status', 'Open')
            ->whereNull('tech_id')
            ->orderBy('priority', 'asc') // P1 تطلع فوق دايماً
            ->oldest()                   // الأقدم أولاً (FIFO)
            ->get();

        return view('tech.workspace', compact('myTasks', 'openQueue'));
    }

    // 2. استلام تذكرة (Claim / Assign to Me)
    public function claim(Ticket $ticket)
    {
        // حماية ضد الـ Race Condition لو فني ثاني سبقه واستلمها بنفس اللحظة
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

    // 3. فتح شاشة تفاصيل التذكرة والـ Worklog
    public function showResolve(Ticket $ticket)
    {
        // ممنوع فني يفتح شاشة إغلاق تذكرة مش مسجلة باسمه أو مش In Progress
        if ($ticket->tech_id !== Auth::id() || $ticket->status !== 'In Progress') {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $ticket->load(['company', 'asset', 'client']);

        return view('tech.resolve-ticket', compact('ticket'));
    }

    // 4. حفظ التقرير وإغلاق التذكرة (Resolve)
    public function resolve(Request $request, Ticket $ticket)
    {
        if ($ticket->tech_id !== Auth::id() || $ticket->status !== 'In Progress') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'root_cause'    => 'nullable|string|max:255',
            'worklog_notes' => 'required|string|min:10',
        ]);

        $ticket->update([
            'root_cause'    => $request->root_cause,
            'worklog_notes' => $request->worklog_notes,
            'status'        => 'Resolved',
            'resolved_at'   => now(),
        ]);

        return redirect()->route('tech.workspace')
            ->with('success', 'Ticket TCK-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT) . ' marked as resolved and worklog saved!');
    }
}
