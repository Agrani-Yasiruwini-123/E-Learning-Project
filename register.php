<div class="auth-section">
    <div class="container">
        <div class="row align-items-center justify-content-center min-vh-100 py-5">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                    <div class="row g-0">


                        <!-- Right Column: Registration Form -->
                        <div class="col-lg-6">
                            <div class="card-body p-4 p-sm-5">
                                <div class="text-center mb-4">
                                    <h1 class="h3 fw-bold">Create Your Account</h1>
                                    <p class="text-muted">Join EDUMA and start your learning journey!</p>
                                </div>

                                <!-- Display Error Message if any -->
                                <?php if (!empty($error_message)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error_message; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Registration Form -->
                                <form action="register.php" method="POST">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" required placeholder="Choose a username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required placeholder="name@example.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required placeholder="Create a strong password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="password_confirm" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required placeholder="Confirm your password">
                                    </div>
                                    <div class="mb-3" style="display: none;">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="student" selected>Student</option>

                                        </select>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="" id="agreeTerms" required>
                                        <label class="form-check-label small" for="agreeTerms">
                                            I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
                                        </label>
                                    </div>

                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-lg">Sign Up</button>
                                    </div>
                                </form>

                                <div class="text-center">
                                    <p class="small">Already have an account? <a href="login.php">Sign In</a></p>
                                </div>
                            </div>
                        </div>

                        <!-- Left Column: Image (Same as login page for consistency) -->
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="auth-image-container"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>