<?php
$pageTitle = "Instructor Dashboard";
require 'includes/header.php';
require '../config/database.php';

$feedback_message = '';
$feedback_class = '';
$instructor_id = $_SESSION['user_id'];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_course'])) {
  $course_id_to_delete = $_POST['course_id_to_delete'] ?? 0;


  $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ? AND instructor_id = ?");
  $stmt->bind_param("ii", $course_id_to_delete, $instructor_id);

  if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
      $feedback_message = "Course removed successfully!";
      $feedback_class = 'alert-success';
    } else {
      $feedback_message = "Could not remove course. It may have already been deleted or you don't have permission.";
      $feedback_class = 'alert-warning';
    }
  } else {
    $feedback_message = "Error removing course: " . $stmt->error;
    $feedback_class = 'alert-danger';
  }
  $stmt->close();
}


$stmt_courses = $conn->prepare("SELECT * FROM courses WHERE instructor_id = ? ORDER BY creation_date DESC");
$stmt_courses->bind_param("i", $instructor_id);
$stmt_courses->execute();
$courses = $stmt_courses->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_courses->close();


$total_enrollments = 0;
if (!empty($courses)) {
  $course_ids = array_column($courses, 'course_id');
  $placeholders = implode(',', array_fill(0, count($course_ids), '?'));
  $types = str_repeat('i', count($course_ids));
  $stmt_enroll = $conn->prepare("SELECT COUNT(*) AS total FROM enrollments WHERE course_id IN ($placeholders)");
  $stmt_enroll->bind_param($types, ...$course_ids);
  $stmt_enroll->execute();
  $total_enrollments = $stmt_enroll->get_result()->fetch_assoc()['total'];
  $stmt_enroll->close();
}
?>
<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Instructor Dashboard</h1>

  <?php if ($feedback_message) : ?>
    <div class="alert <?php echo $feedback_class; ?> alert-dismissible fade show" role="alert">
      <?php echo $feedback_message; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Stats Cards Row -->
  <div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="card-title text-uppercase text-muted mb-2">My Courses</h6><span class="h2 mb-0 font-weight-bold"><?php echo count($courses); ?></span>
            </div>
            <div class="col-auto">
              <div class="icon-shape bg-primary text-white rounded-circle shadow"><i class="fas fa-book-open"></i></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="card-title text-uppercase text-muted mb-2">Total Enrollments</h6><span class="h2 mb-0 font-weight-bold"><?php echo $total_enrollments; ?></span>
            </div>
            <div class="col-auto">
              <div class="icon-shape bg-success text-white rounded-circle shadow"><i class="fas fa-users"></i></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Your Courses List Card -->
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">My Courses</h6>
      <a href="course-add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Add New Course</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>Title</th>
              <th>Category</th>
              <th>Enrollments</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($courses)) : ?>
              <tr>
                <td colspan="4" class="text-center">You have not created any courses yet.</td>
              </tr>
            <?php else : ?>
              <?php foreach ($courses as $course) : ?>
                <?php
                $stmt_count = $conn->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_id = ?");
                $stmt_count->bind_param("i", $course['course_id']);
                $stmt_count->execute();
                $enroll_count = $stmt_count->get_result()->fetch_assoc()['count'];
                $stmt_count->close();
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($course['course_title']); ?></td>
                  <td><?php echo htmlspecialchars($course['category']); ?></td>
                  <td><?php echo $enroll_count; ?></td>
                  <td class="text-center">
                    <a href="course-edit.php?id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    <!-- Remove Button -->
                    <button class="btn btn-sm btn-danger delete-btn"
                      data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="<?php echo $course['course_id']; ?>"
                      data-title="<?php echo htmlspecialchars($course['course_title']); ?>">
                      <i class="fas fa-trash"></i> Remove
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to remove the course <strong id="delete-course-title"></strong>? This will also unenroll all students. This action cannot be undone.
      </div>
      <div class="modal-footer">
        <form action="dashboard.php" method="POST">
          <input type="hidden" id="delete-course-id" name="course_id_to_delete">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="delete_course" class="btn btn-danger">Yes, Remove Course</button>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .icon-shape {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
  }

  .icon-shape i {
    font-size: 1.25rem;
  }

  .sidebar-footer {
    margin-top: auto;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const id = button.dataset.id;
      const title = button.dataset.title;
      deleteModal.querySelector('#delete-course-id').value = id;
      deleteModal.querySelector('#delete-course-title').textContent = title;
    });
  });
</script>

<?php
$conn->close();
require 'includes/footer.php';
?>