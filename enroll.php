<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0 text-center">
        <div class="card-body p-4 p-sm-5">
          <i class="fas <?php echo $icon_class; ?> fa-5x mb-4"></i>

          <h1 class="h3 fw-bold mb-3"><?php echo $message_heading; ?></h1>

          <p class="text-muted"><strong>Course:</strong> <?php echo htmlspecialchars($course_title); ?></p>

          <div class="alert <?php echo $message_class; ?> mt-4">
            <?php echo $message; ?>
          </div>

          <hr class="my-4">

          <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <?php if ($is_new_enrollment) : ?>
              <a href="course-page.php?id=<?php echo $course_id; ?>" class="btn btn-primary btn-lg px-4 gap-3">Start Learning</a>
            <?php else :
            ?>
              <a href="course-page.php?id=<?php echo $course_id; ?>" class="btn btn-primary btn-lg px-4 gap-3">Continue Learning</a>
            <?php endif; ?>

            <a href="profile.php" class="btn btn-outline-secondary btn-lg px-4">View My Courses</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php
require 'includes/footer.php';
?>