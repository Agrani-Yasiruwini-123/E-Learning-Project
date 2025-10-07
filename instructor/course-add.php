<?php
$pageTitle = "Add New Course";
require 'includes/header.php';
require '../config/database.php';


$feedback_message = '';
$feedback_class = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $conn->begin_transaction();
  try {
    $course_title = $_POST['course_title'] ?? '';
    $course_description = $_POST['course_description'] ?? '';
    $category = $_POST['category'] ?? '';
    $instructor_id = $_SESSION['user_id'];
    $course_thumbnail_db_path = 'assets/images/default-thumbnail.png';
    if (isset($_FILES['course_thumbnail']) && $_FILES['course_thumbnail']['error'] == 0) {
      $target_dir_server = dirname(__DIR__) . '/assets/images/';
      $file_name = uniqid() . '-' . basename($_FILES["course_thumbnail"]["name"]);
      $target_file_server = $target_dir_server . $file_name;
      if (move_uploaded_file($_FILES["course_thumbnail"]["tmp_name"], $target_file_server)) {
        $course_thumbnail_db_path = 'assets/images/' . $file_name;
      }
    }
    $stmt = $conn->prepare("INSERT INTO courses (course_title, course_description, course_thumbnail, category, instructor_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $course_title, $course_description, $course_thumbnail_db_path, $category, $instructor_id);
    $stmt->execute();
    $course_id = $conn->insert_id;
    $stmt->close();
    $content_types = $_POST['content_type'] ?? [];
    foreach ($content_types as $index => $type) {
      $order = $index + 1;
      $content_desc = $_POST['content_description'][$index] ?? null;
      if ($type == 'video') {
        $title = $_POST['video_title'][$index];
        $url = $_POST['content_url'][$index];
        $stmt_content = $conn->prepare("INSERT INTO course_content (course_id, content_type, content_title, content_description, content_url, order_in_course) VALUES (?, 'video', ?, ?, ?, ?)");
        $stmt_content->bind_param("isssi", $course_id, $title, $content_desc, $url, $order);
        $stmt_content->execute();
      } elseif ($type == 'quiz') {
        $quiz_title = $_POST['quiz_title'][$index];
        $stmt_quiz = $conn->prepare("INSERT INTO quizzes (course_id, quiz_title) VALUES (?, ?)");
        $stmt_quiz->bind_param("is", $course_id, $quiz_title);
        $stmt_quiz->execute();
        $quiz_id = $conn->insert_id;
        $stmt_content = $conn->prepare("INSERT INTO course_content (course_id, content_type, content_title, content_description, content_url, order_in_course) VALUES (?, 'quiz', ?, ?, ?, ?)");
        $stmt_content->bind_param("isssi", $course_id, $quiz_title, $content_desc, $quiz_id, $order);
        $stmt_content->execute();
        if (isset($_POST['question_text'][$index])) {
          foreach ($_POST['question_text'][$index] as $q_index => $q_text) {
            $opt_a = $_POST['option_a'][$index][$q_index];
            $opt_b = $_POST['option_b'][$index][$q_index];
            $opt_c = $_POST['option_c'][$index][$q_index];
            $opt_d = $_POST['option_d'][$index][$q_index];
            $correct = $_POST['correct_option'][$index][$q_index];
            $stmt_q = $conn->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_q->bind_param("issssss", $quiz_id, $q_text, $opt_a, $opt_b, $opt_c, $opt_d, $correct);
            $stmt_q->execute();
          }
        }
      }
    }
    $conn->commit();
    $feedback_message = "Course added successfully! <a href='dashboard.php'>Return to Dashboard</a>";
    $feedback_class = 'alert-success';
  } catch (Exception $e) {
    $conn->rollback();
    $feedback_message = "Error adding course: " . $e->getMessage();
    $feedback_class = 'alert-danger';
  }
}
$categories = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC")->fetch_all(MYSQLI_ASSOC);
?>
<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Add New Course</h1>
  <?php if ($feedback_message) : ?>
    <div class="alert <?php echo $feedback_class; ?> alert-dismissible fade show" role="alert"><?php echo $feedback_message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <form action="course-add.php" method="POST" enctype="multipart/form-data">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Course Details</h6>
      </div>
      <div class="card-body">
        <div class="mb-3"><label class="form-label">Course Title</label><input type="text" class="form-control" name="course_title" required></div>
        <div class="mb-3"><label class="form-label">Course Description</label><textarea class="form-control" name="course_description" rows="4" required></textarea></div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Category</label>
            <select class="form-select" name="category" required>
              <option value="">Select a category...</option>
              <?php foreach ($categories as $cat): ?><option value="<?php echo htmlspecialchars($cat['category_name']); ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option><?php endforeach; ?>
            </select>
            <small class="form-text text-muted">Categories are managed by the site administrator.</small>
          </div>
          <div class="col-md-6 mb-3"><label class="form-label">Course Thumbnail</label><input type="file" class="form-control" name="course_thumbnail" accept="image/*"></div>
        </div>
      </div>
    </div>
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Course Content (Lessons)</h6>
      </div>
      <div class="card-body" id="course-content-section"></div>
      <div class="card-footer"><button type="button" class="btn btn-secondary" id="add-content-btn"><i class="fas fa-plus"></i> Add Lesson</button></div>
    </div>
    <button type="submit" class="btn btn-primary btn-lg">Create Course</button>
  </form>
</div>


<?php
$conn->close();
require 'includes/footer.php';
?>