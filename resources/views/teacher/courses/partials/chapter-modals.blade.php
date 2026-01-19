<!-- Add Chapter Modal -->
<div id="addChapterModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <!-- Background overlay -->
    <div class="fixed inset-0 transition-opacity" onclick="closeModal('addChapterModal')" style="cursor: pointer;"></div>

    <!-- Modal panel -->
    <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all w-full max-w-md border-2 border-blue-500 pointer-events-auto" style="max-height: 90vh; overflow-y: auto;">
            <form action="{{ route('teacher.courses.chapters.store', $course->id) }}" method="POST">
                @csrf
                <div class="bg-white px-5 pt-6 pb-4 sm:px-6 sm:pb-5">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-gray-900" id="modal-title">Add New Chapter</h3>
                        <button type="button" onclick="closeModal('addChapterModal')" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label for="add_chapter_title" class="block text-sm font-semibold text-gray-700 mb-2">Chapter Title <span class="text-red-500">*</span></label>
                            <input type="text" id="add_chapter_title" name="title" required
                                   placeholder="Enter chapter title"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                        </div>
                        <div>
                            <label for="add_chapter_description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea id="add_chapter_description" name="description" rows="4"
                                      placeholder="Enter chapter description (optional)"
                                      class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm resize-none transition-colors"></textarea>
                        </div>
                        <div>
                            <label for="add_chapter_order" class="block text-sm font-semibold text-gray-700 mb-2">Order</label>
                            <input type="number" id="add_chapter_order" name="order" value="0" min="0"
                                   placeholder="0"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                            <p class="mt-1.5 text-xs text-gray-500">Lower numbers appear first</p>
                        </div>
                        <div class="flex items-center pt-1">
                            <input type="checkbox" id="add_chapter_is_free" name="is_free" value="1"
                                   class="h-4 w-4 text-blue-600 border-2 border-gray-300 rounded focus:ring-blue-500">
                            <label for="add_chapter_is_free" class="ml-2.5 text-sm font-medium text-gray-700">Mark as Free Chapter</label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-200">
                    <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors sm:ml-3 sm:w-auto">
                        Add Chapter
                    </button>
                    <button type="button" onclick="closeModal('addChapterModal')" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm border-2 border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors sm:mt-0 sm:w-auto">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Chapter Modal -->
<div id="editChapterModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <!-- Background overlay -->
    <div class="fixed inset-0 transition-opacity" onclick="closeModal('editChapterModal')" style="cursor: pointer;"></div>

    <!-- Modal panel -->
    <div class="relative flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all w-full max-w-md border-2 border-blue-500 pointer-events-auto" style="max-height: 90vh; overflow-y: auto;">
            <form id="editChapterForm" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-5 pt-6 pb-4 sm:px-6 sm:pb-5">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-gray-900" id="modal-title">Edit Chapter</h3>
                        <button type="button" onclick="closeModal('editChapterModal')" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label for="edit_chapter_title" class="block text-sm font-semibold text-gray-700 mb-2">Chapter Title <span class="text-red-500">*</span></label>
                            <input type="text" id="edit_chapter_title" name="title" required
                                   placeholder="Enter chapter title"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                        </div>
                        <div>
                            <label for="edit_chapter_description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea id="edit_chapter_description" name="description" rows="4"
                                      placeholder="Enter chapter description (optional)"
                                      class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm resize-none transition-colors"></textarea>
                        </div>
                        <div>
                            <label for="edit_chapter_order" class="block text-sm font-semibold text-gray-700 mb-2">Order</label>
                            <input type="number" id="edit_chapter_order" name="order" min="0"
                                   placeholder="0"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                            <p class="mt-1.5 text-xs text-gray-500">Lower numbers appear first</p>
                        </div>
                        <div class="flex items-center pt-1">
                            <input type="checkbox" id="edit_chapter_is_free" name="is_free" value="1"
                                   class="h-4 w-4 text-blue-600 border-2 border-gray-300 rounded focus:ring-blue-500">
                            <label for="edit_chapter_is_free" class="ml-2.5 text-sm font-medium text-gray-700">Mark as Free Chapter</label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-200">
                    <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors sm:ml-3 sm:w-auto">
                        Update Chapter
                    </button>
                    <button type="button" onclick="closeModal('editChapterModal')" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm border-2 border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors sm:mt-0 sm:w-auto">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
