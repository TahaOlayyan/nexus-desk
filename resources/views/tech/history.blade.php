@extends('layouts.tech')

@section('title', 'Resolved History')

@section('content')
@php
    $formatDuration = function (?\Illuminate\Support\Carbon $start, ?\Illuminate\Support\Carbon $end): string {
        if (!$start || !$end) {
            return 'N/A';
        }

        $totalMinutes = max(0, (int) $start->diffInMinutes($end, false));

        if ($totalMinutes < 1) {
            return '< 1m';
        }
        if ($totalMinutes < 60) {
            return "{$totalMinutes}m";
        }

        $hours = intdiv($totalMinutes, 60);
        $mins = $totalMinutes % 60;

        return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
    };
@endphp

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Resolved History</h1>
            <p class="text-sm text-slate-400 mt-1">Completed incidents, technical worklogs, and resolution durations</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-[#0b1222] border border-slate-800 px-4 py-2 rounded-xl">
                <span class="text-xs text-slate-400">Total Closed:</span>
                <span class="text-sm font-bold text-emerald-400">{{ $resolvedHistory->count() }}</span>
            </div>
            <a href="{{ route('tech.workspace') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-[#0b1222] hover:bg-slate-800/80 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Command Center</span>
            </a>
        </div>
    </div>

    <!-- Resolved Tickets Table -->
    <section class="bg-[#0b1222] border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-[#080d1a]/60">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <h2 class="text-sm font-semibold text-white">My Closed Incidents</h2>
            </div>
            <span class="text-xs text-slate-400">Ordered by most recently resolved</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-950/40 text-slate-400 text-xs uppercase border-b border-slate-800 tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Ticket ID</th>
                        <th class="px-5 py-3.5">Company</th>
                        <th class="px-5 py-3.5">Asset</th>
                        <th class="px-5 py-3.5">Priority</th>
                        <th class="px-5 py-3.5">Actions Taken</th>
                        <th class="px-5 py-3.5">Root Cause & Worklog</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Duration</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Resolved At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    @forelse($resolvedHistory as $ticket)
                    @php
                        $pColor = match($ticket->priority) {
                            'P1' => 'bg-red-500/10 text-red-400 border-red-500/20',
                            'P2' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                            default => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                        };
                        $actions = is_array($ticket->actions_taken) ? $ticket->actions_taken : [];
                    @endphp
                    <tr class="hover:bg-slate-800/30 transition align-top">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="font-mono font-semibold text-emerald-400">
                                TCK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $ticket->category }}</div>
                        </td>
                        <td class="px-5 py-4 font-medium text-white whitespace-nowrap">
                            {{ $ticket->company->name ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-4 text-slate-300 min-w-[160px]">
                            {{ $ticket->asset->name ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold border {{ $pColor }}">
                                {{ $ticket->priority }}
                            </span>
                        </td>
                        <td class="px-5 py-4 min-w-[200px]">
                            @if(count($actions) > 0)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($actions as $action)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                    {{ $action }}
                                </span>
                                @endforeach
                            </div>
                            @else
                            <span class="text-xs text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 min-w-[240px] max-w-sm">
                            @if($ticket->root_cause)
                            <div class="text-xs font-semibold text-slate-100 mb-1">
                                {{ $ticket->root_cause }}
                            </div>
                            @endif
                            <p class="text-xs text-slate-400 leading-relaxed" title="{{ $ticket->worklog_notes }}">
                                {{ $ticket->worklog_notes ?? 'No worklog notes recorded.' }}
                            </p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-200">
                                <i data-lucide="timer" class="w-3.5 h-3.5 text-blue-400"></i>
                                <span>{{ $formatDuration($ticket->assigned_at, $ticket->resolved_at) }}</span>
                            </div>
                            <div class="mt-1">
                                @if($ticket->sla_breached)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-500/10 text-red-400 border border-red-500/20">
                                    SLA Breached
                                </span>
                                @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    SLA Met
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($ticket->resolved_at)
                            <div class="text-xs text-slate-200 font-medium">{{ $ticket->resolved_at->format('M d, Y h:i A') }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $ticket->resolved_at->diffForHumans() }}</div>
                            @else
                            <span class="text-xs text-slate-500">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="inline-flex flex-col items-center gap-2 text-slate-500">
                                <i data-lucide="archive" class="w-8 h-8 text-slate-700"></i>
                                <p class="text-sm font-medium text-slate-400">No resolved tickets yet</p>
                                <p class="text-xs text-slate-500">Completed tickets and their worklogs will appear here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection
