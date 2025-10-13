<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Course</h1>
        <a href="dashboard.php" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Back to Dashboard
        </a>
    </div>

    <?php if ($feedback_message) : ?>
        <div class="alert <?php echo htmlspecialchars($feedback_class); ?> alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas <?php echo $feedback_class === 'alert-success' ? 'fa-check-circle' : ($feedback_class === 'alert-warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle'); ?> me-2"></i>
                <div><?php echo htmlspecialchars($feedback_message); ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="course-add.php" method="POST" enctype="multipart/form-data" id="courseForm" novalidate>
        <!-- Course Details Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Course Details
                </h6>
                <span class="badge bg-primary">Required</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="mb-4">
                            <label for="course_title" class="form-label fw-semibold">Course Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="course_title" name="course_title" 
                                   placeholder="Enter an engaging course title" required maxlength="255">
                            <div class="form-text">Make it descriptive and appealing to potential students.</div>
                            <div class="invalid-feedback">Please provide a course title.</div>
                        </div>

                        <div class="mb-4">
                            <label for="course_description" class="form-label fw-semibold">Course Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="course_description" name="course_description" 
                                      rows="5" placeholder="Describe what students will learn in this course..." required></textarea>
                            <div class="form-text d-flex justify-content-between">
                                <span>Be detailed about the learning outcomes.</span>
                                <span id="description-counter">0/2000 characters</span>
                            </div>
                            <div class="invalid-feedback">Please provide a course description.</div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label for="category" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Choose a category...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['category_name']); ?>">
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Categories help students find your course.</div>
                            <div class="invalid-feedback">Please select a category.</div>
                        </div>

                        <div class="mb-4">
                            <label for="course_thumbnail" class="form-label fw-semibold">Course Thumbnail</label>
                            <div class="file-upload-area">
                                <input type="file" class="form-control" id="course_thumbnail" name="course_thumbnail" 
                                       accept="image/*" onchange="previewImage(this)">
                                <div class="form-text">Recommended: 1280x720px, JPG or PNG, max 2MB</div>
                                <div class="invalid-feedback">Please choose a valid image file.</div>
                            </div>
                            
                            <div id="image-preview" class="mt-3 text-center d-none">
                                <img id="preview" class="img-thumbnail" style="max-height: 200px; display: none;">
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeImage()">
                                    <i class="fas fa-times me-1"></i>Remove Image
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="course_level" class="form-label fw-semibold">Difficulty Level</label>
                            <select class="form-select" id="course_level" name="course_level">
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate" selected>Intermediate</option>
                                <option value="Advanced">Advanced</option>
                                <option value="All Levels">All Levels</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Content Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-play-circle me-2"></i>Course Content
                </h6>
                <span class="badge bg-info" id="lesson-count">0 Lessons</span>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>Add lessons to your course. Each lesson can be a video, article, or quiz.</div>
                </div>

                <div id="course-content-section" class="lessons-container">
                    <!-- Lessons will be added here dynamically -->
                    <div class="empty-state text-center py-5" id="empty-lessons">
                        <i class="fas fa-video fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No lessons added yet</h5>
                        <p class="text-muted">Start by adding your first lesson to the course.</p>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <button type="button" class="btn btn-primary" id="add-lesson-btn">
                    <i class="fas fa-plus me-2"></i>Add New Lesson
                </button>
                <button type="button" class="btn btn-outline-secondary" id="add-section-btn">
                    <i class="fas fa-folder-plus me-2"></i>Add Section
                </button>
            </div>
        </div>

        <!-- Course Settings Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-cog me-2"></i>Course Settings
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" checked>
                            <label class="form-check-label fw-semibold" for="is_published">
                                Publish Course Immediately
                            </label>
                            <div class="form-text">If unchecked, the course will be saved as a draft.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="allow_enrollment" name="allow_enrollment" checked>
                            <label class="form-check-label fw-semibold" for="allow_enrollment">
                                Allow Student Enrollment
                            </label>
                            <div class="form-text">Students can enroll in this course.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <div>
                        <button type="submit" name="save_draft" class="btn btn-outline-primary me-2" value="draft">
                            <i class="fas fa-save me-2"></i>Save as Draft
                        </button>
                        <button type="submit" name="create_course" class="btn btn-primary btn-lg px-4" value="publish">
                            <i class="fas fa-rocket me-2"></i>Create Course
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Lesson Template (Hidden) -->
<template id="lesson-template">
    <div class="lesson-item card mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0 lesson-title">New Lesson</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary move-lesson-up">
                    <i class="fas fa-arrow-up"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary move-lesson-down">
                    <i class="fas fa-arrow-down"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger remove-lesson">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Lesson Title</label>
                        <input type="text" class="form-control lesson-title-input" name="lesson_titles[]" 
                               placeholder="Enter lesson title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lesson Description</label>
                        <textarea class="form-control" name="lesson_descriptions[]" 
                                  rows="2" placeholder="Brief description of this lesson"></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Lesson Type</label>
                        <select class="form-select lesson-type" name="lesson_types[]" required>
                            <option value="video">Video</option>
                            <option value="article">Article</option>
                            <option value="quiz">Quiz</option>
                            <option value="assignment">Assignment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content URL</label>
                        <input type="url" class="form-control" name="lesson_urls[]" 
                               placeholder="https://example.com/video">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (minutes)</label>
                        <input type="number" class="form-control" name="lesson_durations[]" 
                               min="1" max="480" placeholder="e.g., 30">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let lessonCount = 0;
    const courseContentSection = document.getElementById('course-content-section');
    const emptyLessons = document.getElementById('empty-lessons');
    const lessonCountBadge = document.getElementById('lesson-count');
    const lessonTemplate = document.getElementById('lesson-template');
    const addLessonBtn = document.getElementById('add-lesson-btn');
    const courseForm = document.getElementById('courseForm');
    const descriptionTextarea = document.getElementById('course_description');
    const descriptionCounter = document.getElementById('description-counter');

    // Character counter for description
    descriptionTextarea.addEventListener('input', function() {
        const length = this.value.length;
        descriptionCounter.textContent = `${length}/2000 characters`;
        if (length > 2000) {
            descriptionCounter.classList.add('text-danger');
        } else {
            descriptionCounter.classList.remove('text-danger');
        }
    });

    // Add new lesson
    addLessonBtn.addEventListener('click', function() {
        addNewLesson();
    });

    function addNewLesson() {
        const lessonClone = lessonTemplate.content.cloneNode(true);
        const lessonItem = lessonClone.querySelector('.lesson-item');
        
        lessonCount++;
        updateLessonCount();
        
        if (emptyLessons) {
            emptyLessons.style.display = 'none';
        }
        
        // Add event listeners for the new lesson
        const removeBtn = lessonItem.querySelector('.remove-lesson');
        const moveUpBtn = lessonItem.querySelector('.move-lesson-up');
        const moveDownBtn = lessonItem.querySelector('.move-lesson-down');
        const titleInput = lessonItem.querySelector('.lesson-title-input');
        
        removeBtn.addEventListener('click', function() {
            lessonItem.remove();
            lessonCount--;
            updateLessonCount();
            if (lessonCount === 0 && emptyLessons) {
                emptyLessons.style.display = 'block';
            }
        });
        
        moveUpBtn.addEventListener('click', function() {
            const prev = lessonItem.previousElementSibling;
            if (prev && prev.classList.contains('lesson-item')) {
                courseContentSection.insertBefore(lessonItem, prev);
            }
        });
        
        moveDownBtn.addEventListener('click', function() {
            const next = lessonItem.nextElementSibling;
            if (next) {
                courseContentSection.insertBefore(next, lessonItem);
            }
        });
        
        titleInput.addEventListener('input', function() {
            const title = this.value || 'New Lesson';
            lessonItem.querySelector('.lesson-title').textContent = title;
        });
        
        courseContentSection.appendChild(lessonItem);
    }

    function updateLessonCount() {
        lessonCountBadge.textContent = `${lessonCount} Lesson${lessonCount !== 1 ? 's' : ''}`;
    }

    // Form validation
    courseForm.addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });

    // Add a couple of empty lessons by default
    addNewLesson();
});

function previewImage(input) {
    const preview = document.getElementById('preview');
    const imagePreview = document.getElementById('image-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            imagePreview.classList.remove('d-none');
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    const input = document.getElementById('course_thumbnail');
    const preview = document.getElementById('preview');
    const imagePreview = document.getElementById('image-preview');
    
    input.value = '';
    preview.style.display = 'none';
    imagePreview.classList.add('d-none');
}
</script>

<style>
.lesson-item {
    border-left: 4px solid #007bff;
}

.lesson-item .card-header {
    cursor: move;
}

.file-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    transition: border-color 0.15s ease-in-out;
}

.file-upload-area:hover {
    border-color: #007bff;
}

.empty-state {
    color: #6c757d;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    transition: all 0.15s ease-in-out;
}

.lesson-item {
    transition: transform 0.2s ease-in-out;
}

.lesson-item:hover {
    transform: translateY(-2px);
}
</style>

<?php
if (isset($conn)) {
    $conn->close();
}
require 'includes/footer.php';
?>