<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Desk - @yield('title', 'Technician Workspace')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-[#030712] text-slate-100 min-h-screen">
    <div class="flex flex-col md:flex-row min-h-screen">

        <aside class="w-full md:w-64 bg-[#080d1a] border-b md:border-b-0 md:border-r border-slate-800/80 p-5 flex flex-col justify-between shrink-0">
            <div>
                <div class="flex items-center justify-between md:block">
                    <div class="flex items-center gap-3 px-2 py-2 bg-slate-900/40 rounded-xl border border-slate-800 flex-1">
                        @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-10 h-10 rounded-lg object-cover border border-slate-700">
                        @else
                        <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white shadow-lg shadow-blue-900/30 uppercase">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        @endif
                        <div class="min-w-0">
                            <strong class="block text-white text-sm font-semibold tracking-tight truncate">{{ Auth::user()->name }}</strong>
                            <span class="text-xs text-slate-400 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Field Technician
                            </span>
                        </div>
                    </div>
                </div>

                <nav class="mt-6 md:mt-8 flex flex-row md:flex-col gap-1.5 overflow-x-auto">
                    <a href="{{ route('tech.workspace') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm whitespace-nowrap transition {{ request()->routeIs('tech.workspace') ? 'bg-blue-600/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <i data-lucide="layers" class="w-4 h-4"></i>
                        <span>Workspace</span>
                    </a>
                </nav>
            </div>

            <div class="mt-4 md:mt-0 pt-4 border-t border-slate-800/80">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-red-400 hover:bg-red-500/10 transition cursor-pointer">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-10 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>