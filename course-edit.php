<?php
$pageTitle = "Edit Course";
require 'includes/header.php';
require '../config/database.php';


$feedback_message = '';
$feedback_class = '';
$instructor_id = $_SESSION['user_id'];
$course_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ? AND instructor_id = ?");
$stmt->bind_param("ii", $course_id, $instructor_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$course) {
  echo "<div class='container-fluid'><div class='alert alert-danger'>Access Denied.</div></div>";
  require 'includes/footer.php';
  exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $conn->begin_transaction();
  try {

    $course_title = $_POST['course_title'] ?? '';
    $course_description = $_POST['course_description'] ?? '';
    $category = $_POST['category'] ?? '';
    $course_thumbnail_db_path = $course['course_thumbnail'];
    if (isset($_FILES['course_thumbnail']) && $_FILES['course_thumbnail']['error'] == 0) {
      $target_dir_server = dirname(__DIR__) . '/assets/images/';
      $file_name = uniqid() . '-' . basename($_FILES["course_thumbnail"]["name"]);
      $target_file_server = $target_dir_server . $file_name;
      if (move_uploaded_file($_FILES["course_thumbnail"]["tmp_name"], $target_file_server)) {
        if ($course['course_thumbnail'] != 'assets/images/default-thumbnail.png' && file_exists(dirname(__DIR__) . '/' . $course['course_thumbnail'])) {
          unlink(dirname(__DIR__) . '/' . $course['course_thumbnail']);
        }
        $course_thumbnail_db_path = 'assets/images/' . $file_name;
      }
    }
    $stmt_update = $conn->prepare("UPDATE courses SET course_title = ?, course_description = ?, category = ?, course_thumbnail = ? WHERE course_id = ?");
    $stmt_update->bind_param("ssssi", $course_title, $course_description, $category, $course_thumbnail_db_path, $course_id);
    $stmt_update->execute();
    $stmt_update->close();
    $conn->query("DELETE FROM quizzes WHERE course_id = $course_id");
    $conn->query("DELETE FROM course_content WHERE course_id = $course_id");
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
    $feedback_message = "Course updated successfully!";
    $feedback_class = 'alert-success';
  } catch (Exception $e) {
    $conn->rollback();
    $feedback_message = "Error updating course: " . $e->getMessage();
    $feedback_class = 'alert-danger';
  }
}

$course = $conn->query("SELECT * FROM courses WHERE course_id = $course_id")->fetch_assoc();
$categories = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC")->fetch_all(MYSQLI_ASSOC);
$curriculum = $conn->query("SELECT * FROM course_content WHERE course_id = $course_id ORDER BY order_in_course")->fetch_all(MYSQLI_ASSOC);
$quizzes_data = [];
$quiz_ids_in_course = array_map(fn($item) => $item['content_url'], array_filter($curriculum, fn($item) => $item['content_type'] == 'quiz'));
if (!empty($quiz_ids_in_course)) {
  $quiz_ids_str = implode(',', array_filter($quiz_ids_in_course, 'is_numeric'));
  if (!empty($quiz_ids_str)) {
    $questions_result = $conn->query("SELECT * FROM quiz_questions WHERE quiz_id IN ($quiz_ids_str)");
    while ($question = $questions_result->fetch_assoc()) {
      $quizzes_data[$question['quiz_id']][] = $question;
    }
  }
}
?>

