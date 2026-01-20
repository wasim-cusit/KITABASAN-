<!-- Add Lesson Modal -->
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-labelledby="addLessonModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 0;">
            <div class="modal-header bg-primary text-white" style="border-radius: 0;">
                <h5 class="modal-title" id="addLessonModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New Lesson
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addLessonForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_lesson_title" class="form-label fw-semibold">
                            Lesson Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="add_lesson_title" name="title" required
                               placeholder="Enter lesson title" style="border-radius: 0;">
                    </div>
                    <div class="mb-3">
                        <label for="add_lesson_description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="add_lesson_description" name="description" rows="3"
                                  placeholder="Enter lesson description (optional)" style="border-radius: 0;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="add_lesson_order" class="form-label fw-semibold">Order</label>
                        <input type="number" class="form-control" id="add_lesson_order" name="order" value="0" min="0"
                               placeholder="0" style="border-radius: 0;">
                    </div>
                    <div class="mb-3">
                        <label for="add_lesson_status" class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="add_lesson_status" name="status" style="border-radius: 0;">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="add_lesson_is_free" name="is_free" value="1">
                        <label class="form-check-label" for="add_lesson_is_free">
                            Free Lesson
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="border-radius: 0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Add Lesson
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Lesson Modal -->
<div class="modal fade" id="editLessonModal" tabindex="-1" aria-labelledby="editLessonModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 0;">
            <div class="modal-header bg-primary text-white" style="border-radius: 0;">
                <h5 class="modal-title" id="editLessonModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Lesson
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editLessonForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_lesson_title" class="form-label fw-semibold">
                            Lesson Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="edit_lesson_title" name="title" required
                               placeholder="Enter lesson title" style="border-radius: 0;">
                    </div>
                    <div class="mb-3">
                        <label for="edit_lesson_description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="edit_lesson_description" name="description" rows="3"
                                  placeholder="Enter lesson description (optional)" style="border-radius: 0;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lesson_order" class="form-label fw-semibold">Order</label>
                        <input type="number" class="form-control" id="edit_lesson_order" name="order" min="0"
                               placeholder="0" style="border-radius: 0;">
                    </div>
                    <div class="mb-3">
                        <label for="edit_lesson_status" class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="edit_lesson_status" name="status" style="border-radius: 0;">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_lesson_is_free" name="is_free" value="1">
                        <label class="form-check-label" for="edit_lesson_is_free">
                            Free Lesson
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="border-radius: 0;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Update Lesson
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
