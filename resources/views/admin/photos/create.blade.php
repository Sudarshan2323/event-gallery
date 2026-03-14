@extends('layouts.admin')

@section('title', 'Upload Photos')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center space-x-4 mb-6">
        <a href="{{ route('admin.photos.index') }}" class="text-gray-500 hover:text-gray-700 bg-white p-2 rounded-md shadow-sm border border-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-900">Upload Event Photos</h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('admin.photos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="uploadForm">
            @csrf

            <!-- Select Event -->
            <div>
                <label for="event_id" class="block text-sm font-medium text-gray-700 mb-2">Select Event <span class="text-red-500">*</span></label>
                <select id="event_id" name="event_id" required class="block w-full pl-3 pr-10 py-3 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg hover:border-gray-400 transition-colors bg-gray-50">
                    <option value="" disabled selected>Choose an event to upload to...</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ old('event_id', request('event_id')) == $event->id ? 'selected' : '' }}>
                            {{ $event->event_name }} ({{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }})
                        </option>
                    @endforeach
                </select>
                @error('event_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Upload Multiple Photos -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Photos (Multiple allowed) <span class="text-red-500">*</span></label>
                <div class="mt-1 flex justify-center px-6 pt-10 pb-12 border-2 border-gray-300 border-dashed rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-all bg-gray-50 cursor-pointer" id="dropzone" onclick="document.getElementById('photos').click()">
                    <div class="space-y-2 text-center">
                        <svg class="mx-auto h-16 w-16 text-indigo-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m0-4c0 4.418-7.163 8-16 8S8 28.418 8 24m32-1l-3.293-3.293a1 1 0 00-1.414 0L24 31l-3.293-3.293a1 1 0 00-1.414 0L8 39v3" />
                        </svg>
                        <div class="flex text-base text-gray-600 justify-center items-center mt-2">
                            <label for="photos" class="relative cursor-pointer bg-transparent rounded-md font-semibold text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>Select photos from your computer</span>
                                <input id="photos" name="photos[]" type="file" multiple class="sr-only" accept="image/jpeg,image/png,image/webp" required onchange="updateFileList(this)">
                            </label>
                        </div>
                        <p class="text-sm text-gray-500">PNG, JPG, WEBP formats. Up to 5MB per file.</p>
                    </div>
                </div>
                <div id="file-list" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                    <!-- Javascript will populate selections here -->
                </div>
                @error('photos') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                @if($errors->has('photos.*'))
                    @foreach($errors->get('photos.*') as $error)
                        <p class="mt-1 text-sm text-red-600">{{ $error[0] }}</p>
                    @endforeach
                @endif
            </div>

            <!-- Submit Button with loading state -->
            <div class="flex justify-end pt-6 border-t border-gray-100">
                <button type="submit" id="submitBtn" class="inline-flex justify-center items-center py-3 px-8 border border-transparent shadow-md text-base font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                    <svg id="spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Upload & Generate QR Codes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let previewUrls = [];

    function escapeHtml(text) {
        return String(text).replace(/[&<>"']/g, (m) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[m]));
    }

    function formatBytes(bytes) {
        if (!bytes && bytes !== 0) return '';
        const units = ['B', 'KB', 'MB', 'GB'];
        let value = bytes;
        let i = 0;
        while (value >= 1024 && i < units.length - 1) {
            value /= 1024;
            i++;
        }
        return `${value.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
    }

    function updateFileList(input) {
        const fileList = document.getElementById('file-list');

        // Revoke old previews to avoid memory leaks.
        previewUrls.forEach((u) => URL.revokeObjectURL(u));
        previewUrls = [];

        fileList.innerHTML = '';
        
        if (input.files.length > 0) {
            Array.from(input.files).forEach(file => {
                const url = URL.createObjectURL(file);
                previewUrls.push(url);

                const item = document.createElement('div');
                item.className = 'group relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm';

                const safeName = escapeHtml(file.name);
                const safeSize = escapeHtml(formatBytes(file.size));

                item.innerHTML = `
                    <div class="aspect-square bg-gray-100 overflow-hidden">
                        <img src="${url}" alt="${safeName}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>
                    <div class="p-2">
                        <p class="text-xs font-semibold text-gray-800 truncate" title="${safeName}">${safeName}</p>
                        <p class="mt-0.5 text-[11px] text-gray-500">${safeSize}</p>
                    </div>
                `;

                fileList.appendChild(item);
            });
        }
    }

    document.getElementById('uploadForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const spinner = document.getElementById('spinner');
        
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        spinner.classList.remove('hidden');
        btn.querySelector('span').innerText = 'Uploading... Please wait';
    });
</script>
@endsection
