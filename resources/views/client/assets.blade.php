@extends('layouts.client')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight text-white">My Assets</h1>
    <p class="text-sm text-slate-400 mt-1">Infrastructure and devices assigned to {{ Auth::user()->company->name }}</p>
</div>

<div class="bg-[#0b1222] border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-950/40 text-slate-400 text-xs uppercase border-b border-slate-800 tracking-wider">
                <tr>
                    <th class="px-6 py-3.5">Asset Name</th>
                    <th class="px-6 py-3.5">Serial Number</th>
                    <th class="px-6 py-3.5">Type</th>
                    <th class="px-6 py-3.5">Added On</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
                @forelse ($assets as $asset)
                <tr class="hover:bg-slate-800/30 transition">
                    <td class="px-6 py-4 font-medium text-white flex items-center gap-3">
                        <i data-lucide="server" class="w-4 h-4 text-blue-400"></i>
                        {{ $asset->name }}
                    </td>
                    <td class="px-6 py-4 font-mono text-slate-400">{{ $asset->serial_number }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold border bg-slate-800/50 text-slate-300 border-slate-700">Hardware</span>
                    </td>
                    <td class="px-6 py-4 text-slate-400 text-xs">{{ $asset->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">No assets found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection