<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Change Administrator Password</h1>

  <div class="row">
    <div class="col-lg-6">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Update Your Password</h6>
        </div>
        <div class="card-body">

          <?php if ($feedback_message) : ?>
            <div class="alert <?php echo $feedback_class; ?>" role="alert">
              <?php echo $feedback_message; ?>
            </div>
          <?php endif; ?>

          <form action="change-password.php" method="POST">
            <div class="mb-3">
              <label for="current_password" class="form-label">Current Password</label>
              <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <hr>
            <div class="mb-3">
              <label for="new_password" class="form-label">New Password</label>
              <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="mb-3">
              <label for="confirm_password" class="form-label">Confirm New Password</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require_once 'includes/footer.php';
?>