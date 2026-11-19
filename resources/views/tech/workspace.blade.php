@extends('layouts.tech')

@section('title', 'Technician Workspace')

@section('content')
<div class="space-y-8">

    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-2 shadow-lg">
        <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-2 shadow-lg">
        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Technician Workspace</h1>
            <p class="text-sm text-slate-400 mt-1">Claim open incidents and manage assigned repairs</p>
        </div>
        <div class="flex items-center gap-3 bg-slate-900/60 border border-slate-800 px-4 py-2 rounded-xl">
            <span class="text-xs text-slate-400">My Active Tasks:</span>
            <span class="text-sm font-bold text-amber-400">{{ $myTasks->count() }}</span>
        </div>
    </div>

    <!-- جدول 1: مهام الفني الحالية -->
    <section class="bg-[#0b1222] border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                <h2 class="text-sm font-semibold text-white">My Current Tasks (In Progress)</h2>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-950/40 text-slate-400 text-xs uppercase border-b border-slate-800 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Ticket ID</th>
                        <th class="px-6 py-3.5">Client / Company</th>
                        <th class="px-6 py-3.5">Asset</th>
                        <th class="px-6 py-3.5">Priority</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
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
                    @endphp
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="px-6 py-4 font-mono font-semibold text-amber-400">
                            TCK-{{ str_pad($task->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 font-medium text-white">{{ $task->company->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-slate-300">{{ $task->asset->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold border {{ $pColor }}">
                                {{ $task->priority }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('tech.tickets.resolve.form', $task->id) }}" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-1.5 rounded-lg text-xs font-semibold transition">
                                <i data-lucide="wrench" class="w-3.5 h-3.5"></i>
                                <span>Resolve</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            You have no active tasks right now. Claim one from the queue below.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- جدول 2: الطابور العام -->
    <section class="bg-[#0b1222] border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                <h2 class="text-sm font-semibold text-white">Global Open Queue (Unassigned)</h2>
            </div>
            <span class="text-xs text-slate-400">{{ $openQueue->count() }} available</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-950/40 text-slate-400 text-xs uppercase border-b border-slate-800 tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Ticket ID</th>
                        <th class="px-6 py-3.5">Client / Company</th>
                        <th class="px-6 py-3.5">Asset</th>
                        <th class="px-6 py-3.5">Priority</th>
                        <th class="px-6 py-3.5">Created At</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    @forelse($openQueue as $ticket)
                    @php
                    $pColor = match($ticket->priority) {
                    'P1' => 'bg-red-500/10 text-red-400 border-red-500/20',
                    'P2' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                    default => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                    };
                    @endphp
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="px-6 py-4 font-mono font-semibold text-white">
                            TCK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 font-medium text-white">{{ $ticket->company->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-slate-300">{{ $ticket->asset->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold border {{ $pColor }}">
                                {{ $ticket->priority }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-400 text-xs">{{ $ticket->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('tech.tickets.claim', $ticket->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-blue-600/15 hover:bg-blue-600 text-blue-400 hover:text-white border border-blue-500/30 px-4 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer">
                                    Assign to Me
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            Queue is clear! No unassigned tickets right now.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection