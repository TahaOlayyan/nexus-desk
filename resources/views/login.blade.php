<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Desk - Portal Login</title>
    <!-- ربط أيقونة المتصفح (Favicon) لتظهر فوق بالتاب -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-950 flex items-center justify-center p-4">
    <div class="fixed inset-0 z-0 bg-cover bg-center opacity-25" style="background-image: url('{{ asset('images/login-bg.jpg') }}');"></div>

    <div class="relative z-10 w-full max-w-md bg-slate-900/90 border border-slate-800 rounded-2xl p-8 backdrop-blur-md shadow-2xl">
        <div class="text-center mb-6">
            <!-- Logo Image -->
            <img src="{{ asset('images/logo.png') }}" alt="Nexus Desk Logo" class="h-28 mx-auto mb-4 object-contain">

            
            <h1 class="text-2xl font-bold text-white tracking-tight">Nexus Desk Portal</h1>
            <p class="text-sm text-slate-400 mt-1">Sign in to your organization account</p>
        </div>

        @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Email Address</label>
                <input type="email" name="email" required placeholder="e.g., yourname@organization.com" value="{{ old('email') }}" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-blue-500">
                <span class="text-xs text-slate-500 mt-1 block">Enter the registered email provided by your IT department.</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-blue-500">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 rounded-lg text-sm transition shadow-lg shadow-blue-600/20 cursor-pointer">
                Sign In
            </button>
        </form>
    </div>
</body>

</html>