<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Edit Course</h1>
  <a href="dashboard.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
  <?php if ($feedback_message) : ?>
    <div class="alert <?php echo $feedback_class; ?> alert-dismissible fade show" role="alert"><?php echo $feedback_message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <form action="course-edit.php?id=<?php echo $course_id; ?>" method="POST" enctype="multipart/form-data">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Course Details</h6>
      </div>
      <div class="card-body">
        <div class="mb-3"><label class="form-label">Course Title</label><input type="text" class="form-control" name="course_title" value="<?php echo htmlspecialchars($course['course_title']); ?>" required></div>
        <div class="mb-3"><label class="form-label">Course Description</label><textarea class="form-control" name="course_description" rows="4" required><?php echo htmlspecialchars($course['course_description']); ?></textarea></div>
        <div class="row">
          <div class="col-md-6 mb-3"><label class="form-label">Category</label><select class="form-select" name="category" required><?php foreach ($categories as $cat): ?><option value="<?php echo htmlspecialchars($cat['category_name']); ?>" <?php if ($course['category'] == $cat['category_name']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option><?php endforeach; ?></select></div>
          <div class="col-md-6 mb-3"><label class="form-label">Change Course Thumbnail</label><input type="file" class="form-control" name="course_thumbnail" accept="image/*">
            <div class="mt-2"><small>Current:</small><img src="../<?php echo htmlspecialchars($course['course_thumbnail']); ?>" class="img-thumbnail" width="100"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Course Content (Lessons)</h6>
      </div>
      <div class="card-body" id="course-content-section">
        <?php foreach ($curriculum as $index => $item): ?>
          <div class="course-content-item card p-3 mb-3">
            <div class="d-flex justify-content-end"><button type="button" class="btn-close remove-content-btn"></button></div>
            <div class="mb-3"><label class="form-label">Lesson Type</label><select class="form-select content-type-select" name="content_type[]">
                <option value="video" <?php if ($item['content_type'] == 'video') echo 'selected'; ?>>Video</option>
                <option value="quiz" <?php if ($item['content_type'] == 'quiz') echo 'selected'; ?>>Quiz</option>
              </select></div>
            <div class="mb-3"><label class="form-label">Lesson Description</label><textarea class="form-control" name="content_description[]" rows="2"><?php echo htmlspecialchars($item['content_description']); ?></textarea></div>
            <div class="video-fields" style="<?php if ($item['content_type'] != 'video') echo 'display: none;'; ?>">
              <div class="mb-3"><label class="form-label">Video Title</label><input type="text" class="form-control" name="video_title[]" value="<?php echo htmlspecialchars($item['content_title']); ?>"></div>
              <!-- *** PHP FIX HERE: Only output URL for videos *** -->
              <div class="mb-3"><label class="form-label">YouTube URL</label><input type="url" class="form-control" name="content_url[]" value="<?php echo ($item['content_type'] == 'video') ? htmlspecialchars($item['content_url']) : ''; ?>"></div>
            </div>
            <div class="quiz-fields" style="<?php if ($item['content_type'] != 'quiz') echo 'display: none;'; ?>">
              <div class="mb-3"><label class="form-label">Quiz Title</label><input type="text" class="form-control" name="quiz_title[]" value="<?php echo htmlspecialchars($item['content_title']); ?>"></div>
              <div class="quiz-questions-container border p-3 rounded">
                <h6>Questions</h6>
                <?php if ($item['content_type'] == 'quiz' && isset($quizzes_data[$item['content_url']])): ?>
                  <?php foreach ($quizzes_data[$item['content_url']] as $q_index => $question): ?>
                    <div class="quiz-question border-bottom pb-2 mb-2">
                      <div class="d-flex justify-content-end"><button type="button" class="btn-close btn-sm remove-question-btn"></button></div>
                      <div class="mb-2"><input type="text" class="form-control" name="question_text[<?php echo $index; ?>][]" value="<?php echo htmlspecialchars($question['question_text']); ?>" required></div>
                      <div class="input-group mb-1"><span class="input-group-text">A</span><input type="text" class="form-control" name="option_a[<?php echo $index; ?>][]" value="<?php echo htmlspecialchars($question['option_a']); ?>" required></div>
                      <div class="input-group mb-1"><span class="input-group-text">B</span><input type="text" class="form-control" name="option_b[<?php echo $index; ?>][]" value="<?php echo htmlspecialchars($question['option_b']); ?>" required></div>
                      <div class="input-group mb-1"><span class="input-group-text">C</span><input type="text" class="form-control" name="option_c[<?php echo $index; ?>][]" value="<?php echo htmlspecialchars($question['option_c']); ?>" required></div>
                      <div class="input-group mb-1"><span class="input-group-text">D</span><input type="text" class="form-control" name="option_d[<?php echo $index; ?>][]" value="<?php echo htmlspecialchars($question['option_d']); ?>" required></div>
                      <select class="form-select form-select-sm" name="correct_option[<?php echo $index; ?>][]" required>
                        <option value="a" <?php if ($question['correct_option'] == 'a') echo 'selected'; ?>>A is Correct</option>
                        <option value="b" <?php if ($question['correct_option'] == 'b') echo 'selected'; ?>>B is Correct</option>
                        <option value="c" <?php if ($question['correct_option'] == 'c') echo 'selected'; ?>>C is Correct</option>
                        <option value="d" <?php if ($question['correct_option'] == 'd') echo 'selected'; ?>>D is Correct</option>
                      </select>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary mt-2 add-question-btn">Add Question</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="card-footer"><button type="button" class="btn btn-secondary" id="add-content-btn"><i class="fas fa-plus"></i> Add Lesson</button></div>
    </div>
    <div class="alert alert-warning"><strong>Note:</strong> Saving changes will overwrite all existing lessons with the content defined above.</div>
    <button type="submit" class="btn btn-primary btn-lg">Save All Changes</button>
  </form>
</div>


<?php
$conn->close();
require 'includes/footer.php';
?>