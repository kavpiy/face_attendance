<?php
session_start();

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

$success = '';
$error = '';
$uploadSuccess = '';
$uploadError = '';

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['current_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if ($student && isset($student['Student_password'])) {
        if ($currentPassword === $student['Student_password']) {
            $collection->updateOne(
                ['Student_id' => $student_id],
                ['$set' => ['Student_password' => $newPassword]]
            );
            $success = "Password updated successfully!";
        } else {
            $error = "Current password is incorrect.";
        }
    } else {
        $error = "Student not found.";
    }
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $uploadDir = 'uploads/profile_pictures/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Delete old image if exists
    if ($profileImage && file_exists($profileImage)) {
        unlink($profileImage);
    }
    
    $fileExtension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
    $fileName = $student_id . '_' . time() . '.' . $fileExtension;
    $targetPath = $uploadDir . $fileName;
    
    // Validate image
    $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $check = getimagesize($_FILES['profile_picture']['tmp_name']);
    
    if ($check !== false && in_array($fileExtension, $validExtensions)) {
        if ($_FILES['profile_picture']['size'] <= 5000000) { // 5MB limit
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
                // Update MongoDB with the new image path
                $collection->updateOne(
                    ['Student_id' => $student_id],
                    ['$set' => ['profile_image' => $targetPath]]
                );
                $uploadSuccess = "Profile picture uploaded successfully!";
                $profileImage = $targetPath; // Update for current page load
            } else {
                $uploadError = "Sorry, there was an error uploading your file.";
            }
        } else {
            $uploadError = "File size too large. Maximum 5MB allowed.";
        }
    } else {
        $uploadError = "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
    }
}
?>

<?php include 'header.php' ?>

