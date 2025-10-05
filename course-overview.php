<?php
$pageTitle = "Course Overview";
require 'includes/header.php';
require 'config/database.php';

$course_id = $_GET['id'] ?? 0;

if ($course_id == 0) {
}


$sql = "SELECT c.*, u.username AS instructor_name, 
        (SELECT COUNT(*) FROM enrollments WHERE course_id = c.course_id) as enrollment_count
        FROM courses c 
        JOIN users u ON c.instructor_id = u.user_id 
        WHERE c.course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
}

$stmt_content = $conn->prepare("SELECT content_title, content_type FROM course_content WHERE course_id = ? ORDER BY order_in_course");
$stmt_content->bind_param("i", $course_id);
$stmt_content->execute();
$curriculum = $stmt_content->get_result()->fetch_all(MYSQLI_ASSOC);


$is_enrolled = false;
if (isset($_SESSION['user_id'])) {
    $stmt_check = $conn->prepare("SELECT 1 FROM enrollments WHERE student_id = ? AND course_id = ?");
    $stmt_check->bind_param("ii", $_SESSION['user_id'], $course_id);
    $stmt_check->execute();
    $is_enrolled = $stmt_check->get_result()->num_rows > 0;
    $stmt_check->close();
}

$what_you_will_learn = [
    "Master the core concepts of " . htmlspecialchars($course['category']),
    "Build real-world projects from scratch",
    "Understand industry best practices",
    "Gain confidence to advance your career"
];

$video_count = count(array_filter($curriculum, fn($item) => $item['content_type'] == 'video'));
$quiz_count = count(array_filter($curriculum, fn($item) => $item['content_type'] == 'quiz'));
?>

<!-- Redesigned Banner with Background Image -->
<section class="overview-banner" style="background-image: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?php echo htmlspecialchars($course['course_thumbnail']); ?>');">
    <div class="container text-white">
        <div class="row">
            <div class="col-lg-8">
                <a href="courses.php" class="text-white-50 text-decoration-none mb-2 d-inline-block"><i class="fas fa-arrow-left"></i> Back to Courses</a>
                <h1 class="display-4 fw-bold"><?php echo htmlspecialchars($course['course_title']); ?></h1>
                <p class="lead lead-sm"><?php echo htmlspecialchars(substr($course['course_description'], 0, 150)) . '...'; ?></p>
                <p class="mb-2">Created by <a href="#" class="text-white fw-bold"><?php echo htmlspecialchars($course['instructor_name']); ?></a></p>
                <div class="d-flex text-white-50 small">
                    <span class="me-3"><i class="fas fa-play-circle me-1"></i> <?php echo $video_count; ?> Videos</span>
                    <span class="me-3"><i class="fas fa-question-circle me-1"></i> <?php echo $quiz_count; ?> Quizzes</span>
                    <span><i class="fas fa-users me-1"></i> <?php echo htmlspecialchars($course['enrollment_count']); ?> Students</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Left Column: Course Details -->
            <div class="col-lg-8">
                <!-- Sticky Card for Mobile View -->
                <div class="card shadow-lg border-0 mb-4 d-lg-none">
                    <img src="<?php echo htmlspecialchars($course['course_thumbnail']); ?>" class="card-img-top" alt="Course Thumbnail">
                    <div class="card-body text-center p-4">
                        <h3 class="card-title fw-bold">Free</h3>
                        <?php if ($is_enrolled): ?>
                            <a href="course-page.php?id=<?php echo $course['course_id']; ?>" class="btn btn-success btn-lg w-100">Go to Course</a>
                        <?php else: ?>
                            <a href="enroll.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary btn-lg w-100">Enroll Now</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- What you'll learn Card -->
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4">What You'll Learn</h3>
                        <div class="row">
                            <?php foreach ($what_you_will_learn as $item): ?>
                                <div class="col-md-6 mb-3 d-flex"><i class="fas fa-check-circle text-success me-2 mt-1"></i><span><?php echo $item; ?></span></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Functional Accordion Curriculum -->
                <h3 class="fw-bold mb-3">Course Curriculum</h3>
                <div class="accordion" id="curriculumAccordion">
                    <?php if (empty($curriculum)): ?>
                        <div class="accordion-item">
                            <div class="accordion-body text-muted">No curriculum has been added.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($curriculum as $index => $lesson): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>">
                                        <span class="fw-bold me-2">Section <?php echo $index + 1; ?>:</span> <?php echo htmlspecialchars($lesson['content_title']); ?>
                                    </button>
                                </h2>
                                <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" data-bs-parent="#curriculumAccordion">
                                    <div class="accordion-body">
                                        <?php $icon = $lesson['content_type'] == 'quiz' ? 'fa-question-circle' : 'fa-play-circle'; ?>
                                        <p><i class="fas <?php echo $icon; ?> me-2 text-primary"></i>This lesson is a <?php echo $lesson['content_type']; ?>. Enroll to access all content.</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Full Description & Instructor -->
                <div class="mt-5">
                    <h3 class="fw-bold mb-3">Description</h3>
                    <p class="lh-lg"><?php echo nl2br(htmlspecialchars($course['course_description'])); ?></p>
                </div>
            </div>

            <!-- Right Column: Sticky Card -->
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 1.5rem;">
                    <div class="card shadow-lg border-0">
                        <img src="<?php echo htmlspecialchars($course['course_thumbnail']); ?>" class="card-img-top" alt="Course Thumbnail">
                        <div class="card-body p-4">
                            <h2 class="card-title text-center fw-bold mb-4">Free</h2>
                            <?php if ($is_enrolled): ?>
                                <a href="course-page.php?id=<?php echo $course['course_id']; ?>" class="btn btn-success btn-lg w-100"><i class="fas fa-play-circle me-1"></i> Go to Course</a>
                            <?php else: ?>
                                <a href="enroll.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary btn-lg w-100">Enroll Now</a>
                            <?php endif; ?>
                            <hr>
                            <h6 class="fw-bold">This course includes:</h6>
                            <ul class="list-unstyled text-muted">
                                <li class="mb-2"><i class="fas fa-video me-2"></i> On-demand video</li>
                                <li class="mb-2"><i class="fas fa-question-circle me-2"></i> Quizzes</li>
                                <li class="mb-2"><i class="fas fa-infinity me-2"></i> Full lifetime access</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
    .overview-banner {
        background-size: cover;
        background-position: center;
        padding: 5rem 0;
    }

    .lead-sm {
        font-size: 1.1rem;
        font-weight: 300;
    }
</style>
<?php
$stmt->close();
$stmt_content->close();
$conn->close();
require 'includes/footer.php';
?>