@extends('layouts.teacher')

@section('title', 'My Devices')
@section('page-title', 'My Devices')

@section('content')
<div class="teacher-devices-container">
    <div class="teacher-devices-card">
        <div class="teacher-devices-header">
            <h1 class="teacher-devices-header-title">My Devices</h1>
        </div>

        <!-- Current Active Device -->
        @php
            $activeDevice = $devices->where('status', 'active')->first();
            $pendingReset = $devices->where('status', 'pending_reset')->first();
        @endphp

        @if($activeDevice)
            <div class="teacher-devices-active">
                <div class="teacher-devices-active-inner">
                    <div>
                        <h3 class="teacher-devices-active-title">Current Active Device</h3>
                        <p class="teacher-devices-active-detail"><strong>Device:</strong> {{ $activeDevice->device_name ?? 'Unknown Device' }}</p>
                        <p class="teacher-devices-active-detail"><strong>IP Address:</strong> {{ $activeDevice->ip_address ?? 'N/A' }}</p>
                        <p class="teacher-devices-active-detail"><strong>Last Used:</strong> {{ $activeDevice->last_used_at ? $activeDevice->last_used_at->format('M d, Y H:i') : 'Never' }}</p>
                        <p class="teacher-devices-active-detail"><strong>Bound On:</strong> {{ $activeDevice->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="teacher-devices-badge teacher-devices-badge--active">Active</span>
                    </div>
                </div>

                @if(!$pendingReset)
                    <div class="teacher-devices-reset-block">
                        <h4 class="teacher-devices-reset-title">Need to use a different device?</h4>
                        <p class="teacher-devices-reset-desc">You can request a device reset. Your request will be reviewed by an administrator.</p>
                        <button type="button" onclick="document.getElementById('resetRequestModal').classList.remove('hidden')" class="teacher-devices-reset-btn">
                            Request Device Reset
                        </button>
                    </div>
                @endif
            </div>
        @endif

        @if($pendingReset)
            <div class="teacher-devices-pending">
                <h3 class="teacher-devices-pending-title">Pending Reset Request</h3>
                <p class="teacher-devices-pending-detail"><strong>Requested On:</strong> {{ $pendingReset->reset_requested_at->format('M d, Y H:i') }}</p>
                <p class="teacher-devices-pending-detail"><strong>Reason:</strong> {{ $pendingReset->reset_request_reason }}</p>
                <p class="teacher-devices-pending-note">Your device reset request is pending admin approval. You will be notified once it's approved.</p>
            </div>
        @endif

        <!-- All Devices History -->
        <div class="teacher-devices-history">
            {{-- <h2 class="teacher-devices-history-title">Device History</h2> --}}
            <div class="teacher-devices-table-wrap">
                <div class="teacher-devices-table-inner">
                    <div class="teacher-devices-table-outer">
                        <table class="teacher-devices-table">
                            <thead>
                                <tr>
                                    <th>Device Name</th>
                                    <th class="hide-md">IP Address</th>
                                    <th>Status</th>
                                    <th class="hide-lg">Last Used</th>
                                    <th class="hide-lg">Bound On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($devices as $device)
                                    <tr>
                                        <td>
                                            <div>{{ $device->device_name ?? 'Unknown Device' }}</div>
                                            <div class="teacher-devices-ip-mobile">{{ $device->ip_address ?? 'N/A' }}</div>
                                        </td>
                                        <td class="whitespace-nowrap hide-md teacher-devices-cell-muted">{{ $device->ip_address ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = $device->status == 'active' ? 'teacher-devices-badge--active' :
                                                    ($device->status == 'pending_reset' ? 'teacher-devices-badge--pending' :
                                                    ($device->status == 'blocked' ? 'teacher-devices-badge--blocked' : 'teacher-devices-badge--default'));
                                            @endphp
                                            <span class="teacher-devices-badge {{ $badgeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $device->status)) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap hide-lg teacher-devices-cell-muted">
                                            {{ $device->last_used_at ? $device->last_used_at->format('M d, Y H:i') : 'Never' }}
                                        </td>
                                        <td class="whitespace-nowrap hide-lg teacher-devices-cell-muted">
                                            {{ $device->created_at->format('M d, Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="teacher-devices-empty">No devices found</td>
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
        <form action="{{ route('teacher.devices.request-reset') }}" method="POST">
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

