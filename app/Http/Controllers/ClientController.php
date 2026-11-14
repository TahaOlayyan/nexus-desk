<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    // 1. دالة عرض الداشبورد وجلب البيانات
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // حساب الإحصائيات
        $activeTicketsCount = $user->clientTickets()->whereIn('status', ['Open', 'In Progress', 'Escalated'])->count();
        $resolvedTicketsCount = $user->clientTickets()->where('status', 'Resolved')->count();
        $assetsCount = $user->company->assets()->count();

        // جلب التذاكر والأجهزة للشركة
        $tickets = $user->clientTickets()->with('asset')->latest()->get();
        $assets = $user->company->assets;

        return view('client.dashboard', compact(
            'activeTicketsCount',
            'resolvedTicketsCount',
            'assetsCount',
            'tickets',
            'assets'
        ));
    }

    // 2. دالة تخزين التذكرة الجديدة القادمة من الـ Modal
    public function storeTicket(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // التحقق من المدخلات
        $request->validate([
            'category' => 'required|string',
            'asset_id' => 'required|exists:assets,id',
            'priority' => 'required|in:P1,P2,P3',
            'description' => 'required|string|max:1000',
        ]);

        // إنشاء التذكرة وربطها بالداتابيس
        \App\Models\Ticket::create([
            'company_id' => $user->company_id,
            'asset_id' => $request->asset_id,
            'client_id' => $user->id,
            'tech_id' => null,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'Open',
            'description' => $request->description,
            'sla_breached' => false,
        ]);

        return redirect()->route('client.dashboard')->with('success', 'Ticket created successfully!');
    }

    // صفحة أصول الشركة (الأجهزة)
    public function assets()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $assets = $user->company->assets()->latest()->get();

        return view('client.assets', compact('assets'));
    }

    // صفحة الإعدادات (الملف الشخصي)
    public function settings()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('client.settings', compact('user'));
    }
    // دالة تحديث الباسوورد
    public function updatePassword(Request $request)
    {
        // 1. التحقق: لازم الباسوورد القديم يكون صح، والجديد لا يقل عن 8 أحرف ومطابق للتأكيد
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. تحديث الباسوورد وتشفيره
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // 3. نرجعه لنفس الصفحة مع رسالة نجاح
        return back()->with('success', 'Password updated successfully!');
    }

    // دالة رفع الصورة الشخصية
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2 ميجا كحد أقصى
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // حفظ الصورة في مجلد storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');

            $user->update([
                'avatar' => $path
            ]);
        }

        return back()->with('success', 'Profile photo updated successfully!');
    }
}
