@extends('layouts.tech')

@section('title', 'Technician Command Center')

@section('content')
@php
    $formatElapsed = function (?\Illuminate\Support\Carbon $timestamp): string {
        if (!$timestamp) {
            return 'N/A';
        }

        $totalMinutes = max(0, (int) $timestamp->diffInMinutes(now(), false));

        if ($totalMinutes < 1) {
            return '< 1m';
        }
        if ($totalMinutes < 60) {
            return "{$totalMinutes}m";
        }
        if ($totalMinutes < 1440) {
            $hours = intdiv($totalMinutes, 60);
            $mins = $totalMinutes % 60;
            return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
        }

        $days = intdiv($totalMinutes, 1440);
        $hours = intdiv($totalMinutes % 1440, 60);
        return $hours > 0 ? "{$days}d {$hours}h" : "{$days}d";
    };

    $rankStyle = match ($rankTitle) {
        'Elite Incident Commander' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
        'Senior Ops Engineer'      => 'bg-violet-500/15 text-violet-300 border-violet-500/30',
        'Incident Specialist'      => 'bg-blue-500/15 text-blue-300 border-blue-500/30',
        default                    => 'bg-slate-800/80 text-slate-300 border-slate-700',
    };
@endphp

<div class="space-y-6">

    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-2 shadow-lg">
        <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('warning'))
    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm flex items-center gap-2 shadow-lg">
        <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
        <span>{{ session('warning') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-2 shadow-lg">
        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Technician Command Center</h1>
            <p class="text-sm text-slate-400 mt-1">Real-time incident dispatch, SLA monitoring, and active repair operations</p>
        </div>
        <a href="{{ route('tech.history') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-[#0b1222] hover:bg-slate-800/80 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition">
            <i data-lucide="history" class="w-3.5 h-3.5 text-emerald-400"></i>
            <span>View Resolved History</span>
        </a>
    </div>

    <!-- Top KPI Bar (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- KPI 1: My Active Tasks -->
        <div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl p-5 shadow-xl flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">My Active Tasks</span>
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <i data-lucide="wrench" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-bold text-white tracking-tight">{{ $activeCount }}</div>
                <p class="text-xs text-slate-400 mt-1">In Progress assigned to you</p>
            </div>
        </div>

        <!-- KPI 2: Open Queue Available -->
        <div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl p-5 shadow-xl flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Open Queue</span>
                <div class="w-9 h-9 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <i data-lucide="inbox" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-bold text-white tracking-tight">{{ $openQueueCount }}</div>
                <p class="text-xs text-slate-400 mt-1">Unassigned tickets awaiting claim</p>
            </div>
        </div>

        <!-- KPI 3: Tickets Resolved + SLA Compliance -->
        <div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl p-5 shadow-xl flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tickets Resolved</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-2.5">
                    <span class="text-3xl font-bold text-white tracking-tight">{{ $resolvedCount }}</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <i data-lucide="shield-check" class="w-3 h-3"></i>
                        {{ $slaRate }}% SLA
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1">SLA compliance on closed incidents</p>
            </div>
        </div>

        <!-- KPI 4: Performance & Rank -->
        <div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl p-5 shadow-xl flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Performance & Rank</span>
                <div class="w-9 h-9 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center text-violet-400">
                    <i data-lucide="award" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-3xl font-bold text-white tracking-tight">{{ $performanceScore }}</span>
                    <span class="text-xs font-semibold text-slate-400">pts</span>
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold border {{ $rankStyle }}">
                        {{ $rankTitle }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3 h-3 text-slate-500"></i>
                    <span>Avg Resolution: <strong class="text-slate-200">{{ $avgResolutionTime }}</strong></span>
                </p>
            </div>
        </div>
    </div>

    <!-- Split Command Center Grid (8 cols Active Tasks | 4 cols Live Open Queue) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left / Center Panel (8 cols): My Active Tasks -->
        <section class="lg:col-span-8 bg-[#0b1222] border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-[#080d1a]/60">
                <div class="flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <h2 class="text-sm font-semibold text-white">My Active Tasks (In Progress)</h2>
                </div>
                <span class="text-xs font-medium text-slate-400">{{ $activeCount }} active</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-950/40 text-slate-400 text-xs uppercase border-b border-slate-800 tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Ticket ID</th>
                            <th class="px-5 py-3.5">Company & Asset</th>
                            <th class="px-5 py-3.5">Priority</th>
                            <th class="px-5 py-3.5">Elapsed</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @forelse($myTasks as $task)
                        @php
                            $pColor = match($task->priority) {
                                'P1' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                'P2' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                default => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                            };
                            $ticketCode = 'TCK-' . str_pad($task->id, 4, '0', STR_PAD_LEFT);
                            $assignedMinutes = $task->assigned_at ? max(0, (int) $task->assigned_at->diffInMinutes(now(), false)) : 0;
                            $isSlaWarning = $task->sla_breached || ($task->priority === 'P1' && $assignedMinutes > 120);
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-mono font-semibold text-amber-400">{{ $ticketCode }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $task->category }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-white">{{ $task->company->name ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-1.5">
                                    <i data-lucide="cpu" class="w-3 h-3 text-slate-500 shrink-0"></i>
                                    <span class="truncate max-w-[220px]" title="{{ $task->asset->name ?? 'N/A' }}">{{ $task->asset->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold border {{ $pColor }}">
                                    {{ $task->priority }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($isSlaWarning)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold {{ $task->sla_breached ? 'bg-red-500/15 text-red-400 border border-red-500/30' : 'bg-amber-500/15 text-amber-300 border border-amber-500/30' }}">
                                    <i data-lucide="flame" class="w-3.5 h-3.5 shrink-0"></i>
                                    <span>{{ $formatElapsed($task->assigned_at) }}</span>
                                </span>
                                <div class="text-[10px] font-semibold uppercase tracking-wider {{ $task->sla_breached ? 'text-red-400' : 'text-amber-400' }} mt-1">
                                    {{ $task->sla_breached ? 'SLA Breached' : 'P1 > 2h Alert' }}
                                </div>
                                @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-300">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-500"></i>
                                    <span>{{ $formatElapsed($task->assigned_at) }}</span>
                                </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('tech.tickets.resolve.form', $task->id) }}" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold transition">
                                        <i data-lucide="wrench" class="w-3.5 h-3.5"></i>
                                        <span>Resolve</span>
                                    </a>
                                    <form action="{{ route('tech.tickets.release', $task->id) }}" method="POST" class="inline" onsubmit="return confirm('Release {{ $ticketCode }} back to the Global Open Queue?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 bg-slate-800/80 hover:bg-amber-500/15 text-slate-300 hover:text-amber-400 border border-slate-700 hover:border-amber-500/30 px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer">
                                            <i data-lucide="undo-2" class="w-3.5 h-3.5"></i>
                                            <span>Release</span>
                                        </button>
                                    </form>
                                    <a href="{{ route('tech.tickets.resolve.form', ['ticket' => $task->id, 'escalate' => 1]) }}" title="Escalate Ticket" class="inline-flex items-center justify-center p-1.5 rounded-lg bg-red-500/10 hover:bg-red-600 text-red-400 hover:text-white border border-red-500/25 transition">
                                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="inline-flex flex-col items-center gap-2 text-slate-500">
                                    <i data-lucide="check-circle" class="w-8 h-8 text-slate-700"></i>
                                    <p class="text-sm font-medium text-slate-400">No active tasks right now</p>
                                    <p class="text-xs text-slate-500">Claim an unassigned ticket from the Live Open Queue on the right.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Right Panel (4 cols): Global Open Queue (Live Queue Cards) -->
        <aside class="lg:col-span-4 bg-[#0b1222] border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between bg-[#080d1a]/60">
                <div class="flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <h2 class="text-sm font-semibold text-white">Global Open Queue</h2>
                </div>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                    {{ $openQueueCount }} available
                </span>
            </div>

            <div class="p-4 space-y-3 max-h-[760px] overflow-y-auto">
                @forelse($openQueue as $ticket)
                @php
                    $pColor = match($ticket->priority) {
                        'P1' => 'bg-red-500/15 text-red-400 border-red-500/30',
                        'P2' => 'bg-amber-500/15 text-amber-400 border-amber-500/30',
                        default => 'bg-blue-500/15 text-blue-400 border-blue-500/30',
                    };
                    $catColor = match($ticket->category) {
                        'Network'  => 'bg-cyan-500/10 text-cyan-300 border-cyan-500/20',
                        'Hardware' => 'bg-orange-500/10 text-orange-300 border-orange-500/20',
                        default    => 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20',
                    };
                    $createdMinutes = $ticket->created_at ? max(0, (int) $ticket->created_at->diffInMinutes(now(), false)) : 0;
                    $isQueueUrgent = $ticket->sla_breached || ($ticket->priority === 'P1' && $createdMinutes > 60);
                @endphp
                <div class="bg-[#080d1a] border {{ $isQueueUrgent ? 'border-red-500/35' : 'border-slate-800/90 hover:border-slate-700' }} rounded-xl p-4 space-y-3 transition">
                    <!-- Top Row: ID + Priority + Category + Elapsed -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-mono text-xs font-bold text-white">
                                TCK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold border {{ $pColor }}">
                                {{ $ticket->priority }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-medium border {{ $catColor }}">
                                {{ $ticket->category }}
                            </span>
                        </div>

                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold shrink-0 {{ $isQueueUrgent ? ($ticket->sla_breached ? 'bg-red-500/15 text-red-400 border border-red-500/30' : 'bg-amber-500/15 text-amber-300 border border-amber-500/30') : 'text-slate-400 bg-slate-900 border border-slate-800' }}">
                            <i data-lucide="{{ $isQueueUrgent ? 'alert-circle' : 'clock' }}" class="w-3 h-3"></i>
                            <span>{{ $formatElapsed($ticket->created_at) }}</span>
                        </span>
                    </div>

                    <!-- Company & Asset -->
                    <div class="space-y-1">
                        <div class="text-sm font-semibold text-white truncate" title="{{ $ticket->company->name ?? 'N/A' }}">
                            {{ $ticket->company->name ?? 'N/A' }}
                        </div>
                        <div class="text-xs text-slate-400 flex items-center gap-1.5 truncate" title="{{ $ticket->asset->name ?? 'N/A' }}">
                            <i data-lucide="server" class="w-3 h-3 text-blue-400 shrink-0"></i>
                            <span class="truncate">{{ $ticket->asset->name ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- 2-line Description Preview -->
                    <p class="text-xs text-slate-400 leading-relaxed line-clamp-2" title="{{ $ticket->description }}">
                        {{ $ticket->description }}
                    </p>

                    <!-- Claim Action -->
                    <form action="{{ route('tech.tickets.claim', $ticket->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-blue-600/15 hover:bg-blue-600 text-blue-400 hover:text-white border border-blue-500/30 px-4 py-2 rounded-lg text-xs font-semibold transition cursor-pointer">
                            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                            <span>Assign to Me</span>
                        </button>
                    </form>
                </div>
                @empty
                <div class="py-10 text-center text-slate-500">
                    <i data-lucide="inbox" class="w-8 h-8 text-slate-700 mx-auto mb-2"></i>
                    <p class="text-sm font-medium text-slate-400">Queue is clear!</p>
                    <p class="text-xs text-slate-500 mt-0.5">No unassigned tickets right now.</p>
                </div>
                @endforelse
            </div>
        </aside>

    </div>

</div>
@endsection
