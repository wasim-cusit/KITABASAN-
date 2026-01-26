@extends('layouts.app')

@section('title', 'Return & Refund Policy - KITAB ASAN')
@section('description', 'Learn about KITAB ASAN refund eligibility, timelines, and the process for paid course refunds.')
@section('keywords', 'refund policy, return policy, course refund, payment refund, kitab asan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <section class="py-12 sm:py-16">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 sm:px-10 py-8 sm:py-10 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                    <div class="flex flex-col gap-2">
                        <h1 class="text-2xl sm:text-3xl font-bold">Return &amp; Refund Policy</h1>
                        <p class="text-blue-100 text-sm sm:text-base">Last updated: {{ date('M d, Y') }}</p>
                    </div>
                </div>

                <div class="px-6 sm:px-10 py-8 space-y-8 text-sm sm:text-base text-gray-700">
                    <p>We aim to provide high-quality courses. If you face a genuine issue, we will review refund requests fairly and transparently.</p>

                    <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Eligibility</h2>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Refund requests must be submitted within 10 days of purchase.</li>
                            <li>Refunds are not available for courses completed beyond a reasonable learning threshold.</li>
                            <li>Free courses are not eligible for refunds.</li>
                            <li>We may request additional details to verify the issue.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Non-Refundable Cases</h2>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Change of mind after significant course access.</li>
                            <li>Violation of platform terms or misuse of content.</li>
                            <li>Payments marked as fraudulent or reversed by the payment provider.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Process & Timelines</h2>
                        <p>To request a refund, contact us with your transaction ID and course name. We review requests within 3–5 business days. Approved refunds are processed to the original payment method, which may take additional time depending on the provider.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Partial Refunds</h2>
                        <p>In some cases, partial refunds may be offered based on course usage and the nature of the issue. This is evaluated on a case-by-case basis.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Cancellations</h2>
                        <p>You may cancel future purchases at any time. Course access for completed purchases remains available unless a refund is approved.</p>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Contact</h2>
                        <p>For refund queries, contact us at:</p>
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