<div class="container-fluid mt-4">
  <div class="row justify-content-center">

    <!-- Profile Card -->
    <div class="col-md-12 mb-4">
      <div class="card shadow-sm border-0">
        <div class="card-body d-flex align-items-center gap-4">
          <div class="position-relative">
            <?php if ($profileImage && file_exists($profileImage)): ?>
              <img src="<?= htmlspecialchars($profileImage) ?>" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;" alt="Profile Image">
            <?php else: ?>
              <i class="fas fa-user-circle fa-5x text-primary"></i>
            <?php endif; ?>
            <span class="position-absolute bottom-0 end-0 bg-success p-1 rounded-circle">
              <i class="fas fa-check text-white small"></i>
            </span>
          </div>
          <div>
            <h3 class="mb-1 fw-bold"><?= htmlspecialchars($student_name) ?></h3>
            <p class="text-muted mb-2"><i class="fas fa-graduation-cap me-2"></i>Student Profile</p>
            <div class="d-flex gap-3">
              <span class="badge bg-primary rounded-pill">
                <i class="fas fa-id-card me-1"></i> ID: <?= htmlspecialchars($student_id) ?>
              </span>
              <span class="badge bg-info text-dark rounded-pill">
                <i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($student_email) ?>
              </span>
            </div>
          </div>
        </div>
        <div class="card-footer bg-light">
          <div class="d-flex justify-content-between">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="student_dashboard.php" class="text-decoration-none"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-user me-1"></i> Profile</li>
              </ol>
            </nav>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
              <i class="fas fa-camera me-1"></i> Change Photo
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- User Details -->
    <div class="col-md-6 mb-4">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white border-0">
          <h5 class="card-title mb-0 fw-bold"><i class="fas fa-id-card me-2 text-primary"></i> Personal Information</h5>
        </div>
        <div class="card-body">
          <div class="list-group list-group-flush">
            <div class="list-group-item border-0 px-0 py-3">
              <div class="d-flex align-items-center">
                <div class="icon-circle bg-light-primary me-3">
                  <i class="fas fa-user text-primary"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-muted small">Full Name</h6>
                  <p class="mb-0 fw-bold"><?= htmlspecialchars($student_name) ?></p>
                </div>
              </div>
            </div>
            
            <div class="list-group-item border-0 px-0 py-3">
              <div class="d-flex align-items-center">
                <div class="icon-circle bg-light-info me-3">
                  <i class="fas fa-id-badge text-info"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-muted small">Student ID</h6>
                  <p class="mb-0 fw-bold"><?= htmlspecialchars($student_id) ?></p>
                </div>
              </div>
            </div>
            
            <div class="list-group-item border-0 px-0 py-3">
              <div class="d-flex align-items-center">
                <div class="icon-circle bg-light-warning me-3">
                  <i class="fas fa-address-card text-warning"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-muted small">NIC Number</h6>
                  <p class="mb-0 fw-bold"><?= htmlspecialchars($student_nic) ?></p>
                </div>
              </div>
            </div>
            
            <div class="list-group-item border-0 px-0 py-3">
              <div class="d-flex align-items-center">
                <div class="icon-circle bg-light-success me-3">
                  <i class="fas fa-envelope text-success"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-muted small">Email Address</h6>
                  <p class="mb-0 fw-bold"><?= htmlspecialchars($student_email) ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Password Change -->
    <div class="col-md-6 mb-4">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white border-0">
          <h5 class="card-title mb-0 fw-bold"><i class="fas fa-lock me-2 text-danger"></i> Security Settings</h5>
        </div>
        <div class="card-body">
          <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center">
              <i class="fas fa-check-circle me-2"></i>
              <?= htmlspecialchars($success) ?>
            </div>
          <?php elseif ($error): ?>
            <div class="alert alert-danger d-flex align-items-center">
              <i class="fas fa-exclamation-circle me-2"></i>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="POST" class="needs-validation" novalidate>
            <div class="mb-4">
              <label for="current_password" class="form-label fw-bold"><i class="fas fa-key me-2"></i>Current Password</label>
              <div class="input-group">
                <input type="password" name="current_password" class="form-control" id="current_password" required>
                <span class="input-group-text toggle-password" data-target="current_password">
                  <i class="fas fa-eye"></i>
                </span>
              </div>
              <div class="invalid-feedback">
                Please enter your current password.
              </div>
            </div>
            
            <div class="mb-4">
              <label for="new_password" class="form-label fw-bold"><i class="fas fa-key me-2"></i>New Password</label>
              <div class="input-group">
                <input type="password" name="new_password" class="form-control" id="new_password" required>
                <span class="input-group-text toggle-password" data-target="new_password">
                  <i class="fas fa-eye"></i>
                </span>
              </div>
              <div class="invalid-feedback">
                Please enter a new password.
              </div>
              <small class="form-text text-muted">
                <i class="fas fa-info-circle me-1"></i> Minimum 8 characters with at least one number and one letter
              </small>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
              <i class="fas fa-save me-2"></i> Update Password
            </button>
          </form>
          
          <hr class="my-4">
          
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-1"><i class="fas fa-shield-alt me-2 text-success"></i> Account Security</h6>
              <p class="small text-muted mb-0">Last updated: <?= date('F j, Y') ?></p>
            </div>
            <a href="#" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-question-circle me-1"></i> Help
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Upload Profile Picture Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadModalLabel"><i class="fas fa-camera me-2"></i>Upload Profile Picture</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if ($uploadSuccess): ?>
          <div class="alert alert-success d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <?= htmlspecialchars($uploadSuccess) ?>
          </div>
        <?php elseif ($uploadError): ?>
          <div class="alert alert-danger d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= htmlspecialchars($uploadError) ?>
          </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="profile_picture" class="form-label">Select Image</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*" required>
            <small class="text-muted">Max size: 5MB | Formats: JPG, JPEG, PNG, GIF</small>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-upload me-2"></i>Upload Picture
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .toggle-password {
    cursor: pointer;
  }
  
  .toggle-password:hover {
    background-color: #f8f9fa;
  }
</style>

<script>
  // Toggle password visibility
  document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
      const target = document.getElementById(this.dataset.target);
      const icon = this.querySelector('i');
      
      if (target.type === 'password') {
        target.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        target.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
  });
  
  // Form validation
  (function() {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        
        form.classList.add('was-validated');
      }, false);
    });
  })();
  
  // Clear upload messages when modal is closed
  document.getElementById('uploadModal').addEventListener('hidden.bs.modal', function () {
    const alerts = this.querySelectorAll('.alert');
    alerts.forEach(alert => alert.remove());
  });
</script>

<?php include 'footer.php' ?>