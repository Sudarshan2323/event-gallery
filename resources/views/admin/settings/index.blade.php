@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Settings</h2>
        <p class="text-sm text-gray-500">Manage admin users.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Admin User</h3>
        <form method="POST" action="{{ route('admin.settings.users.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input name="name" type="text" class="mt-1 block w-full rounded-lg border-gray-300 p-2 border" value="{{ old('name') }}" required>
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input name="email" type="email" class="mt-1 block w-full rounded-lg border-gray-300 p-2 border" value="{{ old('email') }}" required>
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input name="password" type="password" class="mt-1 block w-full rounded-lg border-gray-300 p-2 border" required>
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end">
                <input type="hidden" name="role" value="admin">
                <button type="submit" class="w-full inline-flex justify-center py-2.5 px-4 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                    Create
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Admin Users</h3>
            <span class="text-xs text-gray-500">{{ $users->total() }} users</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2">
                                    <details class="relative">
                                        <summary class="list-none cursor-pointer px-3 py-2 text-xs font-semibold rounded-lg bg-white border border-gray-200 hover:bg-gray-50">
                                            Edit
                                        </summary>
                                        <div class="absolute right-0 mt-2 w-96 bg-white border border-gray-200 rounded-2xl shadow-xl p-4 z-20">
                                            <form method="POST" action="{{ route('admin.settings.users.update', $user) }}" class="space-y-3">
                                                @csrf
                                                @method('PATCH')
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest">Name</label>
                                                    <input name="name" type="text" class="mt-1 block w-full rounded-lg border-gray-300 p-2 border" value="{{ $user->name }}" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest">Email</label>
                                                    <input name="email" type="email" class="mt-1 block w-full rounded-lg border-gray-300 p-2 border" value="{{ $user->email }}" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest">New Password (optional)</label>
                                                    <input name="password" type="password" class="mt-1 block w-full rounded-lg border-gray-300 p-2 border" placeholder="Leave blank to keep current">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest">Role</label>
                                                    <select name="role" class="mt-1 block w-full rounded-lg border-gray-300 p-2 border bg-white" required>
                                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>admin</option>
                                                    </select>
                                                </div>
                                                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                                                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>

                                    <form method="POST" action="{{ route('admin.settings.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-2 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

