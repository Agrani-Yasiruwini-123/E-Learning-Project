<?php
$pageTitle = "Home";
require 'includes/header.php';
?>

<!-- Section 1: Hero -->
<section class="hero-section text-white text-center d-flex align-items-center" style="background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('assets/images/hero-bg.jpg') center/cover no-repeat; min-height: 90vh;">
    <div class="container">
        <h1 class="display-3 fw-bold mb-4" style="text-shadow: 0 3px 10px rgba(0,0,0,0.7);">Discover Your Future</h1>
        <p class="lead mb-5" style="text-shadow: 0 2px 6px rgba(0,0,0,0.6); max-width: 600px; margin: 0 auto;">Empower your career with in-demand skills taught by top instructors.</p>
        <a href="courses.php" class="btn btn-teal btn-lg me-3 shadow-sm">Browse Courses</a>
        <a href="#features" class="btn btn-outline-light btn-lg shadow-sm">How It Works</a>
    </div>
</section>

<!-- Section 2: Features/Benefits -->
<section id="features" class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Learn with EDUMA?</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">We blend flexibility with quality to bring you the best online learning experience.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3 text-teal fs-1">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h5 class="mb-3">Expert-Led Content</h5>
                    <p class="text-muted">Courses crafted and delivered by professionals at the top of their fields.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3 text-teal fs-1">
                        <i class="fas fa-unlock-alt"></i>
                    </div>
                    <h5 class="mb-3">Unlimited Access</h5>
                    <p class="text-muted">Get lifetime access to all your enrolled courses and updates.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3 text-teal fs-1">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h5 class="mb-3">Anytime, Anywhere</h5>
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
            <p class="text-muted mx-auto" style="max-width: 600px;">Our top picks to help you get started.</p>
        </div>
        <div class="text-center">
            <a href="courses.php" class="btn btn-teal btn-lg shadow-sm">Start Learning Now</a>
        </div>
    </div>
</section>

<style>
    /* Custom teal button */
    .btn-teal {
        background-color: #20c997;
        border-color: #20c997;
        color: #fff;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .btn-teal:hover,
    .btn-teal:focus {
        background-color: #1aa179;
        border-color: #1aa179;
        color: #fff;
        text-decoration: none;
    }

    /* Text teal utility */
    .text-teal {
        color: #20c997 !important;
    }

    /* Larger icons for features */
    .fs-1 {
        font-size: 3rem !important;
    }

    /* Add subtle shadow to cards */
    .card.shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    /* Responsive text centering and width */
    @media (min-width: 768px) {
        .lead {
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
    }
</style>

<?php require 'includes/footer.php'; ?>
