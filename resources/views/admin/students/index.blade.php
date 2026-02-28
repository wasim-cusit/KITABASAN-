@extends('layouts.admin')

@section('title', 'Students Management')
@section('page-title', 'Students Management')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 lg:p-6">
        <!-- Header and Action Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-bold">Students Management</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Total: {{ $stats['total'] ?? 0 }} | Active: {{ $stats['active'] ?? 0 }} | Enrolled: {{ $stats['enrolled'] ?? 0 }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.students.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm lg:text-base text-center whitespace-nowrap">
                    + Add New Student
                </a>
            </div>
        </div>

        <!-- Bulk Actions Bar (Hidden by default, shown when students are selected) -->
        <div id="bulkActionsBar" class="hidden mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span id="selectedCount" class="text-sm font-semibold text-blue-900">0 students selected</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="openEmailModal()"
                            class="bg-green-600 hover:bg-green-700 px-5 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-white font-bold tracking-wide" style="color: #ffffff !important; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">Send Email Offer</span>
                    </button>
                    {{--
                    <button type="button" onclick="openSMSModal()"
                            class="bg-purple-600 hover:bg-purple-700 px-5 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span class="text-black font-bold tracking-wide" style="color: #000000 !important;">Send SMS Offer</span>
                    </button>
                    --}}
                    <button type="button" onclick="clearSelection()"
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm font-semibold shadow hover:shadow-md transition-all">
                        <span class="text-white">Clear Selection</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Students</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Active</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-gray-600">{{ $stats['inactive'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Inactive</div>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ $stats['suspended'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Suspended</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['enrolled'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Enrolled</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['not_enrolled'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Not Enrolled</div>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.students.index') }}" class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <input type="text" name="search" placeholder="Search by name, email, mobile..." value="{{ request('search') }}"
                       class="px-4 py-2 border rounded-lg">

                <select name="status" class="px-4 py-2 border rounded-lg">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <select name="enrollment_status" class="px-4 py-2 border rounded-lg">
                    <option value="">All Enrollment Status</option>
                    <option value="enrolled" {{ request('enrollment_status') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                    <option value="not_enrolled" {{ request('enrollment_status') == 'not_enrolled' ? 'selected' : '' }}>Not Enrolled</option>
                </select>

                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Filter
                </button>
            </div>
        </form>

        <!-- Students Table -->
        <div class="overflow-x-auto -mx-4 lg:mx-0">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">
                                    <input type="checkbox" id="selectAll"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                           onchange="toggleSelectAll(this)">
                                </th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Email</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Mobile</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Enrollments</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden xl:table-cell">Payments</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($students as $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 lg:px-6 py-4">
                                        <input type="checkbox"
                                               class="student-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                               value="{{ $student->id }}"
                                               onchange="updateBulkActions()">
                                    </td>
                                    <td class="px-3 lg:px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="shrink-0 h-8 w-8 lg:h-10 lg:w-10">
                                                <x-user-avatar :user="$student" size="sm" />
                                            </div>
                                            <div class="ml-2 lg:ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                                <div class="text-xs text-gray-500 sm:hidden">{{ \Str::limit($student->email, 25) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">{{ $student->email }}</td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ $student->mobile ?? 'N/A' }}</td>
                                    <td class="px-3 lg:px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $student->status == 'active' ? 'bg-green-100 text-green-800' : ($student->status == 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                            {{ $student->enrollments_count ?? 0 }} course(s)
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden xl:table-cell">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                            {{ $student->payments_count ?? 0 }} payment(s)
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 text-sm font-medium">
                                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-0">
                                            <a href="{{ route('admin.students.show', $student->id) }}"
                                               class="text-blue-600 hover:text-blue-900 sm:mr-3">View</a>
                                            <a href="{{ route('admin.students.edit', $student->id) }}"
                                               class="text-indigo-600 hover:text-indigo-900 sm:mr-3">Edit</a>
                                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this student?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No students found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $students->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>

<!-- Email Offer Modal -->
<div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Send Email Offer</h3>
                <button onclick="closeEmailModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Email Configuration Status -->
            <div id="emailConfigStatus" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <strong>Email Configuration:</strong> Using system email settings. Emails will be sent from your configured SMTP server.
                    </div>
                </div>
            </div>

            <form id="emailForm" onsubmit="sendEmailOffers(event)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                        <input type="text" name="subject" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="e.g., Special Course Offer - 50% Off!">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea name="message" rows="8" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Enter your offer message here..."></textarea>
                        <p class="mt-1 text-xs text-gray-500">You can use <code class="bg-gray-100 px-1 rounded">{name}</code> to personalize the message with student's name.</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="send_to_all" id="sendToAllEmail" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <label for="sendToAllEmail" class="ml-2 text-sm text-gray-700">Send to all students (ignore selection)</label>
                    </div>
                    <div id="emailPreview" class="hidden p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-xs font-medium text-gray-700 mb-2">Preview (for first student):</p>
                        <p class="text-sm text-gray-600" id="emailPreviewText"></p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeEmailModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" id="emailSubmitBtn"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                        <span id="emailSubmitText">Send Emails</span>
                        <svg id="emailSubmitSpinner" class="hidden animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SMS Offer Modal -->
