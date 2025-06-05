<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}
$student_name = $_SESSION['student_name'];
$student_id = $_SESSION['student_id'];
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
    
    .student-avatar {
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
    
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-icon i {
        margin-right: 6px;
    }
</style>

<div class="container-fluid mt-2">
    <!-- Welcome Card -->
    <div class="card welcome-card mb-4 border-0">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-2">Welcome, <?= htmlspecialchars($student_name) ?>!</h3>
                <p class="mb-0">Student Dashboard Overview</p>
            </div>
            <div class="student-avatar">
                <?= strtoupper(substr($student_name, 0, 1)) ?>
            </div>
        </div>
    </div>


    <div class="row quick-stats shadow-sm">
        <div class="col-md-4 text-center border-end">
            <h5 class="text-muted mb-1">Student ID</h5>
            <h4 class="fw-bold"><?= htmlspecialchars($student_id) ?></h4>
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
        <div class="col-md-4 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-body text-center p-4">
                    <div class="card-icon text-primary">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h5 class="card-title">My Attendance</h5>
                    <p class="card-text">View your attendance records and history.</p>
                    <div class="d-grid gap-2 mt-3">
                        <a href="view_attendance.php" class="btn btn-primary btn-sm btn-icon">
                            <i class="fas fa-eye"></i> View Attendance
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Course Information -->
        <div class="col-md-4 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-body text-center p-4">
                    <div class="card-icon text-success">
                        <i class="fas fa-user-graduate"></i>  
                    </div>
                    <h5 class="card-title">My Profile</h5> 
                    <p class="card-text">View and manage your student profile.</p>  
                    <div class="d-grid gap-2 mt-3">
                        <a href="profile.php" class="btn btn-success btn-sm btn-icon text-white"> 
                            <i class="fas fa-eye"></i> View Profile 
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Session Schedule -->
        <div class="col-md-4 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-body text-center p-4">
                    <div class="card-icon text-info">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h5 class="card-title">Class Schedule</h5>
                    <p class="card-text">View upcoming class sessions and timetable.</p>
                    <div class="d-grid gap-2 mt-3">
                        <a href="session.php" class="btn btn-info btn-sm btn-icon text-white">
                            <i class="fas fa-calendar"></i> View Schedule
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Management Section -->
    <div class="row">
        <!-- Profile Settings -->
        <div class="col-md-6 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-body p-4">
                    <h5 class="card-title"><i class="fas fa-user-cog text-warning me-2"></i>Profile Settings</h5>
                    <div class="list-group list-group-flush">
                        <a href="profile.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user me-2"></i> View Profile</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="profile.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-key me-2"></i> Change Password</span>
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
                    <h5 class="card-title"><i class="fas fa-power-off text-danger me-2"></i>Account Actions</h5>
                    <div class="list-group list-group-flush">
                        <a href="logout.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-sign-out-alt me-2"></i> Logout</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-question-circle me-2"></i> Help & Support</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
    updateTime(); 
    
 
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