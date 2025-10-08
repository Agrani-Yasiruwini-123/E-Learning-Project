<?php
$pageTitle = "Delete Account";
require 'includes/header.php';
require_once 'includes/functions.php';
require 'config/database.php';

require_login();

$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'] ?? '';
    if (empty($password)) {
        $error_message = 'Password is required to delete your account.';
    } else {

        $user_id = $_SESSION['user_id'];
        $sql = "SELECT password FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {

                $sql = "DELETE FROM users WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {

                    session_destroy();
                    header("Location: index.php");
                    exit();
                } else {
                    $error_message = 'Error deleting account: ' . $stmt->error;
                }
            } else {
                $error_message = 'Incorrect password. Account not deleted.';
            }
        } else {
            $error_message = 'User not found.';
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg border-danger">
                <div class="card-header bg-danger text-white">
                    <h2 class="h4 mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Delete Your Account</h2>
                </div>
                <div class="card-body p-4">
                    <p class="fw-bold">This is a permanent action and cannot be undone.</p>
                    <p class="text-muted">Deleting your account will remove all of your personal information, enrolled courses, and progress. Please be absolutely sure before you proceed.</p>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="profile-remove.php">
                        <div class="mb-3">
                            <label for="password" class="form-label">Please enter your password to confirm:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger btn-lg">I understand, delete my account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>