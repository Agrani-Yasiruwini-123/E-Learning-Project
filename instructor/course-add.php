<?php
$pageTitle = "Add New Course";
require 'includes/header.php';
require '../config/database.php';

// Check if user is instructor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'instructor') {
    header('Location: login.php');
    exit;
}

$feedback_message = '';
$feedback_class = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    try {
        // Validate and sanitize input
        $course_title = trim($_POST['course_title'] ?? '');
        $course_description = trim($_POST['course_description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $instructor_id = $_SESSION['user_id'];
        $course_price = floatval($_POST['course_price'] ?? 0);
        $course_level = $_POST['course_level'] ?? 'beginner';
        $course_duration = intval($_POST['course_duration'] ?? 0);
        
        // Validate required fields
        if (empty($course_title) || empty($course_description) || empty($category)) {
            throw new Exception("All required fields must be filled.");
        }
        
        if (strlen($course_title) < 5) {
            throw new Exception("Course title must be at least 5 characters long.");
        }
        
        // Handle thumbnail upload
        $course_thumbnail_db_path = 'assets/images/default-thumbnail.png';
        if (isset($_FILES['course_thumbnail']) && $_FILES['course_thumbnail']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['course_thumbnail']['type'], $allowed_types)) {
                throw new Exception("Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.");
            }
            
            if ($_FILES['course_thumbnail']['size'] > $max_size) {
                throw new Exception("File size too large. Maximum size is 5MB.");
            }
            
            $target_dir_server = dirname(__DIR__) . '/assets/images/courses/';
            if (!is_dir($target_dir_server)) {
                mkdir($target_dir_server, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES["course_thumbnail"]["name"], PATHINFO_EXTENSION);
            $file_name = uniqid('course_') . '.' . $file_extension;
            $target_file_server = $target_dir_server . $file_name;
            
            if (move_uploaded_file($_FILES["course_thumbnail"]["tmp_name"], $target_file_server)) {
                $course_thumbnail_db_path = 'assets/images/courses/' . $file_name;
            }
        }
        
        // Insert course
        $stmt = $conn->prepare("INSERT INTO courses (course_title, course_description, course_thumbnail, category, instructor_id, price, level, duration, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssidsi", $course_title, $course_description, $course_thumbnail_db_path, $category, $instructor_id, $course_price, $course_level, $course_duration);
        $stmt->execute();
        $course_id = $conn->insert_id;
        $stmt->close();
        
        // Handle course content
        $content_types = $_POST['content_type'] ?? [];
        $content_titles = $_POST['content_title'] ?? [];
        $content_descriptions = $_POST['content_description'] ?? [];
        $content_urls = $_POST['content_url'] ?? [];
        $content_durations = $_POST['content_duration'] ?? [];
        
        foreach ($content_types as $index => $type) {
            if (empty($content_titles[$index])) continue;
            
            $order = $index + 1;
            $title = trim($content_titles[$index]);
            $description = trim($content_descriptions[$index] ?? '');
            $url = trim($content_urls[$index] ?? '');
            $duration = intval($content_durations[$index] ?? 0);
            
            if ($type == 'video') {
                // Validate YouTube URL
                if (!empty($url) && !preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/', $url)) {
                    throw new Exception("Invalid YouTube URL for video: " . $title);
                }
                
                $stmt_content = $conn->prepare("INSERT INTO course_content (course_id, content_type, content_title, content_description, content_url, duration, order_in_course, created_at) VALUES (?, 'video', ?, ?, ?, ?, ?, NOW())");
                $stmt_content->bind_param("isssii", $course_id, $title, $description, $url, $duration, $order);
                $stmt_content->execute();
                $stmt_content->close();
                
            } elseif ($type == 'quiz') {
                $stmt_quiz = $conn->prepare("INSERT INTO quizzes (course_id, quiz_title, duration, created_at) VALUES (?, ?, ?, NOW())");
                $stmt_quiz->bind_param("isi", $course_id, $title, $duration);
                $stmt_quiz->execute();
                $quiz_id = $conn->insert_id;
                $stmt_quiz->close();
                
                $stmt_content = $conn->prepare("INSERT INTO course_content (course_id, content_type, content_title, content_description, content_url, duration, order_in_course, created_at) VALUES (?, 'quiz', ?, ?, ?, ?, ?, NOW())");
                $content_url = strval($quiz_id);
                $stmt_content->bind_param("isssiii", $course_id, $title, $description, $content_url, $duration, $order);
                $stmt_content->execute();
                $stmt_content->close();
                
                // Handle quiz questions
                if (isset($_POST['question_text'][$index])) {
                    foreach ($_POST['question_text'][$index] as $q_index => $q_text) {
                        $q_text = trim($q_text);
                        if (empty($q_text)) continue;
                        
                        $opt_a = trim($_POST['option_a'][$index][$q_index] ?? '');
                        $opt_b = trim($_POST['option_b'][$index][$q_index] ?? '');
                        $opt_c = trim($_POST['option_c'][$index][$q_index] ?? '');
                        $opt_d = trim($_POST['option_d'][$index][$q_index] ?? '');
                        $correct = $_POST['correct_option'][$index][$q_index] ?? '';
                        
                        if (empty($opt_a) || empty($opt_b) || empty($correct)) {
                            throw new Exception("All questions must have at least options A, B and a correct answer selected.");
                        }
                        
                        $stmt_q = $conn->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                        $stmt_q->bind_param("issssss", $quiz_id, $q_text, $opt_a, $opt_b, $opt_c, $opt_d, $correct);
                        $stmt_q->execute();
                        $stmt_q->close();
                    }
                }
            }
        }
        
        $conn->commit();
        $feedback_message = "🎉 Course created successfully! <a href='course-edit.php?id=$course_id' class='alert-link'>Edit Course</a> | <a href='dashboard.php' class='alert-link'>Return to Dashboard</a>";
        $feedback_class = 'alert-success';
        
    } catch (Exception $e) {
        $conn->rollback();
        $feedback_message = "❌ Error adding course: " . $e->getMessage();
        $feedback_class = 'alert-danger';
    }
}

