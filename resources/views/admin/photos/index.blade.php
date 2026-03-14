@extends('layouts.admin')

@section('title', 'Photos')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ previewOpen: false, previewSrc: '', previewTitle: '', previewQr: '', previewLink: '', toast: '', copyLink() { navigator.clipboard.writeText(this.previewLink); this.toast = 'Link copied!'; setTimeout(() => this.toast = '', 2000); } }">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Gallery Management</h2>
        <a href="{{ route('admin.photos.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 transition ease-in-out duration-150 shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Upload Photos
        </a>
    </div>

    <!-- Photo Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($photos as $photo)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group flex flex-col">
                <div class="relative aspect-square bg-gray-100">
                    <img src="{{ Storage::url($photo->image_path) }}" alt="Photo ID {{ $photo->id }}" class="w-full h-full object-cover transition-transform group-hover:scale-105">
                    
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-2 pb-6">
                        <button type="button"
                            class="p-2 bg-white text-gray-900 rounded-full hover:bg-gray-100 transition-colors"
                            title="Preview"
                            @click="previewOpen = true; previewSrc = @js(Storage::url($photo->image_path)); previewTitle = @js($photo->event->event_name . ' #' . $photo->id); previewQr = @js($photo->qr_code_path ? Storage::url($photo->qr_code_path) : ''); previewLink = @js(route('guest.photo.show', $photo->id));">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                        <form action="{{ route('admin.photos.destroy', $photo) }}" method="POST" onsubmit="return confirm('Delete this photo?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-red-600 text-white rounded-full hover:bg-red-700 transition-colors" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Individual QR link or overlay could go here -->
                    @if($photo->qr_code_path)
                    <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ Storage::url($photo->qr_code_path) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-white text-xs font-bold rounded shadow text-gray-800" title="View QR Code">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg> QR
                        </a>
                    </div>
                    @endif
                </div>
                <div class="p-3 text-xs flex-1 flex flex-col justify-between">
                    <p class="font-semibold text-gray-800 truncate" title="{{ $photo->event->event_name }}">{{ $photo->event->event_name }}</p>
                    <div class="flex justify-between items-center text-gray-500 mt-1">
                        <span>{{ $photo->downloads }} dl</span>
                        <span>{{ $photo->created_at->format('M d') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No photos</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new upload.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.photos.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Upload Photos
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $photos->links() }}
    </div>

    <!-- Preview Modal -->
    <div x-show="previewOpen" x-cloak @keydown.escape.window="previewOpen = false" class="fixed inset-0 z-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="previewOpen = false"></div>
        <div class="relative z-10 w-full max-w-6xl bg-white rounded-3xl overflow-hidden shadow-2xl border border-white/10 flex flex-col md:flex-row">
            
            <!-- Left: Image -->
            <div class="bg-gray-900 flex-1 p-4 sm:p-6 flex items-center justify-center min-h-[50vh]">
                <img :src="previewSrc" alt="Preview" class="max-h-[80vh] w-auto object-contain rounded-2xl ring-1 ring-white/10">
            </div>

            <!-- Right: Details & Sidebar -->
            <div class="w-full md:w-80 bg-white p-6 flex flex-col border-l border-gray-100">
                <div class="flex items-start justify-between mb-6">
                    <p class="text-lg font-bold text-gray-900 break-words" x-text="previewTitle"></p>
                    <button type="button" class="p-2 -mr-2 -mt-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700" @click="previewOpen = false">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-4 flex-1">
                    <a :href="'/admin/photos/' + previewTitle.split('#')[1] + '/download'" class="w-full flex items-center justify-center px-4 py-2 font-semibold rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-900 transition-colors">
                        Download Full Image
                    </a>
                    
                    <button type="button" @click="copyLink" class="w-full flex items-center justify-center px-4 py-2 font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white transition-colors relative">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        Copy Public Link
                    </button>
                    <!-- Toast inline relative to button -->
                    <p x-show="toast" x-text="toast" class="text-green-600 text-sm font-medium text-center" style="display: none;"></p>

                    <template x-if="previewQr">
                        <div class="mt-8 text-center bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-3">Photo QR Code</p>
                            <img :src="previewQr" alt="QR Code" class="w-32 h-32 mx-auto bg-white p-2 rounded-xl border border-gray-200 shadow-sm">
                            <a :href="'/admin/qrcodes/download?path=' + encodeURIComponent(previewQr.split('/storage/')[1])" class="mt-3 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-800">Download QR</a>
                        </div>
                    </template>
                    <template x-if="!previewQr">
                        <div class="mt-8 text-center bg-gray-50 rounded-2xl p-4 border border-dashed border-gray-200 text-sm text-gray-500">
                            No QR code available.
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
