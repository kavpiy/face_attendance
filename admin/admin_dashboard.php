<?php
session_start();
if (!isset($_SESSION['lecture_id'])) {
    header("Location: admin_login.php");
    exit();
}
$lecture_name = $_SESSION['lecture_name'];
$lecture_id = $_SESSION['lecture_id'];
?>

<?php include 'header.php'; ?>

<style>
    .dashboard-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        height: 100%;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    
    .card-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    
    .welcome-card {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(78, 115, 223, 0.3);
    }
    
    .admin-avatar {
        width: 80px;
        height: 80px;
        background-color: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
    }
    
    .quick-stats {
        background-color: #f8f9fc;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>

<div class="container-fluid mt-4">
    <!-- Welcome Card -->
    <div class="card welcome-card mb-4 border-0">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-2">Welcome back, <?= htmlspecialchars($lecture_name) ?>!</h3>
                <p class="mb-0">Here's what's happening today.</p>
            </div>
            <div class="admin-avatar">
                <?= strtoupper(substr($lecture_name, 0, 1)) ?>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row quick-stats shadow-sm">
        <div class="col-md-4 text-center border-end">
            <h5 class="text-muted mb-1">Admin ID</h5>
            <h4 class="fw-bold"><?= htmlspecialchars($lecture_id) ?></h4>
        </div>
        <div class="col-md-4 text-center border-end">
            <h5 class="text-muted mb-1">Last Login</h5>
            <h4 class="fw-bold"><?= date('M j, Y') ?></h4>
        </div>
        <div class="col-md-4 text-center">
            <h5 class="text-muted mb-1">Current Time</h5>
            <h4 class="fw-bold" id="current-time"><?= date('h:i A') ?></h4>
        </div>
    </div>

    <!-- Main Dashboard Cards -->
    <div class="row mt-4">
        <!-- Attendance Management -->
        <div class="col-md-4 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-body text-center p-4">
                    <div class="card-icon text-primary">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h5 class="card-title">Attendance Management</h5>
                    <p class="card-text">Manage and track student attendance records.</p>
                    <div class="d-grid gap-2 mt-3">
                        <a href="add_session.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add Session
                        </a>
                        <a href="view_attendance.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i> View Attendance
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Course Management -->
<?php if ($lecture_id === 'L001'): ?>
        <div class="col-md-4 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-body text-center p-4">
                    <div class="card-icon text-success">
                        <i class="fas fa-book"></i>
                    </div>
                    <h5 class="card-title">Course Management</h5>
                    <p class="card-text">Handle course-related operations.</p>
                    <div class="d-grid gap-2 mt-3">
                        
                            <a href="add_course.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-1"></i> Add Course
                            </a>
                        
                        <a href="add_course.php" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-list me-1"></i> View Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>    
        <!-- User Management -->
        <div class="col-md-4 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-body text-center p-4">
                    <div class="card-icon text-info">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title">User Management</h5>
                    <p class="card-text">Manage user accounts and profiles.</p>
                    <div class="d-grid gap-2 mt-3">
                        <?php if ($lecture_id === 'L001'): ?>
                            <a href="add_admin.php" class="btn btn-info btn-sm text-white">
                                <i class="fas fa-user-plus me-1"></i> Add Admin
                            </a>
                        <?php endif; ?>
                        <a href="profile.php" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-user me-1"></i> My Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Actions -->
    <div class="row">
        <!-- Account Settings -->
        <div class="col-md-6 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-body p-4">
                    <h5 class="card-title"><i class="fas fa-cog text-warning me-2"></i>Account Settings</h5>
                    <div class="list-group list-group-flush">
                        <a href="profile.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-key me-2"></i> Change Password</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="profile.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-envelope me-2"></i> Update Email</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Actions -->
        <div class="col-md-6 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-body p-4">
                    <h5 class="card-title"><i class="fas fa-power-off text-danger me-2"></i>System Actions</h5>
                    <div class="list-group list-group-flush">
                        <a href="logout.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-sign-out-alt me-2"></i> Logout</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="profile.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-history me-2"></i> View System Logs</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update current time every second
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
        document.getElementById('current-time').textContent = timeString;
    }
    
    setInterval(updateTime, 1000);
    updateTime(); // Initial call
    
    // Add animation to cards on page load
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transitionDelay = `${index * 0.1}s`;
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    });
</script>

<?php include 'footer.php'; ?>