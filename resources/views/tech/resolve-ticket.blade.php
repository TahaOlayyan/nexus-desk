@extends('layouts.tech')

@section('title', 'Resolve Ticket')

@section('content')
<div class="max-w-3xl space-y-6">

    <div>
        <a href="{{ route('tech.workspace') }}" class="inline-flex items-center gap-2 text-xs font-medium text-slate-400 hover:text-white transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Workspace</span>
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm space-y-1 shadow-lg">
        @foreach($errors->all() as $error)
        <div class="flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
            <span>{{ $error }}</span>
        </div>
        @endforeach
    </div>
    @endif

    @php
    $priorityLabel = match($ticket->priority) {
        'P1' => ['text' => 'P1 - Critical', 'class' => 'bg-red-500/10 text-red-400 border-red-500/20'],
        'P2' => ['text' => 'P2 - Medium', 'class' => 'bg-amber-500/10 text-amber-400 border-amber-500/20'],
        default => ['text' => 'P3 - Low', 'class' => 'bg-blue-500/10 text-blue-400 border-blue-500/20'],
    };

    $standardActions = [
        'Restarted Hardware / Service',
        'Replaced Faulty Cable / Component',
        'Updated Firmware / Drivers',
        'Reconfigured Network / VLAN',
        'Cleared Cache / Reset Application',
        'Ran Diagnostic Health Check',
        'On-Site Physical Inspection',
    ];

    $selectedActions = old('actions_taken', []);
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <span class="font-mono text-sm font-bold px-3 py-1 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20">
                TCK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
            </span>
            <span class="px-2.5 py-1 rounded-md text-xs font-semibold border {{ $priorityLabel['class'] }}">
                {{ $priorityLabel['text'] }}
            </span>
            <span class="text-xs text-slate-400">{{ $ticket->status }}</span>
        </div>

        <button type="button" onclick="openEscalateModal()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-red-400 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 transition cursor-pointer">
            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
            <span>Escalate Ticket</span>
        </button>
    </div>

    <!-- Incident & Asset Details -->
    <div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-400 mb-4 pb-3 border-b border-slate-800">
            Incident & Asset Details
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
            <div>
                <span class="block text-xs text-slate-500">Organization / Branch</span>
                <strong class="text-sm text-white mt-1 block">{{ $ticket->company->name ?? 'N/A' }}</strong>
            </div>
            <div>
                <span class="block text-xs text-slate-500">Affected Asset</span>
                <strong class="text-sm text-blue-400 font-mono mt-1 block">{{ $ticket->asset->name ?? 'N/A' }}</strong>
            </div>
            <div>
                <span class="block text-xs text-slate-500">Category</span>
                <span class="text-sm text-slate-300 mt-1 block">{{ $ticket->category }}</span>
            </div>
            <div>
                <span class="block text-xs text-slate-500">Reported At</span>
                <span class="text-sm text-slate-300 mt-1 block">{{ $ticket->created_at->format('Y-m-d h:i A') }}</span>
            </div>
        </div>

        <div>
            <span class="block text-xs text-slate-500 mb-1.5">Client Problem Description</span>
            <div class="bg-[#030712] border border-slate-800 rounded-xl p-4 text-sm text-slate-300 leading-relaxed">
                {{ $ticket->description }}
            </div>
        </div>
    </div>

    <!-- Technical Resolution & Worklog Form -->
    <div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-semibold uppercase tracking-wider text-white mb-4 pb-3 border-b border-slate-800">
            Technical Resolution & Worklog
        </h2>

        <form action="{{ route('tech.tickets.resolve', $ticket->id) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                    Root Cause (Optional)
                </label>
                <input type="text" name="root_cause" value="{{ old('root_cause') }}" placeholder="e.g., Faulty power supply unit / Misconfigured VLAN" class="w-full bg-[#030712] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2.5">
                    Actions Taken (Select all that apply)
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach($standardActions as $action)
                    <label class="flex items-center gap-3 bg-[#030712] border border-slate-800 hover:border-slate-700 has-[:checked]:border-emerald-500/50 has-[:checked]:bg-emerald-500/5 rounded-xl px-3.5 py-2.5 cursor-pointer transition select-none">
                        <input
                            type="checkbox"
                            name="actions_taken[]"
                            value="{{ $action }}"
                            {{ in_array($action, (array) $selectedActions, true) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-emerald-500 accent-emerald-500 focus:ring-0 focus:ring-offset-0 shrink-0"
                        >
                        <span class="text-xs font-medium text-slate-200">{{ $action }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                    Resolution Steps (Worklog)
                </label>
                <textarea name="worklog_notes" rows="5" required placeholder="Write the technical steps taken to fix the issue..." class="w-full bg-[#030712] border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-emerald-500">{{ old('worklog_notes') }}</textarea>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('tech.workspace') }}" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-white border border-slate-800 hover:bg-slate-800/40 transition">
                    Cancel
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-emerald-600/20 cursor-pointer">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                    <span>Mark as Resolved</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Escalation Notice Card -->
    <div class="bg-[#0b1222] border border-red-500/20 rounded-2xl p-5 shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 shrink-0 mt-0.5">
                <i data-lucide="shield-alert" class="w-4 h-4"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white">Unable to Resolve at Tier 1?</h3>
                <p class="text-xs text-slate-400 mt-0.5">Escalate this ticket to senior engineering or vendor support with a documented technical reason.</p>
            </div>
        </div>
        <button type="button" onclick="openEscalateModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-red-400 bg-red-500/10 hover:bg-red-600 hover:text-white border border-red-500/30 transition cursor-pointer shrink-0">
            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
            <span>Escalate Ticket</span>
        </button>
    </div>

</div>

<!-- Escalate Ticket Modal -->
<div id="escalateModal" class="fixed inset-0 z-50 {{ ($errors->has('escalation_reason') || request()->boolean('escalate')) ? 'flex' : 'hidden' }} items-center justify-center bg-black/75 backdrop-blur-sm p-4" onclick="if (event.target === this) closeEscalateModal()">
    <div class="w-full max-w-lg bg-[#0b1222] border border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-[#080d1a]">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">Escalate Ticket TCK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</h3>
                    <p class="text-xs text-slate-400">Provide a clear technical justification (10–500 characters)</p>
                </div>
            </div>
            <button type="button" onclick="closeEscalateModal()" class="text-slate-400 hover:text-white transition cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('tech.tickets.escalate', $ticket->id) }}" method="POST" class="p-6 space-y-5">
            @csrf

            @error('escalation_reason')
            <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span>{{ $message }}</span>
            </div>
            @enderror

            <div>
                <label for="escalation_reason" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                    Escalation Reason <span class="text-red-400">*</span>
                </label>
                <textarea
                    id="escalation_reason"
                    name="escalation_reason"
                    rows="4"
                    required
                    minlength="10"
                    maxlength="500"
                    placeholder="e.g., Suspected motherboard/hardware controller failure requiring vendor RMA and Tier 2 network architect intervention..."
                    class="w-full bg-[#030712] border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-200 focus:outline-none focus:border-red-500"
                >{{ old('escalation_reason') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-800">
                <button type="button" onclick="closeEscalateModal()" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white border border-slate-800 hover:bg-slate-800/40 transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-500 text-white px-5 py-2 rounded-xl text-xs font-semibold transition shadow-lg shadow-red-600/20 cursor-pointer">
                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                    <span>Confirm Escalation</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEscalateModal() {
        const modal = document.getElementById('escalateModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        const textarea = document.getElementById('escalation_reason');
        if (textarea) textarea.focus();
    }

    function closeEscalateModal() {
        const modal = document.getElementById('escalateModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeEscalateModal();
        }
    });
</script>
@endsection
