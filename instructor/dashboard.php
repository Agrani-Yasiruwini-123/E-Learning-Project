<?php
$pageTitle = "Instructor Dashboard";
require 'includes/header.php';
require '../config/database.php';

// Validate instructor session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

$feedback_message = '';
$feedback_class = '';
$instructor_id = (int)$_SESSION['user_id'];

try {
    // Handle course deletion
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_course'])) {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Security validation failed.");
        }
        
        $course_id_to_delete = filter_input(INPUT_POST, 'course_id_to_delete', FILTER_VALIDATE_INT);
        
        if (!$course_id_to_delete || $course_id_to_delete <= 0) {
            throw new Exception("Invalid course ID.");
        }

        // Begin transaction for data consistency
        $conn->begin_transaction();

        try {
            // First, delete enrollments for this course
            $stmt_enrollments = $conn->prepare("DELETE FROM enrollments WHERE course_id = ?");
            $stmt_enrollments->bind_param("i", $course_id_to_delete);
            $stmt_enrollments->execute();
            $stmt_enrollments->close();

            // Then delete the course
            $stmt_course = $conn->prepare("DELETE FROM courses WHERE course_id = ? AND instructor_id = ?");
            $stmt_course->bind_param("ii", $course_id_to_delete, $instructor_id);
            $stmt_course->execute();
            
            if ($stmt_course->affected_rows > 0) {
                $conn->commit();
                $feedback_message = "Course and all associated enrollments removed successfully!";
                $feedback_class = 'alert-success';
                
                // Regenerate CSRF token after successful operation
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } else {
                $conn->rollback();
                $feedback_message = "Course not found or you don't have permission to delete it.";
                $feedback_class = 'alert-warning';
            }
            $stmt_course->close();
            
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }

    // Generate CSRF token if not exists
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Fetch instructor's courses with additional details
    $stmt_courses = $conn->prepare("
        SELECT c.*, 
               COUNT(e.student_id) as enrollment_count
        FROM courses c 
        LEFT JOIN enrollments e ON c.course_id = e.course_id 
        WHERE c.instructor_id = ? 
        GROUP BY c.course_id 
        ORDER BY c.creation_date DESC
    ");
    $stmt_courses->bind_param("i", $instructor_id);
    $stmt_courses->execute();
    $courses_result = $stmt_courses->get_result();
    $courses = $courses_result->fetch_all(MYSQLI_ASSOC);
    $stmt_courses->close();

    // Calculate total statistics
    $total_enrollments = 0;
    $total_courses = count($courses);
    
    foreach ($courses as $course) {
        $total_enrollments += $course['enrollment_count'];
    }

} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $feedback_message = "An error occurred while processing your request.";
    $feedback_class = 'alert-danger';
}
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Instructor Dashboard</h1>
        <a href="course-add.php" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Add New Course
        </a>
    </div>

    <?php if ($feedback_message) : ?>
        <div class="alert <?php echo htmlspecialchars($feedback_class); ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($feedback_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                My Courses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_courses; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Enrollments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_enrollments; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Avg. Enrollments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $total_courses > 0 ? round($total_enrollments / $total_courses, 1) : 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">My Courses</h6>
            <a href="course-add.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Add New Course
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="coursesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Enrollments</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($courses)) : ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-book-open fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">You haven't created any courses yet.</p>
                                    <a href="course-add.php" class="btn btn-primary">Create Your First Course</a>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($courses as $course) : ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($course['course_title']); ?></strong>
                                        <?php if ($course['enrollment_count'] > 0) : ?>
                                            <span class="badge bg-success ms-1">Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($course['category']); ?></td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill">
                                            <?php echo $course['enrollment_count']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($course['creation_date'])); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="course-edit.php?id=<?php echo $course['course_id']; ?>" 
                                               class="btn btn-warning" title="Edit Course">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="course-view.php?id=<?php echo $course['course_id']; ?>" 
                                               class="btn btn-info" title="View Course">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-danger delete-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal"
                                                    data-id="<?php echo $course['course_id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($course['course_title']); ?>"
                                                    title="Delete Course">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
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
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove the course <strong id="delete-course-title" class="text-danger"></strong>?</p>
                <p class="text-muted small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    This will permanently delete the course and unenroll all students. This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <form action="dashboard.php" method="POST" class="w-100">
                    <input type="hidden" id="delete-course-id" name="course_id_to_delete">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="d-flex justify-content-between w-100">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_course" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Delete Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete modal handler
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            
            document.getElementById('delete-course-id').value = id;
            document.getElementById('delete-course-title').textContent = title;
        });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>

<?php
if (isset($conn)) {
    $conn->close();
}
require 'includes/footer.php';
?>