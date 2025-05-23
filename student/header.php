<?php
// Redirect to login if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}



$student_name = $_SESSION['student_name'];
$student_id = $_SESSION['student_id'];
$student_nic = $_SESSION['student_nic'];
$student_email = $_SESSION['student_email'];

require '../vendor/autoload.php';
use MongoDB\Client;

$client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/");
$db = $client->face_attendance;
$collection = $db->students;

$student = $collection->findOne(['Student_id' => $student_id]);
$profileImage = $student['profile_image'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance system</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .navbar {
      padding: 0.5rem 1rem;
      background-color: #212529 !important;
    }

    .dropdown-menu {
      min-width: 240px;
      border-radius: 0.5rem;
      padding: 0.5rem 0;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .dropdown-item {
      border-radius: 0.25rem;
      margin: 0 0.5rem;
      padding: 0.5rem 1rem;
      transition: all 0.2s;
    }

    .dropdown-item:hover {
      background-color: #f8f9fa;
      color: #212529;
    }

    .dropdown-header {
      padding: 0.5rem 1rem;
    }

    .dropdown-divider {
      margin: 0.25rem 0;
    }
        .profile-image {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid rgba(255, 255, 255, 0.5);
    }
        .profile-image-container {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: rgba(255, 255, 255, 0.1);
      overflow: hidden;
    }
  </style>
</head>
<body style="padding-top: 60px;">

<div class="d-flex" id="wrapper">

  <!-- Sidebar -->
  <div class="bg-dark text-white min-vh-100 pt-4" id="sidebar-wrapper">
    <div class="list-group list-group-flush px-2">
      <a href="student_dashboard.php" class="list-group-item list-group-item-action bg-dark text-white border-0 py-3">
        📊 Dashboard
      </a>
      <a href="session.php" class="list-group-item list-group-item-action bg-dark text-white border-0 py-3">
        🕒 Sessions
      </a>
      <a href="view_attendance.php" class="list-group-item list-group-item-action bg-dark text-white border-0 py-3">
        🗓️ View Attendance
      </a>
      <!-- Mobile-only profile/logout -->
      <a href="profile.php" class="list-group-item list-group-item-action bg-dark text-white border-0 py-3 d-block d-md-none">
        👤 Profile
      </a>
      <a href="logout.php" class="list-group-item list-group-item-action bg-dark text-white border-0 py-3 d-block d-md-none">
        🚪 Logout
      </a>
    </div>
  </div>

  <!-- Page Content -->
  <div id="page-content-wrapper" class="flex-grow-1">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
      <div class="container-fluid">
        <!-- Sidebar toggle and brand -->
        <div class="d-flex align-items-center">
          <button class="btn btn-sm btn-outline-light me-2" id="menu-toggle">
            <i class="fas fa-bars"></i>
          </button>
          <a class="navbar-brand d-flex align-items-center" href="student_dashboard.php">
            <i class="fas fa-graduation-cap me-2"></i>
            <span>Attendance System</span>
          </a>
        </div>

        <!-- Right side navigation -->
        <div class="d-flex align-items-center">
          <!-- User dropdown (Only for md and above) -->
          <div class="dropdown d-none d-md-block">
            <a href="#" class="nav-link dropdown-toggle text-white d-flex align-items-center" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="me-2 text-end d-none d-md-block">
                <div class="small">Logged in as</div>
                <div class="fw-bold"><?= htmlspecialchars($student_name) ?></div>
              </div>
              <div class="profile-image-container">
                <?php if ($profileImage && file_exists($profileImage)): ?>
                  <img src="<?= htmlspecialchars($profileImage) ?>"  class="profile-image" alt="Profile Image">
                <?php else: ?>
                   <i class="fas fa-user-circle fa-2x text-white"></i>
                <?php endif; ?>
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end mt-2 border-0 shadow" aria-labelledby="userDropdown">
              <li>
                <div class="dropdown-header px-3 py-2">
                  <div class="fw-bold"><?= htmlspecialchars($student_name) ?></div>
                  <div class="small text-muted">Student</div>
                </div>
              </li>
              <li><hr class="dropdown-divider my-1"></li>
              <li><a class="dropdown-item px-3 py-2" href="profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
              <li><hr class="dropdown-divider my-1"></li>
              <li><a class="dropdown-item px-3 py-2 text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>