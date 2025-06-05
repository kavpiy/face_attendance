<?php include 'header.php'; ?>

<div class="container-fluid">
    <!-- Welcome Banner -->
    <div class="card shadow-sm p-4 my-4 text-center bg-light">
        <h3 class="text-primary">Online Attendance Management System</h3>
        <h5 class="text-muted">Faculty of Technology, University of Colombo</h5>
        <p class="mt-3">Track your attendance, view session records, and stay up to date with your academic performance — all in one place.</p>
        <div class="mt-4">
            <a href="login.php" class="btn btn-primary me-3">
                <i class="fas fa-sign-in-alt me-1"></i> Login
            </a>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row text-center my-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <i class="fas fa-user-check fa-3x text-success mb-3"></i>
                <h5>Real-time Attendance</h5>
                <p>Instantly mark and view your attendance records with facial recognition.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
                <h5>Session History</h5>
                <p>View upcoming classes and your complete attendance history organized by course.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                <h5>Performance Insights</h5>
                <p>Track your attendance percentage and stay informed about your academic progress.</p>
            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="text-center text-muted my-4">
        <img src="https://cmb.ac.lk/wp-content/uploads/logo-color.png" alt="" width="30"><br>
        <small>&copy; <?= date("Y") ?> Faculty of Technology, University of Colombo</small>
    </div>
</div>

<?php include 'footer.php'; ?>
