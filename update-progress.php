<?php
require_once 'includes/functions.php';
require_once 'config/database.php';


require_login();


header('Content-Type: application/json');


if (isset($_POST['course_id']) && isset($_POST['content_id'])) {
  $course_id = intval($_POST['course_id']);
  $content_id = intval($_POST['content_id']);
  $student_id = $_SESSION['user_id'];


  $stmt_enroll = $conn->prepare("SELECT enrollment_id FROM enrollments WHERE student_id = ? AND course_id = ?");
  $stmt_enroll->bind_param("ii", $student_id, $course_id);
  $stmt_enroll->execute();
  $enrollment = $stmt_enroll->get_result()->fetch_assoc();
  $stmt_enroll->close();

  if (!$enrollment) {

    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Enrollment not found.']);
    exit();
  }
  $enrollment_id = $enrollment['enrollment_id'];


  $stmt_check = $conn->prepare("SELECT id FROM completed_content WHERE enrollment_id = ? AND content_id = ?");
  $stmt_check->bind_param("ii", $enrollment_id, $content_id);
  $stmt_check->execute();
  if ($stmt_check->get_result()->num_rows > 0) {

    echo json_encode(['success' => true, 'message' => 'Already marked as complete.']);
    exit();
  }
  $stmt_check->close();


  $stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM course_content WHERE course_id = ?");
  $stmt_total->bind_param("i", $course_id);
  $stmt_total->execute();
  $total_content = $stmt_total->get_result()->fetch_assoc()['total'];
  $stmt_total->close();

  $progress_increment = ($total_content > 0) ? (100 / $total_content) : 0;



  $stmt_update = $conn->prepare("UPDATE enrollments SET progress = LEAST(100, progress + ?) WHERE enrollment_id = ?");
  $stmt_update->bind_param("di", $progress_increment, $enrollment_id);

  if ($stmt_update->execute()) {
    $stmt_update->close();


    $stmt_mark = $conn->prepare("INSERT INTO completed_content (enrollment_id, content_id) VALUES (?, ?)");
    $stmt_mark->bind_param("ii", $enrollment_id, $content_id);
    $stmt_mark->execute();
    $stmt_mark->close();


    echo json_encode(['success' => true, 'message' => 'Progress updated successfully.']);
  } else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update progress in the database.']);
  }

  $conn->close();
} else {

  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
}
