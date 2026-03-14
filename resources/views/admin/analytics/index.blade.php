@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Analytics</h2>
        <p class="text-sm text-gray-500">Engagement overview across all events and photos.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Events</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalEvents) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Photos</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalPhotos) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Downloads</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalDownloads) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">QR Scans</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalScans) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Unique Visitors</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($uniqueVisitors) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Top Downloaded Photos</h3>
                <p class="text-sm text-gray-500">Highest download counts (all time)</p>
            </div>
            <div class="p-6 space-y-4">
                @php $maxDl = max(1, (int) ($topDownloadedPhotos->max('downloads') ?? 1)); @endphp
                @forelse($topDownloadedPhotos as $photo)
                    @php $w = (int) round(($photo->downloads / $maxDl) * 100); @endphp
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 flex-shrink-0">
                            <img src="{{ Storage::url($photo->image_path) }}" alt="Photo {{ $photo->id }}" class="h-full w-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $photo->event?->event_name }} <span class="text-gray-400 font-normal">#{{ $photo->id }}</span></p>
                                <p class="text-xs font-semibold text-gray-700">{{ number_format($photo->downloads) }}</p>
                            </div>
                            <div class="mt-2 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $w }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No downloads yet.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Top QR Scanned Photos</h3>
                <p class="text-sm text-gray-500">Most scanned photo links (all time)</p>
            </div>
            <div class="p-6 space-y-4">
                @php $maxScan = max(1, (int) ($topScannedPhotos->max('analytics_sum_qr_scans') ?? 1)); @endphp
                @forelse($topScannedPhotos as $photo)
                    @php $scanCount = (int) ($photo->analytics_sum_qr_scans ?? 0); $w = (int) round(($scanCount / $maxScan) * 100); @endphp
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 flex-shrink-0">
                            <img src="{{ Storage::url($photo->image_path) }}" alt="Photo {{ $photo->id }}" class="h-full w-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $photo->event?->event_name }} <span class="text-gray-400 font-normal">#{{ $photo->id }}</span></p>
                                <p class="text-xs font-semibold text-gray-700">{{ number_format($scanCount) }}</p>
                            </div>
                            <div class="mt-2 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-orange-500 rounded-full" style="width: {{ $w }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No scans yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Top Events</h3>
                <p class="text-sm text-gray-500">By total photo downloads</p>
            </div>
            <div class="p-6 space-y-3">
                @php $maxEventDl = max(1, (int) ($topEventsByDownloads->max('photos_sum_downloads') ?? 1)); @endphp
                @forelse($topEventsByDownloads as $event)
                    @php $eventDl = (int) ($event->photos_sum_downloads ?? 0); $w = (int) round(($eventDl / $maxEventDl) * 100); @endphp
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $event->event_name }}</p>
                            <p class="text-xs font-semibold text-gray-700">{{ number_format($eventDl) }}</p>
                        </div>
                        <div class="mt-2 h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-600 rounded-full" style="width: {{ $w }}%"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">{{ $event->photos_count }} photos</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No data yet.</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Recent Activity</h3>
                <p class="text-sm text-gray-500">Latest updates by visitor IP</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Photo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Visitor IP</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Downloads</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Scans</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentAnalytics as $row)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    @if($row->photo_id)
                                        <a href="{{ route('guest.photo.show', $row->photo_id) }}" target="_blank" class="hover:underline">#{{ $row->photo_id }}</a>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $row->photo?->event?->event_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $row->visitor_ip ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                    {{ number_format($row->downloads) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                    {{ number_format($row->qr_scans) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                    {{ $row->updated_at?->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">No activity yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