// Get categories
$categories = $conn->query("SELECT category_name FROM categories WHERE status = 'active' ORDER BY category_name ASC")->fetch_all(MYSQLI_ASSOC);
?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-primary"></i> Create New Course
        </h1>
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?php if ($feedback_message) : ?>
        <div class="alert <?php echo $feedback_class; ?> alert-dismissible fade show" role="alert">
            <?php echo $feedback_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="course-add.php" method="POST" enctype="multipart/form-data" id="courseForm" novalidate>
        <!-- Course Details Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-info-circle"></i> Course Details
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Course Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" name="course_title" 
                                   placeholder="Enter an engaging course title" required maxlength="255"
                                   value="<?php echo htmlspecialchars($_POST['course_title'] ?? ''); ?>">
                            <div class="form-text">Minimum 5 characters</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Course Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="course_description" rows="5" 
                                      placeholder="Describe what students will learn in this course, what skills they'll gain, and who this course is for..." 
                                      required><?php echo htmlspecialchars($_POST['course_description'] ?? ''); ?></textarea>
                            <div class="form-text">Be descriptive to attract students</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Course Thumbnail</label>
                            <div class="course-thumbnail-preview mb-3 text-center border rounded p-3 bg-light">
                                <img id="thumbnailPreview" src="../assets/images/default-thumbnail.png" 
                                     class="img-fluid rounded" style="max-height: 180px; display: block;">
                                <small class="text-muted d-block mt-2">Thumbnail Preview</small>
                            </div>
                            <input type="file" class="form-control" name="course_thumbnail" 
                                   accept="image/*" id="thumbnailInput">
                            <div class="form-text">Recommended: 1280x720 pixels, JPG/PNG/WebP, max 5MB</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select class="form-select" name="category" required>
                            <option value="">Select a category...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['category_name']); ?>" 
                                    <?php echo (($_POST['category'] ?? '') === $cat['category_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Course Level</label>
                        <select class="form-select" name="course_level">
                            <option value="beginner" <?php echo (($_POST['course_level'] ?? 'beginner') === 'beginner') ? 'selected' : ''; ?>>Beginner</option>
                            <option value="intermediate" <?php echo (($_POST['course_level'] ?? '') === 'intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="advanced" <?php echo (($_POST['course_level'] ?? '') === 'advanced') ? 'selected' : ''; ?>>Advanced</option>
                            <option value="all levels" <?php echo (($_POST['course_level'] ?? '') === 'all levels') ? 'selected' : ''; ?>>All Levels</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Price ($)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="course_price" 
                                   step="0.01" min="0" placeholder="0.00"
                                   value="<?php echo htmlspecialchars($_POST['course_price'] ?? '0'); ?>">
                        </div>
                        <div class="form-text">Set to 0 for free course</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Total Duration (hours)</label>
                        <input type="number" class="form-control" name="course_duration" 
                               min="0" step="0.5" placeholder="0"
                               value="<?php echo htmlspecialchars($_POST['course_duration'] ?? '0'); ?>">
                        <div class="form-text">Estimated total course length</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Content Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-play-circle"></i> Course Content
                </h6>
                <span class="badge bg-light text-dark" id="contentCount">0 lessons</span>
            </div>
            <div class="card-body" id="course-content-section">
                <div class="empty-state text-center py-5" id="emptyContentState">
                    <i class="fas fa-video fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No lessons added yet</h5>
                    <p class="text-muted">Start by adding your first lesson or quiz</p>
                </div>
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary" id="add-video-btn">
                        <i class="fas fa-video"></i> Add Video Lesson
                    </button>
                    <button type="button" class="btn btn-success" id="add-quiz-btn">
                        <i class="fas fa-question-circle"></i> Add Quiz
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="add-section-btn">
                        <i class="fas fa-folder"></i> Add Section
                    </button>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
            <a href="dashboard.php" class="btn btn-secondary me-md-2 px-4">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                <i class="fas fa-plus"></i> Create Course
            </button>
        </div>
    </form>
</div>

<!-- Content Templates -->
<template id="videoTemplate">
    <div class="course-content-item card border-primary mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary"><i class="fas fa-video"></i> Video</span>
                <span class="lesson-number fw-semibold text-muted"></span>
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-sm btn-outline-secondary move-up-btn" title="Move up">
                    <i class="fas fa-arrow-up"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary move-down-btn" title="Move down">
                    <i class="fas fa-arrow-down"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger remove-content-btn" title="Remove lesson">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" name="content_type[]" value="video">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lesson Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control content-title" name="content_title[]" required 
                               placeholder="Enter lesson title">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Duration (minutes)</label>
                        <input type="number" class="form-control content-duration" name="content_duration[]" 
                               min="0" placeholder="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Order</label>
                        <input type="number" class="form-control content-order" min="1" value="1" readonly>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Lesson Description</label>
                <textarea class="form-control" name="content_description[]" rows="2" 
                          placeholder="Brief description of what this lesson covers..."></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">YouTube URL <span class="text-danger">*</span></label>
                <input type="url" class="form-control youtube-url" name="content_url[]" 
                       placeholder="https://www.youtube.com/watch?v=..." required
                       pattern="^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$">
                <div class="form-text">Paste the full YouTube URL</div>
            </div>
            <div class="youtube-preview mt-2" style="display: none;">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <span>YouTube preview will be shown after valid URL is entered</span>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="quizTemplate">
    <div class="course-content-item card border-warning mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark"><i class="fas fa-question-circle"></i> Quiz</span>
                <span class="lesson-number fw-semibold text-muted"></span>
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-sm btn-outline-secondary move-up-btn" title="Move up">
                    <i class="fas fa-arrow-up"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary move-down-btn" title="Move down">
                    <i class="fas fa-arrow-down"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger remove-content-btn" title="Remove quiz">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" name="content_type[]" value="quiz">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Quiz Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control content-title" name="content_title[]" required 
                               placeholder="Enter quiz title">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Duration (minutes)</label>
                        <input type="number" class="form-control content-duration" name="content_duration[]" 
                               min="0" placeholder="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Order</label>
                        <input type="number" class="form-control content-order" min="1" value="1" readonly>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Quiz Description</label>
                <textarea class="form-control" name="content_description[]" rows="2" 
                          placeholder="Describe what this quiz will test..."></textarea>
            </div>
            
            <div class="quiz-questions-container border rounded p-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">
                        <i class="fas fa-list-ol"></i> Questions
                        <span class="badge bg-secondary question-count">0 questions</span>
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary add-question-btn">
                        <i class="fas fa-plus"></i> Add Question
                    </button>
                </div>
                <div class="questions-list"></div>
                <div class="no-questions text-center text-muted py-3" style="display: block;">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>No questions added yet</p>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="questionTemplate">
    <div class="quiz-question card mb-3 border">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary">
                    <i class="fas fa-question"></i> Question <span class="question-number"></span>
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-question-btn" title="Remove question">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Question Text <span class="text-danger">*</span></label>
                <input type="text" class="form-control question-text" name="question_text[{{index}}][]" 
                       placeholder="Enter your question..." required>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-success text-white fw-bold">A</span>
                        <input type="text" class="form-control" name="option_a[{{index}}][]" 
                               placeholder="Option A" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white fw-bold">B</span>
                        <input type="text" class="form-control" name="option_b[{{index}}][]" 
                               placeholder="Option B" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-warning fw-bold">C</span>
                        <input type="text" class="form-control" name="option_c[{{index}}][]" 
                               placeholder="Option C (optional)">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-info text-white fw-bold">D</span>
                        <input type="text" class="form-control" name="option_d[{{index}}][]" 
                               placeholder="Option D (optional)">
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label fw-semibold">Correct Answer <span class="text-danger">*</span></label>
                <select class="form-select correct-answer" name="correct_option[{{index}}][]" required>
                    <option value="" selected disabled>Select correct option</option>
                    <option value="a">Option A</option>
                    <option value="b">Option B</option>
                    <option value="c">Option C</option>
                    <option value="d">Option D</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseContentSection = document.getElementById('course-content-section');
    let contentCounter = 0;

    // Thumbnail preview
    document.getElementById('thumbnailInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('thumbnailPreview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Add video lesson
    document.getElementById('add-video-btn')?.addEventListener('click', function() {
        addContentItem('video');
    });

    // Add quiz
    document.getElementById('add-quiz-btn')?.addEventListener('click', function() {
        addContentItem('quiz');
    });

    function addContentItem(type) {
        const template = document.getElementById(type + 'Template');
        if (!template) return;

        const content = template.content.cloneNode(true);
        const contentItem = content.querySelector('.course-content-item');
        contentCounter++;
        
        // Update indices and numbers
        const index = Array.from(courseContentSection.querySelectorAll('.course-content-item')).length;
        contentItem.querySelector('.lesson-number').textContent = `Lesson ${contentCounter}`;
        contentItem.querySelector('.content-order').value = contentCounter;
        
        if (type === 'quiz') {
            contentItem.innerHTML = contentItem.innerHTML.replace(/{{index}}/g, index);
        }

        courseContentSection.appendChild(content);
        document.getElementById('emptyContentState')?.style.display = 'none';
        updateContentCount();
        
        // Scroll to new content
        contentItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Delegated event handling
    courseContentSection.addEventListener('click', function(e) {
        // Remove content item
        if (e.target.closest('.remove-content-btn')) {
            const item = e.target.closest('.course-content-item');
            if (confirm('Are you sure you want to remove this lesson?')) {
                item.remove();
                updateContentCount();
                renumberContentItems();
                if (courseContentSection.querySelectorAll('.course-content-item').length === 0) {
                    document.getElementById('emptyContentState').style.display = 'block';
                }
            }
        }
        
        // Add question to quiz
        if (e.target.closest('.add-question-btn')) {
            const container = e.target.closest('.quiz-questions-container');
            addQuestionToQuiz(container);
        }
        
        // Remove question
        if (e.target.closest('.remove-question-btn')) {
            const question = e.target.closest('.quiz-question');
            const container = question.closest('.quiz-questions-container');
            if (confirm('Are you sure you want to remove this question?')) {
                question.remove();
                updateQuestionCount(container);
            }
        }
        
        // Move content up
        if (e.target.closest('.move-up-btn')) {
            const item = e.target.closest('.course-content-item');
            const prev = item.previousElementSibling;
            if (prev) {
                item.parentNode.insertBefore(item, prev);
                renumberContentItems();
            }
        }
        
        // Move content down
        if (e.target.closest('.move-down-btn')) {
            const item = e.target.closest('.course-content-item');
            const next = item.nextElementSibling;
            if (next) {
                item.parentNode.insertBefore(next, item);
                renumberContentItems();
            }
        }
    });

    // YouTube URL validation
    courseContentSection.addEventListener('input', function(e) {
        if (e.target.classList.contains('youtube-url')) {
            validateYouTubeUrl(e.target);
        }
    });

    function addQuestionToQuiz(container) {
        const questionsList = container.querySelector('.questions-list');
        const questionCount = questionsList.querySelectorAll('.quiz-question').length;
        const contentIndex = Array.from(courseContentSection.querySelectorAll('.course-content-item'))
            .indexOf(container.closest('.course-content-item'));

        const template = document.getElementById('questionTemplate');
        const question = template.content.cloneNode(true);
        const questionElement = question.querySelector('.quiz-question');
        
        // Update indices and question number
        questionElement.innerHTML = questionElement.innerHTML
            .replace(/{{index}}/g, contentIndex)
            .replace('Question <span class="question-number"></span>', 
                     `Question <span class="question-number">${questionCount + 1}</span>`);

        questionsList.appendChild(questionElement);
        container.querySelector('.no-questions').style.display = 'none';
        updateQuestionCount(container);
    }

    function updateQuestionCount(container) {
        const count = container.querySelectorAll('.quiz-question').length;
        container.querySelector('.question-count').textContent = `${count} question${count !== 1 ? 's' : ''}`;
        
        if (count === 0) {
            container.querySelector('.no-questions').style.display = 'block';
        }
    }

    function updateContentCount() {
        const count = courseContentSection.querySelectorAll('.course-content-item').length;
        document.getElementById('contentCount').textContent = `${count} lesson${count !== 1 ? 's' : ''}`;
    }

    function renumberContentItems() {
        const items = courseContentSection.querySelectorAll('.course-content-item');
        items.forEach((item, index) => {
            item.querySelector('.lesson-number').textContent = `Lesson ${index + 1}`;
            item.querySelector('.content-order').value = index + 1;
        });
        contentCounter = items.length;
    }

    function validateYouTubeUrl(input) {
        const url = input.value.trim();
        const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/;
        
        if (url && !youtubeRegex.test(url)) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        } else if (url) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else {
            input.classList.remove('is-invalid', 'is-valid');
        }
    }

    // Form validation
    document.getElementById('courseForm')?.addEventListener('submit', function(e) {
        const contentItems = courseContentSection.querySelectorAll('.course-content-item');
        let isValid = true;

        if (contentItems.length === 0) {
            alert('Please add at least one lesson or quiz to the course.');
            isValid = false;
            e.preventDefault();
            return;
        }

        // Validate each content item
        contentItems.forEach((item, index) => {
            const title = item.querySelector('.content-title');
            if (!title.value.trim()) {
                title.classList.add('is-invalid');
                isValid = false;
            } else {
                title.classList.remove('is-invalid');
            }

            // Validate YouTube URLs for video lessons
            const youtubeUrl = item.querySelector('.youtube-url');
            if (youtubeUrl && !youtubeUrl.value.trim()) {
                youtubeUrl.classList.add('is-invalid');
                isValid = false;
            }

            // Validate quiz questions
            const questions = item.querySelectorAll('.quiz-question');
            if (questions.length === 0 && item.querySelector('input[value="quiz"]')) {
                alert(`Quiz "${title.value}" must have at least one question.`);
                isValid = false;
            }

            questions.forEach(question => {
                const correctAnswer = question.querySelector('.correct-answer');
                if (!correctAnswer.value) {
                    correctAnswer.classList.add('is-invalid');
                    isValid = false;
                } else {
                    correctAnswer.classList.remove('is-invalid');
                }
            });
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fix the errors in the form before submitting.');
            // Scroll to first error
            const firstError = courseContentSection.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });
});
</script>

<?php
$conn->close();
require 'includes/footer.php';
?>