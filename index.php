<?php
$pageTitle = "Home";
require 'includes/header.php';
?>

<!-- Section 1: Hero -->
<section class="hero-section text-white text-center d-flex align-items-center" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/images/hero-bg.jpg') no-repeat center center/cover; height: 100vh;">
    <div class="container">
        <h1 class="display-3 fw-bold mb-3">Unlock Your Potential</h1>
        <p class="lead mb-4">Join thousands of learners and gain new skills with expert-led courses.</p>
        <a href="courses.php" class="btn btn-primary btn-lg me-3">Explore Courses</a>
        <a href="#features" class="btn btn-outline-light btn-lg">Learn More</a>
    </div>
</section>

<!-- Section 2: Features/Benefits -->
<section id="features" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose EDUMA?</h2>
            <p class="lead text-muted">We provide the best learning experience tailored for you.</p>
        </div>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;">
                    <i class="fas fa-video fa-2x"></i>
                </div>
                <h3 class="h5">Expert-Led Courses</h3>
                <p class="text-muted">Learn from industry professionals with real-world experience.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;">
                    <i class="fas fa-infinity fa-2x"></i>
                </div>
                <h3 class="h5">Lifetime Access</h3>
                <p class="text-muted">Enroll once and get unlimited lifetime access to course materials.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;">
                    <i class="fas fa-mobile-alt fa-2x"></i>
                </div>
                <h3 class="h5">Learn Anywhere</h3>
                <p class="text-muted">Access your courses on any device, anytime, anywhere.</p>
            </div>
        </div>

        <!-- New Features Row -->
        <div class="row text-center mt-4">
            <div class="col-md-4 mb-4">
                <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;">
                    <i class="fas fa-certificate fa-2x"></i>
                </div>
                <h3 class="h5">Certified Courses</h3>
                <p class="text-muted">Earn industry-recognized certificates to boost your career.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;">
                    <i class="fas fa-headset fa-2x"></i>
                </div>
                <h3 class="h5">24/7 Support</h3>
                <p class="text-muted">Get help whenever you need it with our dedicated support team.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h3 class="h5">Thriving Community</h3>
                <p class="text-muted">Join a vibrant community of learners and mentors.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Featured Courses -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Explore Our Featured Courses</h2>
            <p class="lead text-muted">Handpicked courses to kickstart your learning journey.</p>
        </div>
        <!-- Placeholder for featured courses -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="assets/images/course1.jpg" class="card-img-top" alt="Course 1">
                    <div class="card-body">
                        <h5 class="card-title">Web Development Bootcamp</h5>
                        <p class="card-text">Master HTML, CSS, JavaScript, and backend development in this complete course.</p>
                        <a href="course-details.php?id=1" class="btn btn-outline-primary btn-sm">View Course</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="assets/images/course2.jpg" class="card-img-top" alt="Course 2">
                    <div class="card-body">
                        <h5 class="card-title">Digital Marketing Mastery</h5>
                        <p class="card-text">Learn SEO, content marketing, PPC, and social media strategies from top experts.</p>
                        <a href="course-details.php?id=2" class="btn btn-outline-primary btn-sm">View Course</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="assets/images/course3.jpg" class="card-img-top" alt="Course 3">
                    <div class="card-body">
                        <h5 class="card-title">Data Science & Python</h5>
                        <p class="card-text">Dive into data analysis, machine learning, and visualization with Python.</p>
                        <a href="course-details.php?id=3" class="btn btn-outline-primary btn-sm">View Course</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Testimonials -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">What Our Learners Say</h2>
            <p class="lead text-muted">Hear from people who transformed their careers with EDUMA.</p>
        </div>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <blockquote class="blockquote">
                    <p class="mb-3">"The courses are top-notch and easy to follow. I landed my dream job thanks to EDUMA!"</p>
                    <footer class="blockquote-footer">Sarah T., <cite title="Source Title">Marketing Specialist</cite></footer>
                </blockquote>
            </div>
            <div class="col-md-4 mb-4">
                <blockquote class="blockquote">
                    <p class="mb-3">"Excellent platform with real-world projects and expert mentors."</p>
                    <footer class="blockquote-footer">Raj P., <cite title="Source Title">Full Stack Developer</cite></footer>
                </blockquote>
            </div>
            <div class="col-md-4 mb-4">
                <blockquote class="blockquote">
                    <p class="mb-3">"I loved the flexibility and quality of content. Highly recommended!"</p>
                    <footer class="blockquote-footer">Emily R., <cite title="Source Title">Freelancer</cite></footer>
                </blockquote>
            </div>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
