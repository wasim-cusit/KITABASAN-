@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- User Info -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <div class="flex items-center space-x-4 mb-6">
            <img src="{{ $user->profile_image ? Storage::url($user->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" 
                 alt="{{ $user->name }}" class="h-20 w-20 rounded-full">
            <div>
                <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                <p class="text-gray-600">{{ $user->email }}</p>
                <p class="text-gray-600">{{ $user->mobile ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <span class="text-sm text-gray-500">Role</span>
                <p class="font-semibold">
                    @foreach($user->roles as $role)
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ ucfirst($role->name) }}</span>
                    @endforeach
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Status</span>
                <p class="font-semibold">
                    <span class="px-2 py-1 text-xs rounded {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Joined</span>
                <p class="font-semibold">{{ $user->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Last Login</span>
                <p class="font-semibold">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</p>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Edit User
            </a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Enrollments -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Course Enrollments</h3>
            <div class="space-y-2">
                @forelse($user->enrollments as $enrollment)
                    <div class="border-b pb-2">
                        <p class="font-medium">{{ $enrollment->book->title }}</p>
                        <p class="text-sm text-gray-600">{{ $enrollment->status }} - {{ $enrollment->created_at->format('M d, Y') }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No enrollments</p>
                @endforelse
            </div>
        </div>

        <!-- Payments -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Payments</h3>
            <div class="space-y-2">
                @forelse($user->payments->take(5) as $payment)
                    <div class="border-b pb-2">
                        <p class="font-medium">Rs. {{ number_format($payment->amount, 2) }}</p>
                        <p class="text-sm text-gray-600">{{ $payment->book->title }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->status }} - {{ $payment->created_at->format('M d, Y') }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No payments</p>
                @endforelse
            </div>
        </div>

        <!-- Devices -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Device Bindings</h3>
            <div class="space-y-2">
                @forelse($user->deviceBindings as $device)
                    <div class="border-b pb-2">
                        <p class="text-sm font-medium">{{ $device->device_name ?? 'Unknown Device' }}</p>
                        <p class="text-xs text-gray-500">{{ $device->status }} - {{ $device->last_used_at ? $device->last_used_at->format('M d, Y') : 'Never' }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No devices</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

