@extends('layouts.app')

@section('title', 'Shipping & Service Policy - KITAB ASAN')
@section('description', 'Learn how KITAB ASAN delivers digital services, access timelines, and support for course delivery.')
@section('keywords', 'shipping policy, service policy, digital delivery, course access, kitab asan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <section class="py-12 sm:py-16">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 sm:px-10 py-8 sm:py-10 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                    <div class="flex flex-col gap-2">
                        <h1 class="text-2xl sm:text-3xl font-bold">Shipping &amp; Service Policy</h1>
                        <p class="text-blue-100 text-sm sm:text-base">Last updated: {{ date('M d, Y') }}</p>
                    </div>
                </div>

                <div class="px-6 sm:px-10 py-8 space-y-8 text-sm sm:text-base text-gray-700">
                    <p>KITAB ASAN provides digital educational services. There are no physical shipments.</p>

                    <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Service Delivery</h2>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Course access is provided instantly after successful payment.</li>
                            <li>For free courses, access is available immediately after enrollment.</li>
                            <li>Invoices and receipts are available in your account.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Support</h2>
                        <p>If you face issues accessing content, contact us and we will assist you promptly.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Service Availability</h2>
                        <p>Our services are delivered online and are accessible across supported devices and browsers. Scheduled maintenance may temporarily affect access.</p>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Contact</h2>
                        <p>For service support, contact us at:</p>
                        <p class="mt-2 text-gray-700">
                            <span class="font-semibold">Address:</span> House No. 12, opposite Masjid Abu Ayub Ansari, Near Abpara Market, University Town, Peshawar.<br>
                            <span class="font-semibold">Phone:</span> 03159427588
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('partials.footer')
@endsection
