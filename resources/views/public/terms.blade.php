@extends('layouts.app')

@section('title', 'Terms & Conditions - KITAB ASAN')
@section('description', 'Review KITAB ASAN terms and conditions for using the platform, payments, and user responsibilities.')
@section('keywords', 'terms and conditions, user agreement, course terms, kitab asan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <section class="py-12 sm:py-16">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 sm:px-10 py-8 sm:py-10 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                    <div class="flex flex-col gap-2">
                        <h1 class="text-2xl sm:text-3xl font-bold">Terms &amp; Conditions</h1>
                        <p class="text-blue-100 text-sm sm:text-base">Last updated: {{ date('M d, Y') }}</p>
                    </div>
                </div>

                <div class="px-6 sm:px-10 py-8 space-y-8 text-sm sm:text-base text-gray-700">
                    <p>By using KITAB ASAN, you agree to comply with these terms and conditions.</p>

                    <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">User Responsibilities</h2>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Provide accurate account information.</li>
                            <li>Use course content for personal learning only.</li>
                            <li>Do not share or redistribute paid content without permission.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Payments</h2>
                        <p>Paid courses require successful payment before access is granted. Refunds are handled according to our Return &amp; Refund Policy.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Account Security</h2>
                        <p>You are responsible for maintaining the confidentiality of your account and password. Please notify us immediately of any unauthorized use.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Changes</h2>
                        <p>We may update these terms from time to time. Continued use of the platform indicates acceptance of updated terms.</p>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Contact</h2>
                        <p>For questions about these terms, contact us at:</p>
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
