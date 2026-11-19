@extends('layouts.tech')

@section('title', 'Resolve Ticket')

@section('content')
<div class="max-w-3xl">

    <div class="mb-6">
        <a href="{{ route('tech.workspace') }}" class="inline-flex items-center gap-2 text-xs font-medium text-slate-400 hover:text-white transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Workspace</span>
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm space-y-1 shadow-lg">
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
    @endphp

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <span class="font-mono text-sm font-bold px-3 py-1 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20">
            TCK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
        </span>
        <span class="px-2.5 py-1 rounded-md text-xs font-semibold border {{ $priorityLabel['class'] }}">
            {{ $priorityLabel['text'] }}
        </span>
        <span class="text-xs text-slate-400">{{ $ticket->status }}</span>
    </div>

    <!-- تفاصيل العطل والجهاز -->
    <div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl p-6 mb-6 shadow-xl">
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

    <!-- فورم التقرير والإغلاق -->
    <div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-semibold uppercase tracking-wider text-white mb-4 pb-3 border-b border-slate-800">
            Technical Resolution & Worklog
        </h2>

        <form action="{{ route('tech.tickets.resolve', $ticket->id) }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                    Root Cause (Optional)
                </label>
                <input type="text" name="root_cause" value="{{ old('root_cause') }}" placeholder="e.g., Faulty power supply unit / Misconfigured VLAN" class="w-full bg-[#030712] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-emerald-500">
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

</div>
@endsection