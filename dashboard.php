<?php
$pageTitle = "Instructor Dashboard";
require 'includes/header.php';
require '../config/database.php';

$feedback_message = '';
$feedback_class = '';
$instructor_id = $_SESSION['user_id'];

// Handle course deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_course'])) {
    $course_id_to_delete = $_POST['course_id_to_delete'] ?? 0;
    
    // Begin transaction for safe deletion
    $conn->begin_transaction();
    try {
        // First delete related records to maintain referential integrity
        $stmt1 = $conn->prepare("DELETE FROM course_content WHERE course_id = ?");
        $stmt1->bind_param("i", $course_id_to_delete);
        $stmt1->execute();
        $stmt1->close();
        
        $stmt2 = $conn->prepare("DELETE FROM enrollments WHERE course_id = ?");
        $stmt2->bind_param("i", $course_id_to_delete);
        $stmt2->execute();
        $stmt2->close();
        
        // Then delete the course
        $stmt3 = $conn->prepare("DELETE FROM courses WHERE course_id = ? AND instructor_id = ?");
        $stmt3->bind_param("ii", $course_id_to_delete, $instructor_id);
        $stmt3->execute();
        
        if ($stmt3->affected_rows > 0) {
            $conn->commit();
            $feedback_message = "Course removed successfully!";
            $feedback_class = 'alert-success';
        } else {
            $conn->rollback();
            $feedback_message = "Could not remove course. It may have already been deleted or you don't have permission.";
            $feedback_class = 'alert-warning';
        }
        $stmt3->close();
    } catch (Exception $e) {
        $conn->rollback();
        $feedback_message = "Error removing course: " . $e->getMessage();
        $feedback_class = 'alert-danger';
    }
}

// Fetch instructor's courses
$stmt_courses = $conn->prepare("SELECT * FROM courses WHERE instructor_id = ? ORDER BY creation_date DESC");
$stmt_courses->bind_param("i", $instructor_id);
$stmt_courses->execute();
$courses = $stmt_courses->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_courses->close();

// Calculate total enrollments and prepare enrollment counts for each course
$total_enrollments = 0;
$course_enrollments = [];

if (!empty($courses)) {
    $course_ids = array_column($courses, 'course_id');
    $placeholders = implode(',', array_fill(0, count($course_ids), '?'));
    $types = str_repeat('i', count($course_ids));
    
    // Get total enrollments across all courses
    $stmt_enroll = $conn->prepare("SELECT COUNT(*) AS total FROM enrollments WHERE course_id IN ($placeholders)");
    $stmt_enroll->bind_param($types, ...$course_ids);
    $stmt_enroll->execute();
    $total_enrollments = $stmt_enroll->get_result()->fetch_assoc()['total'];
    $stmt_enroll->close();
    
    // Get enrollment count for each individual course
    foreach ($courses as $course) {
        $stmt_count = $conn->prepare("SELECT COUNT(*) as count FROM enrollments WHERE course_id = ?");
        $stmt_count->bind_param("i", $course['course_id']);
        $stmt_count->execute();
        $enroll_count = $stmt_count->get_result()->fetch_assoc()['count'];
        $course_enrollments[$course['course_id']] = $enroll_count;
        $stmt_count->close();
    }
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
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                My Courses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($courses); ?></div>
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
                                Active Courses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($courses); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Actions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
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
            <a href="course-add.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Add New Course
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($courses)) : ?>
                <div class="text-center py-4">
                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Courses Yet</h5>
                    <p class="text-muted">You haven't created any courses yet. Start by creating your first course!</p>
                    <a href="course-add.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create Your First Course
                    </a>
                </div>
            <?php else : ?>
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
                            <?php foreach ($courses as $course) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($course['course_thumbnail'])) : ?>
                                                <img src="../<?php echo htmlspecialchars($course['course_thumbnail']); ?>" 
                                                     class="rounded me-3" 
                                                     width="40" 
                                                     height="40" 
                                                     style="object-fit: cover;"
                                                     alt="<?php echo htmlspecialchars($course['course_title']); ?>">
                                            <?php else : ?>
                                                <div class="rounded bg-light d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-book text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?php echo htmlspecialchars($course['course_title']); ?></strong>
                                                <?php if (strlen($course['course_description']) > 0) : ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars(substr($course['course_description'], 0, 50)); ?>
                                                        <?php if (strlen($course['course_description']) > 50) echo '...'; ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($course['category']); ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-users text-muted me-2"></i>
                                            <span><?php echo $course_enrollments[$course['course_id']] ?? 0; ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y', strtotime($course['creation_date'])); ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="course-edit.php?id=<?php echo $course['course_id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Edit Course">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="../course-view.php?id=<?php echo $course['course_id']; ?>" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="View Course"
                                               target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger delete-btn"
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
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove the course <strong id="delete-course-title" class="text-danger"></strong>?</p>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Warning:</strong> This will also unenroll all students and delete all course content. This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <form action="dashboard.php" method="POST" class="w-100">
                    <input type="hidden" id="delete-course-id" name="course_id_to_delete">
                    <div class="d-flex justify-content-between w-100">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" name="delete_course" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Yes, Remove Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 0.5rem;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    color: #6c757d;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin: 0 2px;
}

#coursesTable tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.15s ease-in-out;
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

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.classList.contains('show')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
});
</script>

<?php
$conn->close();
require 'includes/footer.php';
?>