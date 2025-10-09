<?php
$pageTitle = "Home";
require 'includes/header.php';
?>

<!-- Section 1: Hero -->
<section class="hero-section text-white text-center d-flex align-items-center" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/images/hero-bg.jpg') center/cover no-repeat; min-height: 90vh;">
    <div class="container">
        <h1 class="display-3 fw-bold mb-3">Discover Your Future</h1>
        <p class="lead mb-4">Empower your career with in-demand skills taught by top instructors.</p>
        <a href="courses.php" class="btn btn-teal btn-lg me-2">Browse Courses</a>
        <a href="#features" class="btn btn-outline-light btn-lg">How It Works</a>
    </div>
</section>

<!-- Section 2: Features/Benefits -->
<section id="features" class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Learn with EDUMA?</h2>
            <p class="text-muted">We blend flexibility with quality to bring you the best online learning experience.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow h-100 text-center p-4">
                    <div class="mb-3 text-teal fs-2">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h5>Expert-Led Content</h5>
                    <p class="text-muted">Courses crafted and delivered by professionals at the top of their fields.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow h-100 text-center p-4">
                    <div class="mb-3 text-teal fs-2">
                        <i class="fas fa-unlock-alt"></i>
                    </div>
                    <h5>Unlimited Access</h5>
                    <p class="text-muted">Get lifetime access to all your enrolled courses and updates.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow h-100 text-center p-4">
                    <div class="mb-3 text-teal fs-2">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h5>Anytime, Anywhere</h5>
                    <p class="text-muted">Study at your own pace on any device — mobile, tablet, or desktop.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Featured Courses -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Featured Courses</h2>
            <p class="text-muted">Our top picks to help you get started.</p>
        </div>
        <div class="text-center">
            <a href="courses.php" class="btn btn-teal btn-lg">Start Learning Now</a>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
