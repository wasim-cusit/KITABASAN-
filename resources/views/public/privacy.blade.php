@extends('layouts.app')

@section('title', 'Privacy Policy - KITAB ASAN')
@section('description', 'Read how KITAB ASAN collects, uses, and protects your personal information. Learn about your privacy rights and contact details.')
@section('keywords', 'privacy policy, data protection, personal information, kitab asan privacy, user data')

@section('content')
<div class="min-h-screen bg-gray-50">
    <section class="py-12 sm:py-16">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 sm:px-10 py-8 sm:py-10 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                    <div class="flex flex-col gap-2">
                        <h1 class="text-2xl sm:text-3xl font-bold">Privacy Policy</h1>
                        <p class="text-blue-100 text-sm sm:text-base">Last updated: {{ date('M d, Y') }}</p>
                    </div>
                </div>

                <div class="px-6 sm:px-10 py-8 space-y-8 text-sm sm:text-base text-gray-700">
                    <p>We respect your privacy and are committed to protecting your personal information. This policy explains what we collect, how we use it, and your rights.</p>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Information We Collect</h2>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Account details (name, email, phone).</li>
                                <li>Course activity and progress.</li>
                                <li>Payment and billing details (processed by payment providers).</li>
                            </ul>
                        </div>
                        <div class="border border-gray-200 rounded-xl p-5 bg-gray-50">
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">How We Use Information</h2>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Provide access to courses and learning features.</li>
                                <li>Process payments and send invoices/receipts.</li>
                                <li>Improve platform performance and support.</li>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Data Security</h2>
                        <p>We use industry-standard security measures to protect your data. Payment details are handled by trusted gateways; we do not store full card details on our servers.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Cookies & Analytics</h2>
                        <p>We may use cookies and analytics tools to improve site performance and understand how users interact with our platform. You can control cookies through your browser settings.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Your Rights</h2>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Request access, correction, or deletion of your account data.</li>
                            <li>Opt out of non-essential communications.</li>
                            <li>Report suspected misuse of your information.</li>
                        </ul>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Contact</h2>
                        <p>For privacy-related queries, contact us at:</p>
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
