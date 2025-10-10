<?php
require_once 'includes/functions.php';
require 'config/database.php';

require_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $quiz_id = $_POST['quiz_id'] ?? 0;
  $course_id = $_POST['course_id'] ?? 0;
  $content_id = $_POST['content_id'] ?? 0;
  $user_answers = $_POST['answers'] ?? [];
  $student_id = $_SESSION['user_id'];


  if ($quiz_id == 0 || $course_id == 0 || $content_id == 0 || empty($user_answers)) {
    header("Location: course-page.php?id=$course_id&error=InvalidSubmission");
    exit();
  }



  $stmt_correct = $conn->prepare("SELECT question_id, correct_option FROM quiz_questions WHERE quiz_id = ?");
  $stmt_correct->bind_param("i", $quiz_id);
  $stmt_correct->execute();
  $correct_answers = $stmt_correct->get_result()->fetch_all(MYSQLI_ASSOC);
  $stmt_correct->close();

  $total_questions = count($correct_answers);
  $score = 0;
  foreach ($correct_answers as $correct) {
    $qid = $correct['question_id'];
    if (isset($user_answers[$qid]) && $user_answers[$qid] == $correct['correct_option']) {
      $score++;
    }
  }

  $percentage_score = ($total_questions > 0) ? round(($score / $total_questions) * 100) : 0;
  $passing_score = 70;



  $stmt_enroll = $conn->prepare("SELECT enrollment_id FROM enrollments WHERE student_id = ? AND course_id = ?");
  $stmt_enroll->bind_param("ii", $student_id, $course_id);
  $stmt_enroll->execute();
  $enrollment = $stmt_enroll->get_result()->fetch_assoc();
  $enrollment_id = $enrollment['enrollment_id'] ?? null;
  $stmt_enroll->close();

  $message = "You scored $score out of $total_questions ($percentage_score%).";


  if ($enrollment_id) {
    if ($percentage_score >= $passing_score) {


      $stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM course_content WHERE course_id = ?");
      $stmt_total->bind_param("i", $course_id);
      $stmt_total->execute();
      $total_content = $stmt_total->get_result()->fetch_assoc()['total'];
      $stmt_total->close();

      $progress_increment = ($total_content > 0) ? (100 / $total_content) : 0;


      $stmt_update = $conn->prepare("UPDATE enrollments SET quiz_score = ?, progress = LEAST(100, progress + ?) WHERE enrollment_id = ?");
      $stmt_update->bind_param("idi", $percentage_score, $progress_increment, $enrollment_id);
      $stmt_update->execute();
      $stmt_update->close();


      $stmt_mark = $conn->prepare("INSERT INTO completed_content (enrollment_id, content_id) VALUES (?, ?)");
      $stmt_mark->bind_param("ii", $enrollment_id, $content_id);
      $stmt_mark->execute();
      $stmt_mark->close();


      $_SESSION['flash_message'] = $message . " Congratulations, you passed! Your progress has been updated.";
      $_SESSION['flash_class'] = 'alert-success';
    } else {

      $_SESSION['flash_message'] = $message . " You did not meet the passing score of $passing_score%. Please review the material and try again.";
      $_SESSION['flash_class'] = 'alert-danger';
    }
  } else {

    $_SESSION['flash_message'] = "Could not record your score as your enrollment was not found.";
    $_SESSION['flash_class'] = 'alert-danger';
  }


  $conn->close();

  header("Location: profile.php");
  exit();
} else {

  header("Location: index.php");
  exit();
}
