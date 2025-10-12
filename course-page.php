<?php
$pageTitle = "Learning";

require_once 'includes/session-manager.php';
require_once 'includes/functions.php';
require 'config/database.php';


require_login();
$user_id = $_SESSION['user_id'];
$course_id = $_GET['id'] ?? 0;


$stmt_enroll = $conn->prepare("SELECT enrollment_id, progress FROM enrollments WHERE student_id = ? AND course_id = ?");
$stmt_enroll->bind_param("ii", $user_id, $course_id);
$stmt_enroll->execute();
$enrollment = $stmt_enroll->get_result()->fetch_assoc();
if (!$enrollment) {

    die("<div class='container my-5'><div class='alert alert-danger'>You are not enrolled in this course.</div></div>");
}
$enrollment_id = $enrollment['enrollment_id'];
$course_progress = $enrollment['progress'];
$stmt_enroll->close();


$stmt_course_details = $conn->prepare("SELECT course_title FROM courses WHERE course_id = ?");
$stmt_course_details->bind_param("i", $course_id);
$stmt_course_details->execute();
$course_details = $stmt_course_details->get_result()->fetch_assoc();


$stmt_content = $conn->prepare("SELECT * FROM course_content WHERE course_id = ? ORDER BY order_in_course");
$stmt_content->bind_param("i", $course_id);
$stmt_content->execute();
$curriculum = $stmt_content->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_completed = $conn->prepare("SELECT content_id FROM completed_content WHERE enrollment_id = ?");
$stmt_completed->bind_param("i", $enrollment_id);
$stmt_completed->execute();
$completed_ids = array_column($stmt_completed->get_result()->fetch_all(MYSQLI_ASSOC), 'content_id');


