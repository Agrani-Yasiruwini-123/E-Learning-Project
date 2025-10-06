<?php
$pageTitle = "Course Enrollment";
require 'includes/header.php';
require_once 'includes/functions.php';
require 'config/database.php';


require_login();

$course_id = $_GET['id'] ?? 0;
$student_id = $_SESSION['user_id'];


if ($course_id == 0) {
  echo "<div class='container my-5'><div class='alert alert-danger'>Invalid Course ID provided.</div></div>";
  require 'includes/footer.php';
  exit();
}

$stmt = $conn->prepare("SELECT course_title FROM courses WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
  echo "<div class='container my-5'><div class='alert alert-danger'>The selected course does not exist.</div></div>";
  require 'includes/footer.php';
  exit();
}
$course_title = $course['course_title'];


$stmt = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$is_enrolled = $stmt->get_result()->num_rows > 0;


$message = '';
$message_class = '';
$is_new_enrollment = false;

if ($is_enrolled) {
  $message_heading = "You Are Already Enrolled";
  $message = "You can continue your learning journey right where you left off.";
  $message_class = "alert-warning";
  $icon_class = "fa-exclamation-triangle text-warning";
} else {

  $stmt_insert = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
  $stmt_insert->bind_param("ii", $student_id, $course_id);

  if ($stmt_insert->execute()) {
    $message_heading = "Enrollment Successful!";
    $message = "You have successfully enrolled in the course. Happy learning!";
    $message_class = "alert-success";
    $icon_class = "fa-check-circle text-success";
    $is_new_enrollment = true;
  } else {
    $message_heading = "Enrollment Failed";
    $message = "An unexpected error occurred. Please try again later. Error: " . $conn->error;
    $message_class = "alert-danger";
    $icon_class = "fa-times-circle text-danger";
  }
  $stmt_insert->close();
}

$stmt->close();
$conn->close();
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0 text-center">
        <div class="card-body p-4 p-sm-5">
          <i class="fas <?php echo $icon_class; ?> fa-5x mb-4"></i>

          <h1 class="h3 fw-bold mb-3"><?php echo $message_heading; ?></h1>

          <p class="text-muted"><strong>Course:</strong> <?php echo htmlspecialchars($course_title); ?></p>

          <div class="alert <?php echo $message_class; ?> mt-4">
            <?php echo $message; ?>
          </div>

          <hr class="my-4">

          <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <?php if ($is_new_enrollment) : ?>
              <a href="course-page.php?id=<?php echo $course_id; ?>" class="btn btn-primary btn-lg px-4 gap-3">Start Learning</a>
            <?php else :
            ?>
              <a href="course-page.php?id=<?php echo $course_id; ?>" class="btn btn-primary btn-lg px-4 gap-3">Continue Learning</a>
            <?php endif; ?>

            <a href="profile.php" class="btn btn-outline-secondary btn-lg px-4">View My Courses</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php
require 'includes/footer.php';
?>