<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?> | EDUMA</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<!-- body with login form -->
<body>
  <main>
    <div class="auth-section">
      <div class="container">
        <div class="row align-items-center justify-content-center min-vh-100 py-5">
          <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
              <div class="row g-0">
                <!-- Left Column: Image -->
                <div class="col-lg-6 d-none d-lg-block">
                  <div class="auth-image-container"></div>
                </div>

                <!-- Right Column: Login Form -->
                <div class="col-lg-6">
                  <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                      <!-- *** FIX: Use correct relative path to the homepage *** -->
                      <a href="../index.php" class="navbar-brand logo d-inline-block mb-3">EDUMA</a>
                      <h1 class="h3 fw-bold">Admin Panel Login</h1>
                      <p class="text-muted">Sign in to continue</p>
                    </div>

                    <?php if (!empty($error_message)): ?>
                      <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                      </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                      <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="admin@example.com">
                      </div>
                      <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
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
  </main>
  <script src="https:
</body>

</html>