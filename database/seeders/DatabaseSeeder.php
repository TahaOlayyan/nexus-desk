<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        $arabBank = Company::create([
            'name' => 'Arab Bank',
            'contract_tier' => 'Gold',
            'sla_response_minutes' => 30,
        ]);

        $zain = Company::create([
            'name' => 'Zain Jordan',
            'contract_tier' => 'Silver',
            'sla_response_minutes' => 120,
        ]);

        $orange = Company::create([
            'name' => 'Orange Jordan',
            'contract_tier' => 'Bronze',
            'sla_response_minutes' => 240,
        ]);

        $nova = Company::create([
            'name' => 'Nova',
            'contract_tier' => 'Bronze',
            'sla_response_minutes' => 240,
        ]);

        $admin = User::create([
            'company_id' => null,
            'name' => 'Taha Olayan (Super Admin)',
            'email' => 'admin@nexusdesk.com',
            'password' => $password,
            'role' => 'admin',
            'is_available' => true,
        ]);

        $tech1 = User::create([
            'company_id' => null,
            'name' => 'Zaid Al-Masri (Senior Tech)',
            'email' => 'tech1@nexusdesk.com',
            'password' => $password,
            'role' => 'tech',
            'is_available' => true,
        ]);

        $tech2 = User::create([
            'company_id' => null,
            'name' => 'Mohammad Tariq (Network Tech)',
            'email' => 'tech2@nexusdesk.com',
            'password' => $password,
            'role' => 'tech',
            'is_available' => false,
        ]);

        $arabBankClient = User::create([
            'company_id' => $arabBank->id,
            'name' => 'Ahmad Mansour (Arab Bank IT)',
            'email' => 'client@arabbank.com',
            'password' => $password,
            'role' => 'client',
            'is_available' => true,
        ]);

        $zainClient = User::create([
            'company_id' => $zain->id,
            'name' => 'Sara Haddad (Zain NOC)',
            'email' => 'client@zain.com',
            'password' => $password,
            'role' => 'client',
            'is_available' => true,
        ]);

        $abSwitch = Asset::create([
            'company_id' => $arabBank->id,
            'name' => 'Cisco Catalyst 9300 Core Switch',
            'serial_number' => 'AB-CSCO-9300-01',
            'category' => 'Network',
            'status' => 'Active',
        ]);

        $zainFirewall = Asset::create([
            'company_id' => $zain->id,
            'name' => 'FortiGate 600F Next-Gen Firewall',
            'serial_number' => 'ZN-FGT-600F-01',
            'category' => 'Network',
            'status' => 'Active',
        ]);

        Ticket::create([
            'company_id' => $arabBank->id,
            'asset_id' => $abSwitch->id,
            'client_id' => $arabBankClient->id,
            'tech_id' => null,
            'category' => 'Network',
            'priority' => 'P1',
            'status' => 'Open',
            'description' => 'Core switch port flapping on VLAN 40 affecting payment gateway traffic.',
            'sla_breached' => true,
        ]);

        Ticket::create([
            'company_id' => $zain->id,
            'asset_id' => $zainFirewall->id,
            'client_id' => $zainClient->id,
            'tech_id' => $tech1->id,
            'category' => 'Network',
            'priority' => 'P2',
            'status' => 'In Progress',
            'description' => 'IPsec VPN tunnel dropping packets between main HQ and backup data center.',
            'sla_breached' => false,
            'assigned_at' => now()->subMinutes(45),
        ]);
    }
}
