<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">User Management</h2>
                @if(in_array(auth()->user()->role, ['President', 'Coordinator']) && auth()->user()->region)
                    <p class="text-xs font-medium text-gray-500 mt-0.5">
                        Viewing users for region: <span class="font-bold text-gray-700">{{ auth()->user()->region->name }}</span>
                    </p>
                @endif
            </div>

            @if(in_array(auth()->user()->role, ['Admin', 'President', 'Coordinator']))
                <a href="{{ route('users.create') }}" class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition">
                    + Add User
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Success Alert --}}
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 mb-6 text-sm font-bold border rounded-2xl bg-emerald-50 border-emerald-100 text-emerald-700">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Alert --}}
        @if(session('error'))
            <div class="flex items-center gap-3 p-4 mb-6 text-sm font-bold text-red-700 border border-red-100 rounded-2xl bg-red-50">
                <i class="fa-solid fa-triangle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
            <table class="w-full text-left border-collapse">
                <thead class="border-b bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Name & Email</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Role</th>
                        <th class="px-6 py-4 text-xs font-black text-center text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase">Assigned Region</th>
                        <th class="px-6 py-4 text-xs font-black text-right text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition {{ !$user->is_accepted ? 'bg-amber-50/30' : '' }}">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $user->role === 'Admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($user->is_accepted)
                                    <span class="text-emerald-600 text-[10px] font-black uppercase flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-circle-check"></i> Verified
                                    </span>
                                @else
                                    <span class="text-amber-600 text-[10px] font-black uppercase flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-clock-rotate-left"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $user->region?->name ?? 'All Regions (Admin)' }}
                            </td>
                            <td class="px-6 py-4 space-x-3 text-right">
                                {{-- Approval Action --}}
                                @if(!$user->is_accepted && in_array(auth()->user()->role, ['Admin', 'President', 'Coordinator']))
                                    <form action="{{ route('users.accept', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-black uppercase text-emerald-600 hover:underline">
                                            Approve Access
                                        </button>
                                    </form>
                                @endif

                                {{-- Edit Link: System Admins only --}}
                                @if(auth()->user()->role === 'Admin')
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-sm font-bold text-blue-500 hover:underline">
                                        Edit
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">
                                <i class="fa-solid fa-users-slash text-2xl mb-2 text-gray-300 block"></i>
                                No users found in your assigned region.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>