$content_id = $_GET['content_id'] ?? ($curriculum[0]['content_id'] ?? 0);
$current_content = null;
$current_index = -1;
foreach ($curriculum as $index => $item) {
    if ($item['content_id'] == $content_id) {
        $current_content = $item;
        $current_index = $index;
        break;
    }
}
$is_completed = in_array($content_id, $completed_ids);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($current_content['content_title'] ?? 'Learning'); ?> | <?php echo htmlspecialchars($course_details['course_title']); ?></title>
    <link href="https:
    <link rel=" stylesheet" href="https:
    <link rel=" stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .learning-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .learning-sidebar {
            width: 350px;
            background-color: #fff;
            border-right: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
        }

        .learning-main {
            flex: 1;
            padding: 2rem;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .sidebar-body {
            overflow-y: auto;
            flex: 1;
        }

        .lesson-item.active {
            background-color: #e9ecef;
            color: #000;
            font-weight: bold;
        }

        .lesson-item {
            color: #495057;
            border-left: 3px solid transparent;
            padding-left: 1.25rem;
        }

        .lesson-item.active {
            border-left-color: var(--bs-primary);
        }
    </style>
</head>

<body>

    <div class="learning-wrapper">
        <!-- Sidebar -->
        <aside class="learning-sidebar">
            <div class="sidebar-header">
                <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($course_details['course_title']); ?></h5>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo intval($course_progress); ?>%;" aria-valuenow="<?php echo intval($course_progress); ?>"></div>
                </div>
                <a href="profile.php" class="btn btn-sm btn-outline-secondary w-100 mt-3">Back to My Courses</a>
            </div>
            <div class="sidebar-body">
                <ul class="nav flex-column py-2" id="curriculum-sidebar">
                    <?php foreach ($curriculum as $item): ?>
                        <li class="nav-item">
                            <a class="nav-link lesson-item d-flex justify-content-between align-items-center <?php echo ($item['content_id'] == $content_id) ? 'active' : ''; ?>"
                                href="course-page.php?id=<?php echo $course_id; ?>&content_id=<?php echo $item['content_id']; ?>"
                                data-content-id="<?php echo $item['content_id']; ?>">
                                <span>
                                    <?php
                                    $icon = 'fa-play-circle';
                                    if ($item['content_type'] == 'quiz') $icon = 'fa-question-circle';
                                    ?>
                                    <i class="fas <?php echo $icon; ?> fa-fw me-2 text-muted"></i>
                                    <?php echo htmlspecialchars($item['content_title']); ?>
                                </span>
                                <?php if (in_array($item['content_id'], $completed_ids)): ?>
                                    <i class="fas fa-check-circle text-success completion-icon"></i>
                                <?php else: ?>
                                    <i class="fas fa-check-circle text-success completion-icon d-none"></i>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="learning-main">
            <div class="main-content-area">
                <?php if (!$current_content): ?>
                    <div class="card shadow-sm">
                        <div class="card-body text-center p-5">
                            <h2>Welcome!</h2>
                            <p class="lead text-muted">This course doesn't have any content yet. Please select a lesson from the sidebar.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <h1 class="display-6 fw-bold mb-4"><?php echo htmlspecialchars($current_content['content_title']); ?></h1>

                    <!-- Content Display Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-0">
                            <?php if ($current_content['content_type'] == 'video'): ?>
                                <div class="video-container">
                                    <?php
                                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $current_content['content_url'], $match);
                                    $youtube_id = $match[1] ?? '';
                                    ?>
                                    <iframe src="https:
                                </div>
                            <?php elseif ($current_content['content_type'] == 'quiz'): ?>
                                <div class=" p-4">
                                        <?php
                                        $quiz_id = $current_content['content_url'];
                                        $stmt_quiz = $conn->prepare("SELECT quiz_title FROM quizzes WHERE quiz_id = ?");
                                        $stmt_quiz->bind_param("i", $quiz_id);
                                        $stmt_quiz->execute();
                                        $quiz = $stmt_quiz->get_result()->fetch_assoc();
                                        if ($quiz):
                                            $stmt_questions = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
                                            $stmt_questions->bind_param("i", $quiz_id);
                                            $stmt_questions->execute();
                                            $questions = $stmt_questions->get_result()->fetch_all(MYSQLI_ASSOC);
                                        ?>
                                            <?php if ($is_completed): ?>
                                                <div class="alert alert-success"><i class="fas fa-check-circle"></i> You have already completed this quiz.</div>
                                            <?php elseif (!empty($questions)): ?>
                                                <form action="submit-quiz.php" method="POST">
                                                    <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                                                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                                    <input type="hidden" name="content_id" value="<?php echo $content_id; ?>">
                                                    <?php foreach ($questions as $index => $q): ?>
                                                        <div class="mb-4">
                                                            <p><strong>Question <?php echo $index + 1; ?>:</strong> <?php echo htmlspecialchars($q['question_text']); ?></p>
                                                            <div class="form-check"><input class="form-check-input" type="radio" name="answers[<?php echo $q['question_id']; ?>]" id="q<?php echo $q['question_id']; ?>_a" value="a" required><label class="form-check-label" for="q<?php echo $q['question_id']; ?>_a"><?php echo htmlspecialchars($q['option_a']); ?></label></div>
                                                            <div class="form-check"><input class="form-check-input" type="radio" name="answers[<?php echo $q['question_id']; ?>]" id="q<?php echo $q['question_id']; ?>_b" value="b"><label class="form-check-label" for="q<?php echo $q['question_id']; ?>_b"><?php echo htmlspecialchars($q['option_b']); ?></label></div>
                                                            <div class="form-check"><input class="form-check-input" type="radio" name="answers[<?php echo $q['question_id']; ?>]" id="q<?php echo $q['question_id']; ?>_c" value="c"><label class="form-check-label" for="q<?php echo $q['question_id']; ?>_c"><?php echo htmlspecialchars($q['option_c']); ?></label></div>
                                                            <div class="form-check"><input class="form-check-input" type="radio" name="answers[<?php echo $q['question_id']; ?>]" id="q<?php echo $q['question_id']; ?>_d" value="d"><label class="form-check-label" for="q<?php echo $q['question_id']; ?>_d"><?php echo htmlspecialchars($q['option_d']); ?></label></div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <button type="submit" class="btn btn-success">Submit Quiz</button>
                                                </form>
                                            <?php else: ?>
                                                <div class="alert alert-warning">There are no questions in this quiz yet.</div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="alert alert-danger">Error: This quiz could not be loaded.</div>
                                        <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Lesson Description Card -->
                    <?php if (!empty($current_content['content_description'])): ?>
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Lesson Notes</h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($current_content['content_description'])); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Navigation & Completion Buttons -->
                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div>
                            <?php if ($current_index > 0): ?>
                                <a href="course-page.php?id=<?php echo $course_id; ?>&content_id=<?php echo $curriculum[$current_index - 1]['content_id']; ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Previous
                                </a>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if ($current_content['content_type'] != 'quiz' && !$is_completed): ?>
                                <button id="markCompleteBtn" class="btn btn-success" data-course-id="<?php echo $course_id; ?>" data-content-id="<?php echo $content_id; ?>">
                                    <i class="fas fa-check"></i> Mark as Complete
                                </button>
                            <?php endif; ?>

                            <?php if ($current_index + 1 < count($curriculum)): ?>
                                <a href="course-page.php?id=<?php echo $course_id; ?>&content_id=<?php echo $curriculum[$current_index + 1]['content_id']; ?>"
                                    class="btn btn-primary <?php if (!$is_completed) echo 'd-none'; ?>" id="nextLessonBtn">
                                    Next Lesson <i class="fas fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const markCompleteBtn = document.getElementById('markCompleteBtn');
            if (markCompleteBtn) {
                markCompleteBtn.addEventListener('click', function() {
                    const courseId = this.dataset.courseId;
                    const contentId = this.dataset.contentId;
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
                    fetch('update-progress.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `course_id=${courseId}&content_id=${contentId}`
                    }).then(response => response.json()).then(data => {
                        if (data.success) {
                            this.textContent = 'Completed!';
                            this.classList.replace('btn-success', 'btn-outline-success');
                            const nextBtn = document.getElementById('nextLessonBtn');
                            if (nextBtn) {
                                nextBtn.classList.remove('d-none');
                            }
                            const icon = document.querySelector(`#curriculum-sidebar a[data-content-id="${contentId}"] .completion-icon`);
                            if (icon) {
                                icon.classList.remove('d-none');
                            }
                        } else {
                            alert('Error: ' + data.message);
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-check"></i> Mark as Complete';
                        }
                    }).catch(error => {
                        alert('An error occurred. Please try again.');
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-check"></i> Mark as Complete';
                    });
                });
            }
        });
    </script>

    <?php

    if (isset($stmt_course_details)) $stmt_course_details->close();
    if (isset($stmt_content)) $stmt_content->close();
    if (isset($stmt_completed)) $stmt_completed->close();
    if (isset($stmt_quiz)) $stmt_quiz->close();
    if (isset($stmt_questions)) $stmt_questions->close();
    $conn->close();
    ?>
</body>

</html>