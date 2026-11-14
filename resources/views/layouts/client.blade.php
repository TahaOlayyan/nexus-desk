<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeskHub - Client Area</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-[#030712] text-slate-100 min-h-screen selection:bg-blue-600 selection:text-white">
    <div class="flex flex-col md:flex-row min-h-screen">

        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-[#080d1a] border-b md:border-b-0 md:border-r border-slate-800/80 p-5 flex flex-col justify-between shrink-0">
            <div>
                <!-- معلومات العميل والشركة ديناميكية -->
                <div class="flex items-center gap-3 px-2 py-2 bg-slate-900/40 rounded-xl border border-slate-800">
                    <!-- فحص: إذا في صورة اعرضها، إذا لأ اعرض الحرفين -->
                    @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Profile" class="w-10 h-10 rounded-lg object-cover shadow-lg shadow-emerald-900/30 border border-slate-700">
                    @else
                    <div class="w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center font-bold text-white shadow-lg shadow-emerald-900/30">
                        {{ substr(Auth::user()->company->name, 0, 2) }}
                    </div>
                    @endif
                    <div>
                        <strong class="block text-white text-sm font-semibold tracking-tight">{{ Auth::user()->company->name }}</strong>
                        <span class="text-xs text-slate-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ Auth::user()->name }}
                        </span>
                    </div>
                </div>

                <nav class="mt-8 flex flex-row md:flex-col gap-1.5 overflow-x-auto">
                    <a href="{{ route('client.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('client.dashboard') ? 'bg-blue-600/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('client.assets') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('client.assets') ? 'bg-blue-600/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <i data-lucide="server" class="w-4 h-4"></i>
                        <span>My Assets</span>
                    </a>
                    <a href="{{ route('client.settings') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition text-sm font-medium {{ request()->routeIs('client.settings') ? 'bg-blue-600/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                        <span>Settings</span>
                    </a>
                </nav>
            </div>

            <!-- زر تسجيل الخروج -->
            <div class="hidden md:block pt-4 border-t border-slate-800/80">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-red-400 hover:bg-red-500/10 transition cursor-pointer">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- المحتوى المتغير لكل صفحة -->
        <main class="flex-1 p-6 md:p-10 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <!-- مكان لوضع سكريبتات إضافية للصفحات (زي الـ Modal) -->
    @stack('scripts')

    <script>
        lucide.createIcons();
    </script>
</body>

</html>