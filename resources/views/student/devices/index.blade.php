@extends('layouts.student')

@section('title', 'My Devices')
@section('page-title', 'My Devices')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="bg-white rounded-lg shadow p-4 lg:p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl lg:text-2xl font-bold">My Devices</h1>
        </div>

        <!-- Current Active Device -->
        @php
            $activeDevice = $devices->where('status', 'active')->first();
            $pendingReset = $devices->where('status', 'pending_reset')->first();
        @endphp

        @if($activeDevice)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 lg:p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="text-base lg:text-lg font-semibold text-green-800 mb-2">Current Active Device</h3>
                        <p class="text-xs lg:text-sm text-gray-700"><strong>Device:</strong> {{ $activeDevice->device_name ?? 'Unknown Device' }}</p>
                        <p class="text-xs lg:text-sm text-gray-700"><strong>IP Address:</strong> {{ $activeDevice->ip_address ?? 'N/A' }}</p>
                        <p class="text-xs lg:text-sm text-gray-700"><strong>Last Used:</strong> {{ $activeDevice->last_used_at ? $activeDevice->last_used_at->format('M d, Y H:i') : 'Never' }}</p>
                        <p class="text-xs lg:text-sm text-gray-700"><strong>Bound On:</strong> {{ $activeDevice->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs lg:text-sm font-semibold">Active</span>
                    </div>
                </div>

                @if(!$pendingReset)
                    <div class="mt-4 pt-4 border-t border-green-200">
                        <h4 class="font-semibold text-gray-800 mb-2 text-sm lg:text-base">Need to use a different device?</h4>
                        <p class="text-xs lg:text-sm text-gray-600 mb-4">You can request a device reset. Your request will be reviewed by an administrator.</p>

                        <button onclick="document.getElementById('resetRequestModal').classList.remove('hidden')"
                                class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 text-sm lg:text-base">
                            Request Device Reset
                        </button>
                    </div>
                @endif
            </div>
        @endif

        @if($pendingReset)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 lg:p-6 mb-6">
                <h3 class="text-base lg:text-lg font-semibold text-yellow-800 mb-2">Pending Reset Request</h3>
                <p class="text-xs lg:text-sm text-gray-700 mb-2"><strong>Requested On:</strong> {{ $pendingReset->reset_requested_at->format('M d, Y H:i') }}</p>
                <p class="text-xs lg:text-sm text-gray-700 mb-2"><strong>Reason:</strong> {{ $pendingReset->reset_request_reason }}</p>
                <p class="text-xs lg:text-sm text-yellow-700">Your device reset request is pending admin approval. You will be notified once it's approved.</p>
            </div>
        @endif

        <!-- All Devices History -->
        <div class="mt-6">
            <h2 class="text-lg lg:text-xl font-bold mb-4">Device History</h2>
            <div class="overflow-x-auto -mx-4 lg:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Device Name</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">IP Address</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Last Used</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Bound On</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($devices as $device)
                                    <tr>
                                        <td class="px-3 lg:px-6 py-4 text-xs lg:text-sm">
                                            <div>{{ $device->device_name ?? 'Unknown Device' }}</div>
                                            <div class="text-xs text-gray-500 md:hidden mt-1">{{ $device->ip_address ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-xs lg:text-sm text-gray-500 hidden md:table-cell">{{ $device->ip_address ?? 'N/A' }}</td>
                                        <td class="px-3 lg:px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $device->status == 'active' ? 'bg-green-100 text-green-800' :
                                                   ($device->status == 'pending_reset' ? 'bg-yellow-100 text-yellow-800' :
                                                   ($device->status == 'blocked' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $device->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-xs lg:text-sm text-gray-500 hidden lg:table-cell">
                                            {{ $device->last_used_at ? $device->last_used_at->format('M d, Y H:i') : 'Never' }}
                                        </td>
                                        <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-xs lg:text-sm text-gray-500 hidden lg:table-cell">
                                            {{ $device->created_at->format('M d, Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No devices found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Request Modal -->
<div id="resetRequestModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4">Request Device Reset</h3>
        <p class="text-sm text-gray-600 mb-4">
            Please provide a reason for requesting a device reset. Your request will be reviewed by an administrator.
        </p>
        <form action="{{ route('student.devices.request-reset') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Reset</label>
                <textarea name="reason" rows="4" required
                          class="w-full px-3 py-2 border rounded-lg @error('reason') border-red-500 @enderror"
                          placeholder="e.g., Lost my device, Changed phone, etc."></textarea>
                @error('reason')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">
                    Submit Request
                </button>
                <button type="button" onclick="document.getElementById('resetRequestModal').classList.add('hidden')"
                        class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