<div id="smsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Send SMS Offer</h3>
                <button onclick="closeSMSModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="smsForm" onsubmit="sendSMSOffers(event)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea name="message" rows="6" required maxlength="160"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                  placeholder="Enter your SMS offer message (max 160 characters)..."></textarea>
                        <p class="mt-1 text-xs text-gray-500">
                            <span id="smsCharCount">0</span>/160 characters
                            <span class="ml-2">You can use {name} to personalize the message.</span>
                        </p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="send_to_all" id="sendToAllSMS" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="sendToAllSMS" class="ml-2 text-sm text-gray-700">Send to all students (ignore selection)</label>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-yellow-800">
                            <strong>Note:</strong> SMS will only be sent to students who have a valid mobile number in their profile.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeSMSModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Send SMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Select All functionality
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateBulkActions();
    }

    // Update bulk actions bar visibility
    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.student-checkbox:checked');
        const bulkBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.getElementById('selectedCount');

        if (checkboxes.length > 0) {
            bulkBar.classList.remove('hidden');
            selectedCount.textContent = `${checkboxes.length} student${checkboxes.length > 1 ? 's' : ''} selected`;
        } else {
            bulkBar.classList.add('hidden');
        }

        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.student-checkbox');
        const selectAll = document.getElementById('selectAll');
        if (allCheckboxes.length > 0) {
            selectAll.checked = checkboxes.length === allCheckboxes.length;
            selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
        }
    }

    // Clear selection
    function clearSelection() {
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateBulkActions();
    }

    // Email Modal functions
    function openEmailModal() {
        const selected = getSelectedStudentIds();
        const modal = document.getElementById('emailModal');
        const selectedCount = selected.length;

        // Update preview if students are selected
        if (selectedCount > 0) {
            const preview = document.getElementById('emailPreview');
            preview.classList.remove('hidden');
            document.getElementById('emailPreviewText').textContent =
                `Will send to ${selectedCount} selected student${selectedCount > 1 ? 's' : ''}`;
        } else {
            document.getElementById('emailPreview').classList.add('hidden');
        }

        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        modal.classList.add('items-center', 'justify-center');

        // Add preview update on input
        const messageTextarea = document.querySelector('#emailForm textarea[name="message"]');
        const subjectInput = document.querySelector('#emailForm input[name="subject"]');

        function updatePreview() {
            const subject = subjectInput.value || 'Your Subject';
            const message = messageTextarea.value || 'Your message';
            const previewText = document.getElementById('emailPreviewText');
            if (previewText && selectedCount > 0) {
                previewText.innerHTML = `
                    <strong>Subject:</strong> ${subject}<br>
                    <strong>Message Preview:</strong> ${message.substring(0, 100)}${message.length > 100 ? '...' : ''}<br>
                    <strong>Recipients:</strong> ${selectedCount} student${selectedCount > 1 ? 's' : ''}
                `;
            }
        }

        // Remove old listeners and add new ones
        const newMessageTextarea = messageTextarea.cloneNode(true);
        const newSubjectInput = subjectInput.cloneNode(true);
        messageTextarea.parentNode.replaceChild(newMessageTextarea, messageTextarea);
        subjectInput.parentNode.replaceChild(newSubjectInput, subjectInput);

        newMessageTextarea.addEventListener('input', updatePreview);
        newSubjectInput.addEventListener('input', updatePreview);
    }

    function closeEmailModal() {
        const modal = document.getElementById('emailModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.getElementById('emailForm').reset();
        document.getElementById('emailPreview').classList.add('hidden');
        const submitBtn = document.getElementById('emailSubmitBtn');
        const submitText = document.getElementById('emailSubmitText');
        const submitSpinner = document.getElementById('emailSubmitSpinner');
        submitBtn.disabled = false;
        submitText.textContent = 'Send Emails';
        submitSpinner.classList.add('hidden');
    }

    // SMS Modal functions
    function openSMSModal() {
        const selected = getSelectedStudentIds();
        const modal = document.getElementById('smsModal');
        if (selected.length === 0) {
            // Allow opening modal even without selection - user can check "send to all"
            // Validation will happen on form submit
        }
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        modal.classList.add('items-center', 'justify-center');

        // Character counter - update on input
        const textarea = document.querySelector('#smsForm textarea[name="message"]');
        const counter = document.getElementById('smsCharCount');
        if (textarea && counter) {
            // Remove existing listeners by cloning and replacing
            const newTextarea = textarea.cloneNode(true);
            textarea.parentNode.replaceChild(newTextarea, textarea);

            // Add new listener
            newTextarea.addEventListener('input', function() {
                counter.textContent = this.value.length;
            });

            // Update initial count
            counter.textContent = newTextarea.value.length;
        }
    }

    function closeSMSModal() {
        const modal = document.getElementById('smsModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.getElementById('smsForm').reset();
        document.getElementById('smsCharCount').textContent = '0';
    }

    // Get selected student IDs
    function getSelectedStudentIds() {
        const checkboxes = document.querySelectorAll('.student-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    // Use global showToast function from notification-toast component

    // Send Email Offers
    function sendEmailOffers(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const selectedIds = getSelectedStudentIds();

        if (selectedIds.length === 0 && !formData.get('send_to_all')) {
            showToast('Please select at least one student or check "Send to all students"', 'error');
            return;
        }

        const data = {
            student_ids: formData.get('send_to_all') ? [] : selectedIds,
            send_to_all: formData.get('send_to_all') ? true : false,
            subject: formData.get('subject'),
            message: formData.get('message'),
        };

        // Show loading
        const submitBtn = document.getElementById('emailSubmitBtn');
        const submitText = document.getElementById('emailSubmitText');
        const submitSpinner = document.getElementById('emailSubmitSpinner');
        const originalText = submitText.textContent;

        submitBtn.disabled = true;
        submitText.textContent = 'Sending...';
        submitSpinner.classList.remove('hidden');

        fetch('{{ route("admin.students.bulk.send-email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Server error occurred');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.failed_count > 0) {
                    // Partial success
                    let errorDetails = '';
                    if (data.failed_emails && data.failed_emails.length > 0) {
                        const failedList = data.failed_emails.slice(0, 3).map(email => {
                            const errorMsg = typeof email.error === 'string' ? email.error : 'Email sending failed';
                            return `${email.email || 'Unknown'}: ${errorMsg.substring(0, 50)}`;
                        }).join('\n');
                        errorDetails = `\n\nFailed emails:\n${failedList}`;
                        if (data.failed_emails.length > 3) {
                            errorDetails += `\n... and ${data.failed_emails.length - 3} more`;
                        }
                    }
                    showToast(
                        `Successfully sent ${data.sent_count} email(s). ${data.failed_count} failed.${errorDetails}`,
                        'error'
                    );
                } else {
                    // Complete success
                    showToast(
                        `Successfully sent ${data.sent_count} email(s) to all selected students!`,
                        'success'
                    );
                    closeEmailModal();
                    clearSelection();
                }
            } else {
                // All failed
                let errorMsg = data.message || 'Failed to send emails';
                if (data.failed_emails && data.failed_emails.length > 0) {
                    const firstError = data.failed_emails[0];
                    const errorText = typeof firstError.error === 'string' ? firstError.error : 'Email sending failed';
                    errorMsg += ': ' + errorText.substring(0, 100);
                }
                showToast(errorMsg, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'An error occurred while sending emails.';
            if (error.message) {
                errorMessage = error.message;
            }
            showToast(
                errorMessage + '. Please check your email configuration in Admin Settings.',
                'error'
            );
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitText.textContent = originalText;
            submitSpinner.classList.add('hidden');
        });
    }

    // Send SMS Offers
    function sendSMSOffers(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const selectedIds = getSelectedStudentIds();

        if (selectedIds.length === 0 && !formData.get('send_to_all')) {
            alert('Please select at least one student or check "Send to all students"');
            return;
        }

        const data = {
            student_ids: formData.get('send_to_all') ? [] : selectedIds,
            send_to_all: formData.get('send_to_all') ? true : false,
            message: formData.get('message'),
        };

        // Show loading
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        fetch('{{ route("admin.students.bulk.send-sms") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            // Check if response is ok
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Server error occurred');
                });
            }
            return response.json();
        })
        .then(data => {
            // Build detailed message
            let message = data.message || '';

            if (data.success) {
                if (data.failed_count > 0) {
                    message += '\n\n' + data.failed_count + ' SMS failed to send.';
                    if (data.failed_mobiles && data.failed_mobiles.length > 0) {
                        message += '\n\nFailed SMS:\n';
                        data.failed_mobiles.slice(0, 5).forEach(function(mobile) {
                            message += '- ' + mobile.mobile + ' (' + mobile.name + '): ' + mobile.error + '\n';
                        });
                        if (data.failed_mobiles.length > 5) {
                            message += '... and ' + (data.failed_mobiles.length - 5) + ' more';
                        }
                    }
                    alert(message);
                } else {
                    alert(message);
                    closeSMSModal();
                    clearSelection();
                }
            } else {
                // All SMS failed or error occurred
                let errorMsg = 'Error: ' + message;
                if (data.failed_mobiles && data.failed_mobiles.length > 0) {
                    errorMsg += '\n\nFailed SMS:\n';
                    data.failed_mobiles.slice(0, 5).forEach(function(mobile) {
                        errorMsg += '- ' + mobile.mobile + ' (' + mobile.name + '): ' + mobile.error + '\n';
                    });
                }
                alert(errorMsg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'An error occurred while sending SMS.';
            if (error.message) {
                errorMessage = error.message;
            }
            alert('Error: ' + errorMessage + '\n\nPlease check:\n1. Your SMS service configuration\n2. Your internet connection\n3. Server logs for more details');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    }

    // Close modals on outside click
    document.getElementById('emailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEmailModal();
        }
    });

    document.getElementById('smsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSMSModal();
        }
    });
</script>

<style>
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slide-out {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
</style>
@endsection
