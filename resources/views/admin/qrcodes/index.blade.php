@extends('layouts.admin')

@section('title', 'QR Codes')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">QR Codes</h2>
            <p class="text-sm text-gray-500">Event QR opens the gallery. Photo QR opens a specific photo.</p>
        </div>
        <form method="POST" action="{{ route('admin.qrcodes.regenerate-all') }}">
            @csrf
            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition">
                Regenerate All (Switch to Live Domain)
            </button>
        </form>

    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Event QR Codes</h3>
            <span class="text-xs text-gray-500">{{ $events->total() }} events</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Guest URL</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">QR</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $event->event_name }}</div>
                                <div class="text-xs text-gray-500">{{ $event->couple_name }} &bull; {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('guest.event.show', $event->slug) }}" target="_blank" class="text-sm text-indigo-700 hover:underline break-all">{{ route('guest.event.show', $event->slug) }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($event->qr_code_path)
                                    <img src="{{ Storage::url($event->qr_code_path) }}" alt="Event QR" class="h-16 w-16 bg-white rounded-lg border border-gray-200 p-1">
                                @else
                                    <span class="text-xs font-semibold text-red-600">Missing</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" class="px-3 py-2 text-xs font-semibold rounded-lg bg-white border border-gray-200 hover:bg-gray-50"
                                        onclick="navigator.clipboard.writeText('{{ route('guest.event.show', $event->slug) }}')">
                                        Copy Link
                                    </button>

                                    @if($event->qr_code_path)
                                        <a href="{{ route('admin.qrcodes.download') }}?path={{ urlencode($event->qr_code_path) }}" class="px-3 py-2 text-xs font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                                            Download QR
                                        </a>
                                    @endif

                                    <form method="POST" action="{{ route('admin.qrcodes.events.regenerate', $event) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 text-xs font-semibold rounded-lg bg-gray-900 text-white hover:bg-gray-800">
                                            Regenerate
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">No events yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $events->links() }}
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Photo QR Codes</h3>
            <span class="text-xs text-gray-500">{{ $photos->total() }} photos</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Photo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">QR</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($photos as $photo)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">#{{ $photo->id }}</div>
                                <div class="text-xs text-gray-500">{{ $photo->downloads }} downloads</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $photo->event?->event_name }}</div>
                                <div class="text-xs text-gray-500">{{ $photo->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($photo->qr_code_path)
                                    <img src="{{ Storage::url($photo->qr_code_path) }}" alt="Photo QR" class="h-16 w-16 bg-white rounded-lg border border-gray-200 p-1">
                                @else
                                    <span class="text-xs font-semibold text-red-600">Missing</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('guest.photo.show', $photo->id) }}" target="_blank" class="px-3 py-2 text-xs font-semibold rounded-lg bg-white border border-gray-200 hover:bg-gray-50">
                                        Open
                                    </a>
                                    <button type="button" class="px-3 py-2 text-xs font-semibold rounded-lg bg-white border border-gray-200 hover:bg-gray-50"
                                        onclick="navigator.clipboard.writeText('{{ route('guest.photo.show', $photo->id) }}')">
                                        Copy Link
                                    </button>
                                    @if($photo->qr_code_path)
                                        <a href="{{ route('admin.qrcodes.download') }}?path={{ urlencode($photo->qr_code_path) }}" class="px-3 py-2 text-xs font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                                            Download QR
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.qrcodes.photos.regenerate', $photo) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 text-xs font-semibold rounded-lg bg-gray-900 text-white hover:bg-gray-800">
                                            Regenerate
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">No photos uploaded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $photos->links() }}
        </div>
    </div>
</div>
@endsection

