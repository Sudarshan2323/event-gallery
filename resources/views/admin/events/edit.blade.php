@extends('layouts.admin')

@section('title', 'Edit Event')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center space-x-4 mb-6">
        <a href="{{ route('admin.events.show', $event) }}" class="text-gray-500 hover:text-gray-700 bg-white p-2 rounded-md shadow-sm border border-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Event</h2>
            <p class="text-sm text-gray-500">{{ $event->event_name }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="event_name" class="block text-sm font-medium text-gray-700">Event Name <span class="text-red-500">*</span></label>
                    <input type="text" name="event_name" id="event_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" value="{{ old('event_name', $event->event_name) }}" required>
                    @error('event_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="couple_name" class="block text-sm font-medium text-gray-700">Couple/Host Name <span class="text-red-500">*</span></label>
                    <input type="text" name="couple_name" id="couple_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" value="{{ old('couple_name', $event->couple_name) }}" required>
                    @error('couple_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="couple_initials" class="block text-sm font-medium text-gray-700">Couple Initials (Auto)</label>
                    <input type="text" id="couple_initials" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm p-2 border" readonly placeholder="e.g. RP">
                    <p class="mt-1 text-xs text-gray-500">Auto-generated from couple name.</p>
                </div>

                <div>
                    <label for="event_date" class="block text-sm font-medium text-gray-700">Event Date <span class="text-red-500">*</span></label>
                    <input type="date" name="event_date" id="event_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" value="{{ old('event_date', optional(\Carbon\Carbon::parse($event->event_date))->format('Y-m-d')) }}" required>
                    @error('event_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" id="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" value="{{ old('location', $event->location) }}">
                    @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Event Description</label>
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">{{ old('description', $event->description) }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Guest URL (Slug)</p>
                <div class="mt-2 text-sm text-gray-700 break-all">{{ route('guest.event.show', $event->slug) }}</div>
                <p class="mt-1 text-xs text-gray-500">For stability, the slug stays the same even if you change the event name.</p>
            </div>

            <div>
                <label for="organizer_logo" class="block text-sm font-medium text-gray-700">Organizer Logo (Optional)</label>
                <div class="mt-3 flex items-center gap-4">
                    @if($event->organizer_logo)
                        <img src="{{ Storage::url($event->organizer_logo) }}" alt="Organizer logo" class="h-14 w-14 rounded-xl object-cover border border-gray-200 bg-white">
                    @else
                        <div class="h-14 w-14 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                            {{ substr($event->event_name, 0, 1) }}
                        </div>
                    @endif
                    <input id="organizer_logo" name="organizer_logo" type="file" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept="image/jpeg,image/png,image/webp">
                </div>
                @error('organizer_logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function computeInitials(name) {
        if (!name) return '';
        const words = name
            .replace(/[^a-zA-Z\\s]/g, ' ')
            .split(/\\s+/)
            .filter(Boolean);
        return words.slice(0, 2).map(w => w[0].toUpperCase()).join('');
    }

    const coupleNameInput = document.getElementById('couple_name');
    const initialsInput = document.getElementById('couple_initials');
    const syncInitials = () => initialsInput.value = computeInitials(coupleNameInput.value);

    coupleNameInput.addEventListener('input', syncInitials);
    syncInitials();
</script>
@endsection
