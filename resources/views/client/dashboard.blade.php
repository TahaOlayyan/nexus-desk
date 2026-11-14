@extends('layouts.client')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-white">Branch Overview</h1>
        <p class="text-sm text-slate-400 mt-1">Incident tracking and infrastructure requests</p>
    </div>
    <button id="openModalBtn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-blue-600/25 cursor-pointer">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span>Open New Ticket</span>
    </button>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center justify-between shadow-lg">
    <div class="flex items-center gap-2">
        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
        <span>{{ session('success') }}</span>
    </div>
    <span class="text-xs text-emerald-500/80">Just now</span>
</div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Active Tickets</span>
            <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-blue-400 mt-3">{{ $activeTicketsCount }}</div>
    </div>

    <div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Resolved</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-emerald-400 mt-3">{{ $resolvedTicketsCount }}</div>
    </div>

    <div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Assets</span>
            <div class="w-8 h-8 rounded-lg bg-slate-500/10 text-slate-300 flex items-center justify-center">
                <i data-lucide="server" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-white mt-3">{{ $assetsCount }}</div>
    </div>
</div>

<div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
    <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-white">Recent Branch Tickets</h2>
        <span class="text-xs text-slate-400">Auto-synced</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-950/40 text-slate-400 text-xs uppercase border-b border-slate-800 tracking-wider">
                <tr>
                    <th class="px-6 py-3.5">Ticket ID</th>
                    <th class="px-6 py-3.5">Asset</th>
                    <th class="px-6 py-3.5">Priority</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5">Created At</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
                @forelse ($tickets as $ticket)
                <tr class="hover:bg-slate-800/30 transition">
                    <td class="px-6 py-4 font-mono font-semibold text-slate-300">TCK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 font-medium text-white">{{ $ticket->asset->name }}</td>
                    <td class="px-6 py-4">
                        @if($ticket->priority == 'P1')
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold border bg-red-500/10 text-red-400 border-red-500/20">P1</span>
                        @elseif($ticket->priority == 'P2')
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold border bg-amber-500/10 text-amber-400 border-amber-500/20">P2</span>
                        @else
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold border bg-slate-500/10 text-slate-400 border-slate-500/20">P3</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium
                        {{ $ticket->status == 'Open' ? 'text-blue-400' : ($ticket->status == 'In Progress' ? 'text-amber-400' : 'text-emerald-400') }}">
                        {{ $ticket->status }}
                    </td>
                    <td class="px-6 py-4 text-slate-400 text-xs">{{ $ticket->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">No tickets found for your branch.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="ticketModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#0b1222] border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-800">
            <h3 class="text-base font-bold text-white">Open New Ticket</h3>
            <span class="text-xs text-slate-400">Branch incident</span>
        </div>

        <form action="{{ route('client.tickets.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Category</label>
                <select name="category" required class="w-full bg-[#030712] border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Select category...</option>
                    <option value="Hardware">Hardware</option>
                    <option value="Software">Software</option>
                    <option value="Network">Network</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Asset</label>
                <select name="asset_id" required class="w-full bg-[#030712] border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-blue-500">
                    <option value="" disabled selected>Select an asset...</option>

                    @foreach($assets as $asset)
                    <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->serial_number }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Priority</label>
                <select name="priority" required class="w-full bg-[#030712] border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-blue-500">
                    <option value="P1">P1 - Critical</option>
                    <option value="P2">P2 - Medium</option>
                    <option value="P3" selected>P3 - Standard</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Description</label>
                <textarea name="description" rows="3" required placeholder="Detailed failure symptoms..." class="w-full bg-[#030712] border border-slate-800 rounded-xl px-3.5 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" id="closeModalBtn" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-400 hover:text-white cursor-pointer">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-xl text-sm font-semibold transition cursor-pointer">Submit Ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById("ticketModal");
    const openBtn = document.getElementById("openModalBtn");
    const closeBtn = document.getElementById("closeModalBtn");

    openBtn.onclick = () => modal.classList.remove("hidden");
    closeBtn.onclick = () => modal.classList.add("hidden");
    window.onclick = (e) => {
        if (e.target === modal) modal.classList.add("hidden");
    };
</script>
@endpush