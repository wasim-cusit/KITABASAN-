<!-- Add Topic Modal -->
<div id="addTopicModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <!-- Background overlay -->
    <div class="fixed inset-0 transition-opacity" onclick="closeModal('addTopicModal')" style="cursor: pointer;"></div>
    <!-- Modal panel -->
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all w-full max-w-md border-2 border-blue-500 pointer-events-auto" style="max-height: 90vh; overflow-y: auto;">
            <form id="addTopicForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Add New Topic</h3>
                        <button type="button" onclick="closeModal('addTopicModal')" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label for="add_topic_title" class="block text-sm font-medium text-gray-700 mb-1">Topic Title *</label>
                            <input type="text" id="add_topic_title" name="title" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="add_topic_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="add_topic_description" name="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label for="add_topic_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select id="add_topic_type" name="type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="lecture">Lecture</option>
                                <option value="quiz">Quiz</option>
                                <option value="mcq">MCQ</option>
                            </select>
                        </div>
                        <div>
                            <label for="add_topic_order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                            <input type="number" id="add_topic_order" name="order" value="0" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="add_topic_video_host" class="block text-sm font-medium text-gray-700 mb-1">Video Source</label>
                            <select id="add_topic_video_host" name="video_host" onchange="toggleVideoInputs('add')"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">None</option>
                                <option value="youtube">YouTube</option>
                                <option value="bunny">Bunny Stream</option>
                                <option value="upload">Upload Video</option>
                            </select>
                        </div>
                        <div id="add_topic_youtube_input" class="hidden">
                            <label for="add_topic_video_id" class="block text-sm font-medium text-gray-700 mb-1">YouTube Video ID or URL</label>
                            <input type="text" id="add_topic_video_id" name="video_id"
                                   placeholder="Enter YouTube video ID or URL"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div id="add_topic_bunny_input" class="hidden">
                            <label for="add_topic_bunny_video_id" class="block text-sm font-medium text-gray-700 mb-1">Bunny Video ID</label>
                            <input type="text" id="add_topic_bunny_video_id" name="video_id"
                                   placeholder="Enter Bunny Stream video ID"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div id="add_topic_upload_input" class="hidden">
                            <label for="add_topic_video_file" class="block text-sm font-medium text-gray-700 mb-1">Video File</label>
                            <input type="file" id="add_topic_video_file" name="video_file" accept="video/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Max file size: 5GB. Supported formats: MP4, WebM, MOV, AVI, FLV, WMV, MKV</p>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="add_topic_is_free" name="is_free" value="1"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="add_topic_is_free" class="ml-2 text-sm text-gray-700">Free Topic</label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Add Topic
                    </button>
                    <button type="button" onclick="closeModal('addTopicModal')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Topic Modal -->
<div id="editTopicModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <!-- Background overlay -->
    <div class="fixed inset-0 transition-opacity" onclick="closeModal('editTopicModal')" style="cursor: pointer;"></div>
    <!-- Modal panel -->
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all w-full max-w-md border-2 border-blue-500 pointer-events-auto" style="max-height: 90vh; overflow-y: auto;">
            <form id="editTopicForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Edit Topic</h3>
                        <button type="button" onclick="closeModal('editTopicModal')" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label for="edit_topic_title" class="block text-sm font-medium text-gray-700 mb-1">Topic Title *</label>
                            <input type="text" id="edit_topic_title" name="title" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="edit_topic_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="edit_topic_description" name="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label for="edit_topic_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select id="edit_topic_type" name="type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="lecture">Lecture</option>
                                <option value="quiz">Quiz</option>
                                <option value="mcq">MCQ</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_topic_order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                            <input type="number" id="edit_topic_order" name="order" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="edit_topic_video_host" class="block text-sm font-medium text-gray-700 mb-1">Video Source</label>
                            <select id="edit_topic_video_host" name="video_host" onchange="toggleVideoInputs('edit')"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">None</option>
                                <option value="youtube">YouTube</option>
                                <option value="bunny">Bunny Stream</option>
                                <option value="upload">Upload Video</option>
                            </select>
                        </div>
                        <div id="edit_topic_youtube_input" class="hidden">
                            <label for="edit_topic_video_id" class="block text-sm font-medium text-gray-700 mb-1">YouTube Video ID or URL</label>
                            <input type="text" id="edit_topic_video_id" name="video_id"
                                   placeholder="Enter YouTube video ID or URL"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div id="edit_topic_bunny_input" class="hidden">
                            <label for="edit_topic_bunny_video_id" class="block text-sm font-medium text-gray-700 mb-1">Bunny Video ID</label>
                            <input type="text" id="edit_topic_bunny_video_id" name="video_id"
                                   placeholder="Enter Bunny Stream video ID"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div id="edit_topic_upload_input" class="hidden">
                            <label for="edit_topic_video_file" class="block text-sm font-medium text-gray-700 mb-1">Video File</label>
                            <input type="file" id="edit_topic_video_file" name="video_file" accept="video/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Max file size: 5GB. Supported formats: MP4, WebM, MOV, AVI, FLV, WMV, MKV</p>
                            <p class="mt-1 text-xs text-gray-600">Leave empty to keep existing video</p>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="edit_topic_is_free" name="is_free" value="1"
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="edit_topic_is_free" class="ml-2 text-sm text-gray-700">Free Topic</label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update Topic
                    </button>
                    <button type="button" onclick="closeModal('editTopicModal')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
