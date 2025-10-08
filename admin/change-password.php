<?php
$pageTitle = "Change Password";
require_once 'includes/auth-check.php';
require_once 'includes/header.php';
require_once '../config/database.php';


$feedback_message = '';
$feedback_class = '';
$admin_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $current_password = $_POST['current_password'] ?? '';
  $new_password = $_POST['new_password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';


  if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $feedback_message = "All fields are required.";
    $feedback_class = 'alert-danger';
  } elseif ($new_password !== $confirm_password) {
    $feedback_message = "New passwords do not match. Please try again.";
    $feedback_class = 'alert-danger';
  } else {

    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ? AND role = 'admin'");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $admin = $result->fetch_assoc();

      if (password_verify($current_password, $admin['password'])) {

        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $new_hashed_password, $admin_id);

        if ($update_stmt->execute()) {
          $feedback_message = "Your password has been updated successfully.";
          $feedback_class = 'alert-success';
        } else {
          $feedback_message = "Error updating password. Please try again.";
          $feedback_class = 'alert-danger';
        }
        $update_stmt->close();
      } else {
        $feedback_message = "The current password you entered is incorrect.";
        $feedback_class = 'alert-danger';
      }
    } else {
      $feedback_message = "Could not find your administrator account.";
      $feedback_class = 'alert-danger';
    }
    $stmt->close();
  }
}

$conn->close();
?>

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