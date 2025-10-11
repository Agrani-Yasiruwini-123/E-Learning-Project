<?php
    // Set page title and include header
    $pageTitle = "Login";
    require 'includes/header.php';

    // Initialize error message
    $error_message = '';

    // Handle POST request
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Collect form data
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';

        // Connect to database
        require 'config/database.php';

        // Prepare SQL query
        $sql = "SELECT user_id, role, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $row['password'])) {

                // Start session and store user info
                session_start();
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['role']    = $row['role'];

                // Redirect based on role
                if ($row['role'] === 'instructor') {
                    header("Location: instructor/dashboard.php");
                    exit();
                } else {
                    header("Location: index.php");
                    exit();
                }

            } else {
                $error_message = "Invalid credentials. Please try again.";
            }

        } else {
            $error_message = "Invalid credentials. Please try again.";
        }

        // Close connections
        $stmt->close();
        $conn->close();
    }
?>

<!-- =====================================
     Login Form Section
====================================== -->
<div class="auth-section">
    <div class="container">
        <div class="row align-items-center justify-content-center min-vh-100 py-5">
            <div class="col-lg-10">

                <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                    <div class="row g-0">

                        <!-- Left Column: Image (optional) -->
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="auth-image-container">
                                <!-- Optional image goes here -->
                            </div>
                        </div>

                        <!-- Right Column: Form -->
                        <div class="col-lg-6">
                            <div class="card-body p-4 p-sm-5">

                                <!-- Header -->
                                <div class="text-center mb-4">
                                    <h1 class="h3 fw-bold">Welcome Back!</h1>
                                    <p class="text-muted">Sign in to continue to EDUMA</p>
                                </div>

                                <!-- Error Message -->
                                <?php if (!empty($error_message)) : ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error_message; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Login Form -->
                                <form action="login.php" method="POST">

                                    <!-- Email Input -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input
                                            type="email"
                                            class="form-control"
                                            id="email"
                                            name="email"
                                            required
                                            placeholder="name@example.com"
                                        >
                                    </div>

                                    <!-- Password Input -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input
                                            type="password"
                                            class="form-control"
                                            id="password"
                                            name="password"
                                            required
                                            placeholder="••••••••"
                                        >
                                    </div>

                                    <!-- Options: Remember Me + Forgot Password -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="rememberMe"
                                            >
                                            <label class="form-check-label" for="rememberMe">
                                                Remember me
                                            </label>
                                        </div>
                                        <a href="forgot-password.php" class="small">
                                            Forgot Password?
                                        </a>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            Login
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div> <!-- /.col-lg-6 -->

                    </div> <!-- /.row -->
                </div> <!-- /.card -->

            </div> <!-- /.col-lg-10 -->
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</div> <!-- /.auth-section -->

<?php
    // Include footer
    require 'includes/footer.php';
?>
