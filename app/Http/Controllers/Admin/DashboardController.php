<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Photo;
use App\Models\Analytic;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEvents = Event::count();
        $totalPhotos = Photo::count();
        $totalDownloads = Photo::sum('downloads');
        $totalScans = Analytic::sum('qr_scans');

        $latestPhotos = Photo::with('event')->latest()->take(5)->get();
        $recentEvents = Event::withCount('photos')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalEvents',
            'totalPhotos',
            'totalDownloads',
            'totalScans',
            'latestPhotos',
            'recentEvents'
        ));
    }
}
