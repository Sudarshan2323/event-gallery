<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Analytic;
use App\Models\Event;
use App\Models\Photo;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalEvents = Event::count();
        $totalPhotos = Photo::count();
        $totalDownloads = Photo::sum('downloads');
        $totalScans = Analytic::sum('qr_scans');
        $uniqueVisitors = Analytic::whereNotNull('visitor_ip')->distinct('visitor_ip')->count('visitor_ip');

        $topDownloadedPhotos = Photo::with('event')
            ->orderByDesc('downloads')
            ->take(10)
            ->get();

        $topScannedPhotos = Photo::with('event')
            ->withSum('analytics', 'qr_scans')
            ->orderByDesc('analytics_sum_qr_scans')
            ->take(10)
            ->get();

        $topEventsByDownloads = Event::withCount('photos')
            ->withSum('photos', 'downloads')
            ->orderByDesc('photos_sum_downloads')
            ->take(8)
            ->get();

        $recentAnalytics = Analytic::with(['photo.event'])
            ->latest('updated_at')
            ->take(20)
            ->get();

        return view('admin.analytics.index', compact(
            'totalEvents',
            'totalPhotos',
            'totalDownloads',
            'totalScans',
            'uniqueVisitors',
            'topDownloadedPhotos',
            'topScannedPhotos',
            'topEventsByDownloads',
            'recentAnalytics',
        ));
    }
}

