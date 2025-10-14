<?php
$pageTitle = "Instructor Dashboard";
require 'includes/header.php';
require '../config/database.php';

// Check if user is instructor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'instructor') {
    header('Location: login.php');
    exit;
}

$feedback_message = '';
$feedback_class = '';
$instructor_id = $_SESSION['user_id'];

// Handle course deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_course'])) {
    $course_id_to_delete = $_POST['course_id_to_delete'] ?? 0;
    
    // Verify course belongs to instructor
    $check_stmt = $conn->prepare("SELECT course_title FROM courses WHERE course_id = ? AND instructor_id = ?");
    $check_stmt->bind_param("ii", $course_id_to_delete, $instructor_id);
    $check_stmt->execute();
    $course = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();
    
    if ($course) {
        $conn->begin_transaction();
        try {
            // Delete enrollments first
            $stmt_enroll = $conn->prepare("DELETE FROM enrollments WHERE course_id = ?");
            $stmt_enroll->bind_param("i", $course_id_to_delete);
            $stmt_enroll->execute();
            $stmt_enroll->close();
            
            // Delete course content
            $stmt_content = $conn->prepare("DELETE FROM course_content WHERE course_id = ?");
            $stmt_content->bind_param("i", $course_id_to_delete);
            $stmt_content->execute();
            $stmt_content->close();
            
            // Delete the course
            $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
            $stmt->bind_param("i", $course_id_to_delete);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $conn->commit();
                $feedback_message = "✅ Course '{$course['course_title']}' has been successfully removed!";
                $feedback_class = 'alert-success';
            } else {
                $conn->rollback();
                $feedback_message = "❌ Could not remove course. Please try again.";
                $feedback_class = 'alert-danger';
            }
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            $feedback_message = "❌ Error removing course: " . $e->getMessage();
            $feedback_class = 'alert-danger';
        }
    } else {
        $feedback_message = "❌ Course not found or you don't have permission to delete it.";
        $feedback_class = 'alert-warning';
    }
}

