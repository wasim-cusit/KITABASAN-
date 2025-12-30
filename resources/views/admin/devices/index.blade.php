@extends('layouts.admin')

@section('title', 'Device Management')
@section('page-title', 'Device Management')

@section('content')
@if($pendingResetCount > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4 lg:mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-base lg:text-lg font-semibold text-yellow-800">Pending Reset Requests</h3>
                <p class="text-sm text-yellow-700">You have {{ $pendingResetCount }} pending device reset request(s) awaiting approval.</p>
            </div>
            <a href="#pending-resets" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 text-sm lg:text-base text-center">
                View Requests
            </a>
        </div>
    </div>
@endif

<div class="bg-white rounded-lg shadow p-4 lg:p-6">
    <!-- Filters -->
    <form method="GET" action="{{ route('admin.devices.index') }}" class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <input type="text" name="search" placeholder="Search users..." value="{{ request('search') }}"
               class="px-4 py-2 border rounded-lg">
        <select name="status" class="px-4 py-2 border rounded-lg">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
            <option value="pending_reset" {{ request('status') == 'pending_reset' ? 'selected' : '' }}>Pending Reset</option>
        </select>
        <select name="user_id" class="px-4 py-2 border rounded-lg">
            <option value="">All Users</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Filter
        </button>
    </form>

    <!-- Pending Reset Requests -->
    @php
        $pendingResets = $devices->where('status', 'pending_reset');
    @endphp

    @if($pendingResets->count() > 0)
        <div id="pending-resets" class="mb-6">
            <h3 class="text-lg font-bold mb-4 text-yellow-800">Pending Reset Requests ({{ $pendingResets->count() }})</h3>
            <div class="space-y-4">
                @foreach($pendingResets as $device)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ $device->user->name }}</h4>
                                    <span class="text-sm text-gray-600">{{ $device->user->email }}</span>
                                </div>
                                <p class="text-sm text-gray-700 mb-1"><strong>Device:</strong> {{ $device->device_name ?? 'Unknown Device' }}</p>
                                <p class="text-sm text-gray-700 mb-1"><strong>IP Address:</strong> {{ $device->ip_address ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-700 mb-2"><strong>Requested On:</strong> {{ $device->reset_requested_at->format('M d, Y H:i') }}</p>
                                <div class="bg-white rounded p-3 mt-2">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Reason:</p>
                                    <p class="text-sm text-gray-600">{{ $device->reset_request_reason }}</p>
                                </div>
                            </div>
                            <div class="flex flex-row sm:flex-col gap-2 mt-4 sm:mt-0 sm:ml-4">
                                <form action="{{ route('admin.devices.approve-reset', $device->id) }}" method="POST" onsubmit="return confirm('Approve device reset? This will allow the user to login from a new device.')" class="flex-1 sm:flex-none">
                                    @csrf
                                    <button type="submit" class="w-full sm:w-auto bg-green-600 text-white px-3 lg:px-4 py-2 rounded-lg hover:bg-green-700 text-xs lg:text-sm whitespace-nowrap">
                                        Approve Reset
                                    </button>
                                </form>
                                <form action="{{ route('admin.devices.reject-reset', $device->id) }}" method="POST" onsubmit="return confirm('Reject reset request? Device will remain active.')" class="flex-1 sm:flex-none">
                                    @csrf
                                    <button type="submit" class="w-full sm:w-auto bg-red-600 text-white px-3 lg:px-4 py-2 rounded-lg hover:bg-red-700 text-xs lg:text-sm whitespace-nowrap">
                                        Reject Request
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Devices Table -->
    <div class="overflow-x-auto -mx-4 lg:mx-0">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Device Name</th>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">IP Address</th>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Last Used</th>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($devices as $device)
                            <tr class="{{ $device->status == 'pending_reset' ? 'bg-yellow-50' : '' }}">
                                <td class="px-3 lg:px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $device->user->name }}</div>
                                    <div class="text-xs text-gray-500 md:hidden">{{ $device->device_name ?? 'Unknown Device' }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($device->user->email, 20) }}</div>
                                </td>
                                <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                    {{ $device->device_name ?? 'Unknown Device' }}
                                </td>
                                <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ $device->ip_address ?? 'N/A' }}</td>
                                <td class="px-3 lg:px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $device->status == 'active' ? 'bg-green-100 text-green-800' :
                                           ($device->status == 'blocked' ? 'bg-red-100 text-red-800' :
                                           ($device->status == 'pending_reset' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $device->status)) }}
                                    </span>
                                </td>
                                <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                    {{ $device->last_used_at ? $device->last_used_at->format('M d, Y H:i') : 'Never' }}
                                </td>
                                <td class="px-3 lg:px-6 py-4 text-sm font-medium">
                                    <div class="flex flex-col sm:flex-row gap-1 sm:gap-0">
                                        @if($device->status == 'active')
                                            <form action="{{ route('admin.devices.block', $device->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900 sm:mr-3 text-xs lg:text-sm">Block</button>
                                            </form>
                                        @elseif($device->status == 'blocked')
                                            <form action="{{ route('admin.devices.unblock', $device->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 sm:mr-3 text-xs lg:text-sm">Unblock</button>
                                            </form>
                                        @elseif($device->status == 'pending_reset')
                                            <form action="{{ route('admin.devices.approve-reset', $device->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 sm:mr-3 text-xs lg:text-sm">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.devices.reject-reset', $device->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs lg:text-sm">Reject</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.devices.reset', $device->id) }}" method="POST" class="inline" onsubmit="return confirm('Reset device binding? User will need to login again.')">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-900 sm:ml-3 text-xs lg:text-sm">Reset</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No devices found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $devices->links() }}
    </div>
</div>
@endsection

