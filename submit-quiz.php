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