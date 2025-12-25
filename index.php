<?php
/**
 * Landing Page
 * Online Notes Sharing System
 */

require_once 'includes/auth.php';

$pageTitle = 'Online Notes Sharing System';
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden">
    <div class="container">
        <div class="row align-items-center min-vh-100 py-5">
            <div class="col-lg-6 text-white">
                <h1 class="display-3 fw-bold mb-4">Online learning platform.</h1>
                <p class="lead mb-4">Share Your Notes Online.</p>
                <a href="signup.php" class="btn btn-warning btn-lg px-5 py-3 rounded-pill">
                    <i class="fas fa-rocket"></i> Join For Free
                </a>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-illustration">
                    <i class="fas fa-mobile-alt fa-10x text-white opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-book fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-3">60+ UX courses</h4>
                        <p class="text-muted">The automated process all your website tasks.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-chalkboard-teacher fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Expert instructors</h4>
                        <p class="text-muted">The automated process all your website tasks.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-clock fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Life time access</h4>
                        <p class="text-muted">The automated process all your website tasks.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

