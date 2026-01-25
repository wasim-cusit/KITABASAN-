<!-- Add Topic Modal -->
<div class="modal fade" id="addTopicModal" tabindex="-1" aria-labelledby="addTopicModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 0;">
            <div class="modal-header bg-primary text-white" style="border-radius: 0;">
                <h5 class="modal-title" id="addTopicModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New Topic
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addTopicForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_topic_title" class="form-label fw-semibold">
                            Topic Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="add_topic_title" name="title" required
                               placeholder="Enter topic title" style="border-radius: 0;">
                    </div>
                    <div class="mb-3">
                        <label for="add_topic_description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="add_topic_description" name="description" rows="3"
                                  placeholder="Enter topic description (optional)" style="border-radius: 0;"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_topic_type" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="add_topic_type" name="type" style="border-radius: 0;">
                                <option value="lecture">Lecture</option>
                                <option value="quiz">Quiz</option>
                                <option value="mcq">MCQ</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_topic_order" class="form-label fw-semibold">Order</label>
                            <input type="number" class="form-control" id="add_topic_order" name="order" value="0" min="0"
                                   placeholder="0" style="border-radius: 0;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add_topic_video_host" class="form-label fw-semibold">Video Source</label>
                        <select class="form-select" id="add_topic_video_host" name="video_host" onchange="toggleVideoInputs('add')" style="border-radius: 0;">
                            <option value="">None</option>
                            <option value="youtube">YouTube</option>
                            <option value="bunny">Bunny Stream</option>
                            <option value="upload">Upload Video</option>
                        </select>
                    </div>
                    <div id="add_topic_youtube_input" class="mb-3" style="display: none;">
                        <label for="add_topic_video_id" class="form-label fw-semibold">YouTube Video ID or URL</label>
                        <input type="text" class="form-control" id="add_topic_video_id" name="video_id"
                               placeholder="Enter YouTube video ID or URL" style="border-radius: 0;"
                               oninput="showYouTubePreview('add', this.value)">
                        <div id="add_topic_youtube_preview" class="mt-3" style="display: none;">
                            <label class="form-label fw-semibold mb-2">Video Preview</label>
                            <div class="bg-black rounded" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                <iframe id="add_topic_youtube_preview_iframe"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    </div>
                    <div id="add_topic_bunny_input" class="mb-3" style="display: none;">
                        <label for="add_topic_bunny_video_id" class="form-label fw-semibold">Bunny Video ID</label>
                        <input type="text" class="form-control" id="add_topic_bunny_video_id" name="video_id"
                               placeholder="Enter Bunny Stream video ID" style="border-radius: 0;">
                    </div>
                    <div id="add_topic_upload_input" class="mb-3" style="display: none;">
                        <label for="add_topic_video_file" class="form-label fw-semibold">Video File</label>
                        <input type="file" class="form-control" id="add_topic_video_file" name="video_file" accept="video/*" style="border-radius: 0;">
                        <small class="form-text text-muted">Max file size: 5GB. Supported formats: MP4, WebM, MOV, AVI, FLV, WMV, MKV</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="add_topic_is_free" name="is_free" value="1">
                        <label class="form-check-label" for="add_topic_is_free">
                            Free Topic
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="border-radius: 0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Add Topic
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Topic Modal -->
<div class="modal fade" id="editTopicModal" tabindex="-1" aria-labelledby="editTopicModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 0;">
            <div class="modal-header bg-primary text-white" style="border-radius: 0;">
                <h5 class="modal-title" id="editTopicModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Topic
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTopicForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_topic_title" class="form-label fw-semibold">
                            Topic Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="edit_topic_title" name="title" required
                               placeholder="Enter topic title" style="border-radius: 0;">
                    </div>
                    <div class="mb-3">
                        <label for="edit_topic_description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="edit_topic_description" name="description" rows="3"
                                  placeholder="Enter topic description (optional)" style="border-radius: 0;"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_topic_type" class="form-label fw-semibold">Type</label>
                            <select class="form-select" id="edit_topic_type" name="type" style="border-radius: 0;">
                                <option value="lecture">Lecture</option>
                                <option value="quiz">Quiz</option>
                                <option value="mcq">MCQ</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_topic_order" class="form-label fw-semibold">Order</label>
                            <input type="number" class="form-control" id="edit_topic_order" name="order" min="0"
                                   placeholder="0" style="border-radius: 0;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_topic_video_host" class="form-label fw-semibold">Video Source</label>
                        <select class="form-select" id="edit_topic_video_host" name="video_host" onchange="toggleVideoInputs('edit')" style="border-radius: 0;">
                            <option value="">None</option>
                            <option value="youtube">YouTube</option>
                            <option value="bunny">Bunny Stream</option>
                            <option value="upload">Upload Video</option>
                        </select>
                    </div>
                    <div id="edit_topic_youtube_input" class="mb-3" style="display: none;">
                        <label for="edit_topic_video_id" class="form-label fw-semibold">YouTube Video ID or URL</label>
                        <input type="text" class="form-control" id="edit_topic_video_id" name="video_id"
                               placeholder="Enter YouTube video ID or URL" style="border-radius: 0;"
                               oninput="showYouTubePreview('edit', this.value)">
                        <div id="edit_topic_youtube_preview" class="mt-3" style="display: none;">
                            <label class="form-label fw-semibold mb-2">Video Preview</label>
                            <div class="bg-black rounded" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                <iframe id="edit_topic_youtube_preview_iframe"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    </div>
                    <div id="edit_topic_bunny_input" class="mb-3" style="display: none;">
                        <label for="edit_topic_bunny_video_id" class="form-label fw-semibold">Bunny Video ID</label>
                        <input type="text" class="form-control" id="edit_topic_bunny_video_id" name="video_id"
                               placeholder="Enter Bunny Stream video ID" style="border-radius: 0;">
                    </div>
                    <div id="edit_topic_upload_input" class="mb-3" style="display: none;">
                        <label for="edit_topic_video_file" class="form-label fw-semibold">Video File</label>
                        <input type="file" class="form-control" id="edit_topic_video_file" name="video_file" accept="video/*" style="border-radius: 0;">
                        <small class="form-text text-muted">Max file size: 5GB. Supported formats: MP4, WebM, MOV, AVI, FLV, WMV, MKV</small>
                        <small class="form-text text-muted d-block mt-1">Leave empty to keep existing video</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_topic_is_free" name="is_free" value="1">
                        <label class="form-check-label" for="edit_topic_is_free">
                            Free Topic
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="border-radius: 0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Update Topic
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
