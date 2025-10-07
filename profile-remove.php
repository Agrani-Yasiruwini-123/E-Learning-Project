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