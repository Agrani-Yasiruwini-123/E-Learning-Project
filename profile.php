<div class="profile-section bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Display Flash Message if it exists -->
                <?php if ($flash_message): ?>
                    <div class="alert <?php echo htmlspecialchars($flash_class); ?> alert-dismissible fade show shadow-sm" role="alert">
                        <?php echo htmlspecialchars($flash_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Redesigned Profile Header (No Image) -->
                <div class="profile-header card card-body p-4 shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-user-circle fa-3x text-primary"></i>
                        </div>
                        <div>
                            <h1 class="h3 fw-bold mb-0">Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h1>
                            <p class="text-muted mb-0 small">Joined on <?php echo date("F j, Y", strtotime($user['registration_date'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="row">
                    <!-- Simplified Sidebar Navigation -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0">
                            <div class="list-group list-group-flush profile-nav">
                                <a href="profile.php" class="list-group-item list-group-item-action active" aria-current="true">
                                    <i class="fas fa-book me-2"></i> My Enrolled Courses
                                </a>
                                <a href="profile-remove.php" class="list-group-item list-group-item-action text-danger">
                                    <i class="fas fa-trash-alt me-2"></i> Delete Account
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Area -->
                    <div class="col-lg-8 mt-4 mt-lg-0">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white p-3">
                                <h5 class="mb-0 fw-bold">My Learning</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($enrolled_courses)): ?>
                                    <div class="text-center p-4">
                                        <i class="fas fa-search-plus fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">You are not enrolled in any courses yet.</p>
                                        <a href="courses.php" class="btn btn-primary">Explore Courses</a>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($enrolled_courses as $course): ?>
                                            <div class="list-group-item p-3">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3 mb-3 mb-md-0">
                                                        <img src="<?php echo htmlspecialchars($course['course_thumbnail']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($course['course_title']); ?>">
                                                    </div>
                                                    <div class="col-md-9">
                                                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($course['course_title']); ?></h6>
                                                        <p class="text-muted small mb-2">
                                                            By <?php echo htmlspecialchars($course['instructor_name']); ?> in <strong><?php echo htmlspecialchars($course['category']); ?></strong>
                                                        </p>
                                                        <div class="progress mb-2" style="height: 8px;">
                                                            <div class="progress-bar" role="progressbar" style="width: <?php echo intval($course['progress']); ?>%;" aria-valuenow="<?php echo intval($course['progress']); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <!-- Feature: Smart Button -->
                                                            <?php if (intval($course['progress']) >= 100): ?>
                                                                <a href="course-page.php?id=<?php echo $course['course_id']; ?>" class="btn btn-success btn-sm">
                                                                    <i class="fas fa-check-circle me-1"></i> Review Course
                                                                </a>
                                                            <?php else: ?>
                                                                <a href="course-page.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary btn-sm">Continue Learning</a>
                                                            <?php endif; ?>
                                                            <span class="small fw-bold text-muted"><?php echo intval($course['progress']); ?>% Complete</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
$stmt_user->close();
$stmt_courses->close();
$conn->close();
require 'includes/footer.php';
?>