<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\Organization;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'Admin';
        $regionId = $user->region_id;

        // 1. Scoped Member Queries
        $memberQuery = Member::when(! $isAdmin, fn ($q) => $q->where('region_id', $regionId));

        $totalMembers = (clone $memberQuery)->count();
        $recentMembers = (clone $memberQuery)->with('region')->latest()->take(10)->get();

        // 2. Scoped Active Organizations Count
        $activeRegionsCount = Organization::when(! $isAdmin, fn ($q) => $q->where('region_id', $regionId))
            ->where('is_active', true)
            ->count();

        // 3. Announcements Scoping
        $announcements = Announcement::with('user')
            ->when(! $isAdmin, function ($query) use ($regionId) {
                $query->where(function ($sub) use ($regionId) {
                    $sub->whereNull('region_id') // Global Announcements
                        ->orWhere('region_id', $regionId); // Regional Announcements
                });
            })
            ->latest()
            ->take(5)
            ->get();

        // 4. Top Stats & Scoped Audit Logs
        $queueCount = DB::table('jobs')->count();

        $auditLogs = AuditLog::with('user:id,name,email,region_id')
            ->when(! $isAdmin, function ($query) use ($regionId) {
                $query->whereHas('user', fn ($q) => $q->where('region_id', $regionId));
            })
            ->latest()
            ->take(5)
            ->get();

        // 5. Chart Data (Aggregate by Region)
        $chartData = Member::join('regions', 'members.region_id', '=', 'regions.id')
            ->select('regions.name', DB::raw('count(members.id) as total'))
            ->when(! $isAdmin, fn ($q) => $q->where('members.region_id', $regionId))
            ->groupBy('regions.id', 'regions.name')
            ->get();

        // 6. Regional Progress Stats (Query-level Scoped)
        $regionalStats = Region::query()
            ->when(! $isAdmin, fn ($q) => $q->where('id', $regionId))
            ->withCount(['members' => function ($q) use ($isAdmin, $regionId) {
                if (! $isAdmin) {
                    $q->where('region_id', $regionId);
                }
            }])
            ->get()
            ->map(function ($reg) use ($totalMembers) {
                return (object) [
                    'name' => $reg->name,
                    'percentage' => $totalMembers > 0 ? round(($reg->members_count / $totalMembers) * 100) : 0,
                ];
            });

        return view('dashboard', [
            'total_members'        => $totalMembers,
            'announcements'        => $announcements,
            'queue_count'          => $queueCount,
            'audit_logs'           => $auditLogs,
            'recent_members'       => $recentMembers,
            'chartLabels'          => $chartData->pluck('name'),
            'chartCounts'          => $chartData->pluck('total'),
            'active_regions_count' => $activeRegionsCount,
            'regional_stats'       => $regionalStats,

            'activities' => collect([
                (object) ['title' => 'ICT Benchmarking - Field Office VIII', 'location' => 'Palo, Leyte', 'date_range' => 'Mar 10 - 15', 'is_ongoing' => true],
                (object) ['title' => 'Siglat 4-H Regional Summit', 'location' => 'MLU Campus', 'date_range' => 'April 02', 'is_ongoing' => false],
            ]),
        ]);
    }
}