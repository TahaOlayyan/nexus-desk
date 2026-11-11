<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        // 1. نجيب اليوزر اللي مسجل دخول (العميل)
        $user = Auth::user();

        // 2. نجيب الآي دي تبع شركته
        $companyId = $user->company_id;

        // 3. نحسب الإحصائيات (Active Tickets, Resolved, Total Assets)
        $activeTicketsCount = $user->clientTickets()->whereIn('status', ['Open', 'In Progress', 'Escalated'])->count();
        $resolvedTicketsCount = $user->clientTickets()->where('status', 'Resolved')->count();
        $assetsCount = $user->company->assets()->count();

        // 4. نجيب أحدث التذاكر تبعت هاد العميل مع اسم الجهاز المربوط فيها
        $tickets = $user->clientTickets()->with('asset')->latest()->get();

        // 5. نبعت هاي الداتا لشاشة الـ Dashboard
        return view('client.dashboard', compact(
            'activeTicketsCount',
            'resolvedTicketsCount',
            'assetsCount',
            'tickets'
        ));
    }
}
