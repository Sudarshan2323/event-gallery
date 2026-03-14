@extends('layouts.admin')

@section('title', 'Create Event')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center space-x-4 mb-6">
        <a href="{{ route('admin.events.index') }}" class="text-gray-500 hover:text-gray-700 bg-white p-2 rounded-md shadow-sm border border-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-900">Create New Event</h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Form Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Event Name -->
                <div>
                    <label for="event_name" class="block text-sm font-medium text-gray-700">Event Name <span class="text-red-500">*</span></label>
                    <input type="text" name="event_name" id="event_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" value="{{ old('event_name') }}" required placeholder="e.g. Rahul & Priya Wedding">
                    @error('event_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Couple Name -->
                <div>
                    <label for="couple_name" class="block text-sm font-medium text-gray-700">Couple/Host Name <span class="text-red-500">*</span></label>
                    <input type="text" name="couple_name" id="couple_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" value="{{ old('couple_name') }}" required placeholder="e.g. Rahul & Priya">
                    @error('couple_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Couple Initials (Auto) -->
                <div>
                    <label for="couple_initials" class="block text-sm font-medium text-gray-700">Couple Initials (Auto)</label>
                    <input type="text" id="couple_initials" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm p-2 border" readonly placeholder="e.g. RP">
                    <p class="mt-1 text-xs text-gray-500">Auto-generated from couple name.</p>
                </div>

                <!-- Event Date -->
                <div>
                    <label for="event_date" class="block text-sm font-medium text-gray-700">Event Date <span class="text-red-500">*</span></label>
                    <input type="date" name="event_date" id="event_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" value="{{ old('event_date') }}" required>
                    @error('event_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" id="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" value="{{ old('location') }}" placeholder="e.g. Grand Taj Hotel, Mumbai">
                    @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Event Description</label>
                <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="Welcome to our special day!">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Organizer Logo -->
            <div>
                <label for="organizer_logo" class="block text-sm font-medium text-gray-700">Organizer Logo (Optional)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors bg-gray-50">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="organizer_logo" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 px-2 py-1">
                                <span>Upload a file</span>
                                <input id="organizer_logo" name="organizer_logo" type="file" class="sr-only" accept="image/jpeg,image/png,image/webp">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, WEBP up to 2MB</p>
                    </div>
                </div>
                @error('organizer_logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Create Event
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
