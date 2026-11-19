@extends('layouts.tech')

@section('title', 'Account Settings')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight text-white">Account Settings</h1>
    <p class="text-sm text-slate-400 mt-1">Manage your technician profile and security preferences</p>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-2 shadow-lg max-w-2xl">
    <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex flex-col gap-1 shadow-lg max-w-2xl">
    @foreach($errors->all() as $error)
    <div class="flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
        <span>{{ $error }}</span>
    </div>
    @endforeach
</div>
@endif

<div class="max-w-2xl bg-[#0b1222] border border-slate-800/80 rounded-2xl p-6 shadow-xl space-y-6">

    <!-- Form 1: Profile Picture Upload -->
    <div class="pb-6 border-b border-slate-800">
        <h3 class="text-sm font-semibold text-white mb-4">Profile Picture</h3>
        <form action="{{ route('tech.settings.avatar') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row sm:items-center gap-5">
            @csrf

            @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-xl object-cover border border-slate-700 shrink-0">
            @else
            <div class="w-16 h-16 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white text-xl shadow-lg shadow-blue-900/30 uppercase shrink-0">
                {{ substr($user->name, 0, 2) }}
            </div>
            @endif

            <div class="flex-1 min-w-0">
                <input type="file" name="avatar" accept="image/*" required class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600/10 file:text-blue-400 hover:file:bg-blue-600/20 cursor-pointer">
                <p class="text-xs text-slate-500 mt-2">JPG, PNG or GIF. Max size 2MB.</p>
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition cursor-pointer border border-slate-700 shrink-0">Upload</button>
        </form>
    </div>

    <!-- Form 2: Account Info & Change Password -->
    <form action="{{ route('tech.settings.password') }}" method="POST" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Full Name</label>
                <input type="text" value="{{ $user->name }}" readonly class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-400 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Email Address</label>
                <input type="email" value="{{ $user->email }}" readonly class="w-full bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-400 cursor-not-allowed">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800">
            <h3 class="text-sm font-semibold text-white mb-4">Change Password</h3>
            <div class="space-y-4">
                <div>
                    <input type="password" name="current_password" required placeholder="Current Password" class="w-full bg-[#030712] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <input type="password" name="password" required placeholder="New Password (min. 8 characters)" class="w-full bg-[#030712] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <input type="password" name="password_confirmation" required placeholder="Confirm New Password" class="w-full bg-[#030712] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-xl text-sm font-semibold transition cursor-pointer">Update Password</button>
        </div>
    </form>

</div>
@endsection