// Get instructor's courses with additional data
$stmt_courses = $conn->prepare("
    SELECT c.*, 
           COUNT(e.enrollment_id) as enrollment_count,
           AVG(r.rating) as avg_rating,
           COUNT(r.rating_id) as rating_count
    FROM courses c 
    LEFT JOIN enrollments e ON c.course_id = e.course_id 
    LEFT JOIN ratings r ON c.course_id = r.course_id 
    WHERE c.instructor_id = ? 
    GROUP BY c.course_id 
    ORDER BY c.created_at DESC
");
$stmt_courses->bind_param("i", $instructor_id);
$stmt_courses->execute();
$courses = $stmt_courses->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_courses->close();

// Get dashboard statistics
$total_enrollments = 0;
$total_revenue = 0;
$average_rating = 0;

if (!empty($courses)) {
    $total_enrollments = array_sum(array_column($courses, 'enrollment_count'));
    
    // Calculate total revenue (assuming $50 per enrollment for demo)
    $total_revenue = $total_enrollments * 50;
    
    // Calculate average rating
    $rated_courses = array_filter($courses, function($course) {
        return !is_null($course['avg_rating']);
    });
    if (!empty($rated_courses)) {
        $average_rating = array_sum(array_column($rated_courses, 'avg_rating')) / count($rated_courses);
    }
}

// Get recent enrollments for the activity feed
$stmt_recent = $conn->prepare("
    SELECT e.enrollment_date, c.course_title, u.username 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.course_id 
    JOIN users u ON e.user_id = u.user_id 
    WHERE c.instructor_id = ? 
    ORDER BY e.enrollment_date DESC 
    LIMIT 5
");
$stmt_recent->bind_param("i", $instructor_id);
$stmt_recent->execute();
$recent_enrollments = $stmt_recent->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_recent->close();
?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt text-primary me-2"></i>Instructor Dashboard
        </h1>
        <a href="course-add.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create New Course
        </a>
    </div>

    <?php if ($feedback_message) : ?>
        <div class="alert <?php echo $feedback_class; ?> alert-dismissible fade show" role="alert">
            <?php echo $feedback_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Stats Cards Row -->
    <div class="row">
        <!-- Total Courses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Courses</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo count($courses); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Enrollments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Enrollments</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $total_enrollments; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Rating Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Average Rating</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?php if ($average_rating > 0): ?>
                                    <?php echo number_format($average_rating, 1); ?>/5.0
                                    <small class="text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?php echo $i <= round($average_rating) ? '' : '-o'; ?>"></i>
                                        <?php endfor; ?>
                                    </small>
                                <?php else: ?>
                                    No ratings yet
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Estimated Revenue</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">$<?php echo number_format($total_revenue); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Courses List -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-book me-2"></i>My Courses
                    </h6>
                    <span class="badge bg-primary rounded-pill"><?php echo count($courses); ?> courses</span>
                </div>
                <div class="card-body">
                    <?php if (empty($courses)) : ?>
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No courses created yet</h5>
                            <p class="text-muted mb-4">Start by creating your first course to share your knowledge.</p>
                            <a href="course-add.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create Your First Course
                            </a>
                        </div>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="coursesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course</th>
                                        <th>Category</th>
                                        <th class="text-center">Enrollments</th>
                                        <th class="text-center">Rating</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course) : ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($course['course_thumbnail'])): ?>
                                                        <img src="../<?php echo htmlspecialchars($course['course_thumbnail']); ?>" 
                                                             alt="Course thumbnail" class="rounded me-3" 
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                                                             style="width: 40px; height: 40px;">
                                                            <i class="fas fa-book text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($course['course_title']); ?></h6>
                                                        <small class="text-muted">Created: <?php echo date('M j, Y', strtotime($course['created_at'])); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($course['category']); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold text-primary"><?php echo $course['enrollment_count']; ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($course['avg_rating']): ?>
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <span class="fw-bold text-warning me-1"><?php echo number_format($course['avg_rating'], 1); ?></span>
                                                        <i class="fas fa-star text-warning"></i>
                                                        <small class="text-muted ms-1">(<?php echo $course['rating_count']; ?>)</small>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">No ratings</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php 
                                                $status = $course['status'] ?? 'active';
                                                $status_class = $status === 'active' ? 'bg-success' : 'bg-secondary';
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($status); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="../course-details.php?id=<?php echo $course['course_id']; ?>" 
                                                       class="btn btn-outline-primary" target="_blank" title="View Course">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="course-edit.php?id=<?php echo $course['course_id']; ?>" 
                                                       class="btn btn-outline-warning" title="Edit Course">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="course-analytics.php?id=<?php echo $course['course_id']; ?>" 
                                                       class="btn btn-outline-info" title="View Analytics">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger delete-btn"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
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

        <!-- Sidebar with Recent Activity -->
        <div class="col-lg-4 mb-4">
            <!-- Recent Enrollments -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-history me-2"></i>Recent Enrollments
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_enrollments)) : ?>
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p>No recent enrollments</p>
                        </div>
                    <?php else : ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_enrollments as $enrollment) : ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                    <div>
                                        <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($enrollment['username']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($enrollment['course_title']); ?></small>
                                    </div>
                                    <small class="text-muted"><?php echo date('M j', strtotime($enrollment['enrollment_date'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="course-add.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create New Course
                        </a>
                        <a href="profile.php" class="btn btn-outline-primary">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </a>
                        <a href="settings.php" class="btn btn-outline-secondary">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </div>
                </div>
            </div>
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
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone!
                </div>
                <p>Are you sure you want to delete the course <strong id="delete-course-title" class="text-danger"></strong>?</p>
                <p class="text-muted small mb-0">
                    This will permanently delete the course and all associated content, including:
                    <br>• All course lessons and materials
                    <br>• Student enrollments and progress
                    <br>• Ratings and reviews
                </p>
            </div>
            <div class="modal-footer">
                <form action="dashboard.php" method="POST" class="w-100">
                    <input type="hidden" id="delete-course-id" name="course_id_to_delete">
                    <div class="d-flex gap-2 w-100">
                        <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" name="delete_course" class="btn btn-danger flex-fill">
                            <i class="fas fa-trash me-2"></i>Yes, Delete Course
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
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }

.btn-group .btn {
    border-radius: 0.375rem;
    margin: 0 2px;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    color: #6c757d;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete modal handling
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.dataset.id;
        const title = button.dataset.title;
        deleteModal.querySelector('#delete-course-id').value = id;
        deleteModal.querySelector('#delete-course-title').textContent = title;
    });

    // Add search functionality to courses table
    const coursesTable = document.getElementById('coursesTable');
    if (coursesTable) {
        // You can add search functionality here if needed
        console.log('Courses table loaded with', <?php echo count($courses); ?> + ' courses');
    }

    // Add loading states to buttons
    const deleteButtons = document.querySelectorAll('button[type="submit"][name="delete_course"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
            this.disabled = true;
        });
    });
});
</script>

<?php
$conn->close();
require 'includes/footer.php';
?>