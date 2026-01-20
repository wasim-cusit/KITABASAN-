<!-- Add Chapter Modal -->
<div class="modal fade" id="addChapterModal" tabindex="-1" aria-labelledby="addChapterModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 0;">
            <div class="modal-header bg-primary text-white" style="border-radius: 0;">
                <h5 class="modal-title" id="addChapterModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New Chapter
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('teacher.courses.chapters.store', $course->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_chapter_title" class="form-label fw-semibold">
                            Chapter Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="add_chapter_title" name="title" required
                               placeholder="Enter chapter title" style="border-radius: 0;">
                    </div>
                    <div class="mb-3">
                        <label for="add_chapter_description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="add_chapter_description" name="description" rows="4"
                                  placeholder="Enter chapter description (optional)" style="border-radius: 0;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="add_chapter_order" class="form-label fw-semibold">Order</label>
                        <input type="number" class="form-control" id="add_chapter_order" name="order" value="0" min="0"
                               placeholder="0" style="border-radius: 0;">
                        <small class="form-text text-muted">Lower numbers appear first</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="add_chapter_is_free" name="is_free" value="1">
                        <label class="form-check-label" for="add_chapter_is_free">
                            Mark as Free Chapter
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="border-radius: 0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Add Chapter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Chapter Modal -->
<div class="modal fade" id="editChapterModal" tabindex="-1" aria-labelledby="editChapterModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 0;">
            <div class="modal-header bg-primary text-white" style="border-radius: 0;">
                <h5 class="modal-title" id="editChapterModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Chapter
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editChapterForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_chapter_title" class="form-label fw-semibold">
                            Chapter Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="edit_chapter_title" name="title" required
                               placeholder="Enter chapter title" style="border-radius: 0;">
                    </div>
                    <div class="mb-3">
                        <label for="edit_chapter_description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="edit_chapter_description" name="description" rows="4"
                                  placeholder="Enter chapter description (optional)" style="border-radius: 0;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_chapter_order" class="form-label fw-semibold">Order</label>
                        <input type="number" class="form-control" id="edit_chapter_order" name="order" min="0"
                               placeholder="0" style="border-radius: 0;">
                        <small class="form-text text-muted">Lower numbers appear first</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_chapter_is_free" name="is_free" value="1">
                        <label class="form-check-label" for="edit_chapter_is_free">
                            Mark as Free Chapter
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="border-radius: 0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Update Chapter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
