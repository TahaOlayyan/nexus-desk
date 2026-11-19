<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::whereHas('assets')
            ->whereHas('users', fn ($query) => $query->where('role', 'client'))
            ->with([
                'assets' => fn ($query) => $query->orderBy('id'),
                'users'  => fn ($query) => $query->where('role', 'client')->orderBy('id'),
            ])
            ->orderBy('id')
            ->get();

        $techs = User::where('role', 'tech')->orderBy('id')->get();

        if ($companies->isEmpty()) {
            $this->command?->warn('TicketSeeder skipped: No companies found with both assets and client users.');
            return;
        }

        if ($techs->isEmpty()) {
            $this->command?->warn('TicketSeeder skipped: No technician users (role = tech) found in database.');
            return;
        }

        $blueprints = [
            // 10 Open Tickets (Unassigned, varied timestamps from 20m ago to 2 days ago)
            [
                'category' => 'Network',
                'priority' => 'P1',
                'status' => 'Open',
                'description' => 'BGP peering session flapping intermittently on primary WAN uplink causing packet loss across branch ATMs.',
                'sla_breached' => true,
                'created_mins_ago' => 240, // 4 hours ago
            ],
            [
                'category' => 'Hardware',
                'priority' => 'P1',
                'status' => 'Open',
                'description' => 'Redundant power supply unit #2 failed on core rack server; amber chassis fault LED blinking continuously.',
                'sla_breached' => false,
                'created_mins_ago' => 90, // 90 mins ago (triggers P1 > 1h alert in queue)
            ],
            [
                'category' => 'Software',
                'priority' => 'P1',
                'status' => 'Open',
                'description' => 'Production ERP database reporting service throwing HTTP 503 timeouts during end-of-day batch settlement.',
                'sla_breached' => true,
                'created_mins_ago' => 360, // 6 hours ago
            ],
            [
                'category' => 'Network',
                'priority' => 'P2',
                'status' => 'Open',
                'description' => 'Guest Wi-Fi captive portal failing to issue DHCP leases on Floor 3 conference wing.',
                'sla_breached' => false,
                'created_mins_ago' => 20, // 20 mins ago
            ],
            [
                'category' => 'Hardware',
                'priority' => 'P2',
                'status' => 'Open',
                'description' => 'High-volume branch check scanner jamming repeatedly and reporting optical sensor calibration error.',
                'sla_breached' => false,
                'created_mins_ago' => 135, // 2h 15m ago
            ],
            [
                'category' => 'Software',
                'priority' => 'P2',
                'status' => 'Open',
                'description' => 'Active Directory SSO sync delayed by 20+ minutes for newly onboarded finance department accounts.',
                'sla_breached' => false,
                'created_mins_ago' => 420, // 7 hours ago
            ],
            [
                'category' => 'Network',
                'priority' => 'P2',
                'status' => 'Open',
                'description' => 'Site-to-site SSL VPN latency spiking above 350ms for remote treasury analysts.',
                'sla_breached' => false,
                'created_mins_ago' => 780, // 13 hours ago
            ],
            [
                'category' => 'Hardware',
                'priority' => 'P3',
                'status' => 'Open',
                'description' => 'Secondary thermal receipt printer at teller counter 4 producing faded barcodes after toner replacement.',
                'sla_breached' => false,
                'created_mins_ago' => 45, // 45 mins ago
            ],
            [
                'category' => 'Software',
                'priority' => 'P3',
                'status' => 'Open',
                'description' => 'Scheduled PDF export macro in compliance portal crashes when generating quarterly audit archive.',
                'sla_breached' => false,
                'created_mins_ago' => 1440, // 1 day ago
            ],
            [
                'category' => 'Network',
                'priority' => 'P3',
                'status' => 'Open',
                'description' => 'SNMP trap notifications from branch access switch not forwarding to central NOC monitoring dashboard.',
                'sla_breached' => false,
                'created_mins_ago' => 2880, // 2 days ago
            ],

            // 4 In Progress Tickets (Assigned to existing technicians with realistic assigned_at)
            [
                'category' => 'Network',
                'priority' => 'P1',
                'status' => 'In Progress',
                'description' => 'Core firewall HA failover triggered unexpectedly; secondary node running hot at 88% CPU utilization.',
                'sla_breached' => false,
                'created_mins_ago' => 210,  // 3h 30m ago
                'assigned_mins_ago' => 155, // 2h 35m ago (triggers P1 > 2h SLA warning pill)
            ],
            [
                'category' => 'Hardware',
                'priority' => 'P2',
                'status' => 'In Progress',
                'description' => 'SAN storage array drive bay 6 showing predictive SMART failure warning during nightly backup window.',
                'sla_breached' => false,
                'created_mins_ago' => 180,
                'assigned_mins_ago' => 50,
            ],
            [
                'category' => 'Software',
                'priority' => 'P2',
                'status' => 'In Progress',
                'description' => 'Endpoint security agent failing signature updates across 14 branch teller workstations.',
                'sla_breached' => false,
                'created_mins_ago' => 260,
                'assigned_mins_ago' => 35,
            ],
            [
                'category' => 'Network',
                'priority' => 'P3',
                'status' => 'In Progress',
                'description' => 'VoIP desk phones on VLAN 30 experiencing one-way audio when dialing external SIP trunks.',
                'sla_breached' => false,
                'created_mins_ago' => 1440, // 1 day ago
                'assigned_mins_ago' => 110,
            ],

            // 4 Resolved Tickets (With root_cause, actions_taken, worklog_notes & realistic resolution durations)
            [
                'category' => 'Network',
                'priority' => 'P1',
                'status' => 'Resolved',
                'description' => 'Trunk link between distribution switch and DMZ firewall dropped after scheduled maintenance window.',
                'root_cause' => 'Degraded LC-LC fiber patch cord and VLAN 120 missing from trunk allowed list.',
                'actions_taken' => [
                    'Replaced Faulty Cable / Component',
                    'Reconfigured Network / VLAN',
                    'Ran Diagnostic Health Check',
                ],
                'worklog_notes' => 'Inspected SFP+ optical Rx levels (-24 dBm). Replaced damaged fiber patch cable, restored VLAN 120 tagging on Port-Channel 4, and verified 0% packet loss over 15 minutes.',
                'sla_breached' => false,
                'created_mins_ago' => 1500,  // ~1 day ago
                'assigned_mins_ago' => 1480,
                'resolved_mins_ago' => 1438, // 42m resolution
            ],
            [
                'category' => 'Hardware',
                'priority' => 'P2',
                'status' => 'Resolved',
                'description' => 'Branch rack UPS reporting battery runtime degradation and intermittent audible alarm.',
                'root_cause' => 'Loose battery cartridge terminal and outdated UPS management firmware.',
                'actions_taken' => [
                    'On-Site Physical Inspection',
                    'Updated Firmware / Drivers',
                    'Ran Diagnostic Health Check',
                ],
                'worklog_notes' => 'Performed physical rack inspection, reseated UPS battery pack connectors, upgraded network management card firmware to v7.2.4, and passed full self-test with 42 min runtime.',
                'sla_breached' => false,
                'created_mins_ago' => 2900,
                'assigned_mins_ago' => 2860,
                'resolved_mins_ago' => 2780, // 1h 20m resolution
            ],
            [
                'category' => 'Software',
                'priority' => 'P2',
                'status' => 'Resolved',
                'description' => 'Internal ticketing webhook worker stuck in retry loop causing delayed email notifications.',
                'root_cause' => 'Corrupted Redis queue lock and stale application cache after minor patch.',
                'actions_taken' => [
                    'Restarted Hardware / Service',
                    'Cleared Cache / Reset Application',
                ],
                'worklog_notes' => 'Flushed stale queue lock keys, cleared compiled application config cache, restarted supervisor worker pool, and confirmed live SMTP alerts delivering under 3 seconds.',
                'sla_breached' => false,
                'created_mins_ago' => 4320,
                'assigned_mins_ago' => 4290,
                'resolved_mins_ago' => 4235, // 55m resolution
            ],
            [
                'category' => 'Hardware',
                'priority' => 'P3',
                'status' => 'Resolved',
                'description' => 'Conference room docking station failing to detect dual 4K DisplayPort monitors.',
                'root_cause' => 'Outdated Thunderbolt controller firmware and faulty DisplayPort cable.',
                'actions_taken' => [
                    'Replaced Faulty Cable / Component',
                    'Updated Firmware / Drivers',
                ],
                'worklog_notes' => 'Updated docking station Thunderbolt firmware to latest enterprise release, swapped bent DisplayPort cable on Monitor B, and validated dual 60Hz output.',
                'sla_breached' => false,
                'created_mins_ago' => 5760,
                'assigned_mins_ago' => 5720,
                'resolved_mins_ago' => 5655, // 1h 05m resolution
            ],

            // 2 Escalated Tickets (Assigned with [ESCALATED] notes & assigned_at)
            [
                'category' => 'Network',
                'priority' => 'P1',
                'status' => 'Escalated',
                'description' => 'Intermittent MPLS circuit blackholing between Amman HQ and Aqaba regional hub.',
                'escalation_reason' => 'Confirmed clean interface counters and optics on customer edge router; traceroute drops inside ISP MPLS core. Escalating to Tier 2 Network Architect and carrier NOC.',
                'sla_breached' => true,
                'created_mins_ago' => 960,
                'assigned_mins_ago' => 900,
            ],
            [
                'category' => 'Hardware',
                'priority' => 'P2',
                'status' => 'Escalated',
                'description' => 'Primary virtualization host throwing uncorrectable ECC memory machine-check exceptions.',
                'escalation_reason' => 'DIMM slots A2 and B2 continue logging hardware ECC errors after reseating modules; requires vendor RMA motherboard replacement and cluster VM migration approval.',
                'sla_breached' => false,
                'created_mins_ago' => 1680,
                'assigned_mins_ago' => 1560,
            ],
        ];

        $techAssignmentIndex = 0;

        foreach ($blueprints as $index => $item) {
            $company = $companies[$index % $companies->count()];

            // Prefer an asset matching the ticket category; otherwise rotate through the company's assets
            $matchingAssets = $company->assets->where('category', $item['category'])->values();
            $assetPool = $matchingAssets->isNotEmpty() ? $matchingAssets : $company->assets->values();
            $asset = $assetPool[$index % $assetPool->count()];

            $clientPool = $company->users->values();
            $client = $clientPool[$index % $clientPool->count()];

            $tech = null;
            $assignedAt = null;
            $resolvedAt = null;
            $worklogNotes = $item['worklog_notes'] ?? null;

            if ($item['status'] !== 'Open') {
                $tech = $techs[$techAssignmentIndex % $techs->count()];
                $techAssignmentIndex++;

                $assignedAt = now()->subMinutes($item['assigned_mins_ago'] ?? 60);

                if ($item['status'] === 'Resolved') {
                    $resolvedAt = now()->subMinutes($item['resolved_mins_ago'] ?? 15);
                } elseif ($item['status'] === 'Escalated') {
                    $worklogNotes = '[ESCALATED BY ' . $tech->name . ']: ' . $item['escalation_reason'];
                }
            }

            $createdAt = now()->subMinutes($item['created_mins_ago']);

            $ticket = new Ticket([
                'company_id'    => $company->id,
                'asset_id'      => $asset->id,
                'client_id'     => $client->id,
                'tech_id'       => $tech?->id,
                'category'      => $item['category'],
                'priority'      => $item['priority'],
                'status'        => $item['status'],
                'description'   => $item['description'],
                'root_cause'    => $item['root_cause'] ?? null,
                'actions_taken' => $item['actions_taken'] ?? null,
                'worklog_notes' => $worklogNotes,
                'sla_breached'  => $item['sla_breached'] ?? false,
                'assigned_at'   => $assignedAt,
                'resolved_at'   => $resolvedAt,
            ]);

            $ticket->created_at = $createdAt;
            $ticket->updated_at = $resolvedAt ?? $assignedAt ?? $createdAt;
            $ticket->save();
        }

        $this->command?->info('TicketSeeder: Created 20 realistic enterprise IT tickets (10 Open, 4 In Progress, 4 Resolved, 2 Escalated).');
    }
}
