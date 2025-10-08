<div class="auth-section">
    <div class="container">
        <div class="row align-items-center justify-content-center min-vh-100 py-5">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                    <div class="row g-0">
                        <!-- Left Column: Image -->
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="auth-image-container">
                                <!-- You can replace this with your own image -->
                            </div>
                        </div>

                        <!-- Right Column: Login Form -->
                        <div class="col-lg-6">
                            <div class="card-body p-4 p-sm-5">
                                <div class="text-center mb-4">
                                    <h1 class="h3 fw-bold">Welcome Back!</h1>
                                    <p class="text-muted">Sign in to continue to EDUMA</p>
                                </div>

                                <!-- Display Error Message if any -->
                                <?php if (!empty($error_message)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error_message; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Login Form -->
                                <form action="login.php" method="POST">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required placeholder="name@example.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">
                                                Remember me
                                            </label>
                                        </div>
                                        <a href="forgot-password.php" class="small">Forgot Password?</a>
                                    </div>
                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>