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