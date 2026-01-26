@extends('layouts.app')

@section('title', 'FAQs - KITAB ASAN')
@section('description', 'Find answers to common questions about courses, payments, access, and support at KITAB ASAN.')
@section('keywords', 'faqs, help, support, course access, payments, kitab asan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <section class="py-12 sm:py-16">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 sm:px-10 py-8 sm:py-10 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                    <div class="flex flex-col gap-2">
                        <h1 class="text-2xl sm:text-3xl font-bold">FAQs</h1>
                        <p class="text-blue-100 text-sm sm:text-base">Last updated: {{ date('M d, Y') }}</p>
                    </div>
                </div>

                <div class="px-6 sm:px-10 py-8 space-y-4 text-sm sm:text-base text-gray-700">
                    <details class="group border border-gray-200 rounded-xl bg-gray-50 p-5">
                        <summary class="cursor-pointer list-none font-semibold text-gray-900 flex items-center justify-between">
                            <span>How do I enroll in a course?</span>
                            <span class="text-blue-600 group-open:rotate-180 transition-transform">▾</span>
                        </summary>
                        <p class="mt-3">Select a course, click “Purchase Course” for paid courses or “Start Free Course” for free courses, then complete the payment or enrollment.</p>
                    </details>

                    <details class="group border border-gray-200 rounded-xl bg-gray-50 p-5">
                        <summary class="cursor-pointer list-none font-semibold text-gray-900 flex items-center justify-between">
                            <span>When will I get access after payment?</span>
                            <span class="text-blue-600 group-open:rotate-180 transition-transform">▾</span>
                        </summary>
                        <p class="mt-3">Access is granted automatically after successful payment confirmation. If a payment is pending, please allow a few minutes for processing.</p>
                    </details>

                    <details class="group border border-gray-200 rounded-xl bg-gray-50 p-5">
                        <summary class="cursor-pointer list-none font-semibold text-gray-900 flex items-center justify-between">
                            <span>Can I get a refund?</span>
                            <span class="text-blue-600 group-open:rotate-180 transition-transform">▾</span>
                        </summary>
                        <p class="mt-3">Refunds are reviewed according to our Return &amp; Refund Policy. Please submit your request within 10 days of purchase.</p>
                    </details>

                    <details class="group border border-gray-200 rounded-xl bg-gray-50 p-5">
                        <summary class="cursor-pointer list-none font-semibold text-gray-900 flex items-center justify-between">
                            <span>How can I contact support?</span>
                            <span class="text-blue-600 group-open:rotate-180 transition-transform">▾</span>
                        </summary>
                        <p class="mt-3">You can contact us using the details below for any issues with courses, access, or payments.</p>
                    </details>

                    <details class="group border border-gray-200 rounded-xl bg-gray-50 p-5">
                        <summary class="cursor-pointer list-none font-semibold text-gray-900 flex items-center justify-between">
                            <span>Do free courses have limits?</span>
                            <span class="text-blue-600 group-open:rotate-180 transition-transform">▾</span>
                        </summary>
                        <p class="mt-3">Free courses provide full access to listed content. Some courses may also offer limited previews of paid content.</p>
                    </details>

                    <details class="group border border-gray-200 rounded-xl bg-gray-50 p-5">
                        <summary class="cursor-pointer list-none font-semibold text-gray-900 flex items-center justify-between">
                            <span>Can I access courses on mobile?</span>
                            <span class="text-blue-600 group-open:rotate-180 transition-transform">▾</span>
                        </summary>
                        <p class="mt-3">Yes. KITAB ASAN works on modern mobile browsers for learning on the go.</p>
                    </details>

                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Contact</h2>
                        <p>For assistance, contact us at:</p>
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
