<?php
session_start();
require '../vendor/autoload.php';

use MongoDB\Client;

// Check if admin is logged in and has permission
if (!isset($_SESSION['lecture_id']) || $_SESSION['lecture_id'] !== 'L001') {
    header("Location: admin_login.php");
    exit();
}

$success = "";
$error = "";


$client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");
$collection = $client->face_attendance->lectures;


function getNextLectureId($collection) {
    $lastLecture = $collection->find([], [
        'sort' => ['Lecture_id' => -1],
        'limit' => 1
    ])->toArray();

    if (count($lastLecture) === 0) {
        return "L001";
    }

    $lastId = $lastLecture[0]['Lecture_id'];
    $number = intval(substr($lastId, 1)) + 1;
    return "L" . str_pad($number, 3, '0', STR_PAD_LEFT);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lecture_name = trim($_POST['lecture_name']);
    $lecture_email = trim($_POST['lecture_email']);
    $lecture_password = $_POST['lecture_password'];
    $lecture_id = getNextLectureId($collection);

    try {
        // Validate inputs
        if (empty($lecture_name)) {
            throw new Exception("Name cannot be empty");
        }
        
        if (!filter_var($lecture_email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        if (strlen($lecture_password) < 8) {
            throw new Exception("Password must be at least 8 characters");
        }

        $existing = $collection->findOne(['Lecture_email' => $lecture_email]);
        if ($existing) {
            $error = "Admin with this email already exists!";
        } else {
            $collection->insertOne([
                'Lecture_id' => $lecture_id,
                'Lecture_name' => $lecture_name,
                'Lecture_email' => $lecture_email,
                'Lecture_password' => $lecture_password,
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);
            $success = "Admin added successfully with ID: $lecture_id";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}


$lectures = $collection->find([], ['sort' => ['created_at' => -1]]);
?>

<?php include 'header.php'; ?>

<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-lg-6">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New Admin</h4>
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

          <form method="post" id="adminForm">
            <div class="mb-3">
              <label for="lecture_name" class="form-label fw-bold">Full Name</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" id="lecture_name" name="lecture_name" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="lecture_email" class="form-label fw-bold">Email Address</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" id="lecture_email" name="lecture_email" required>
              </div>
            </div>

            <div class="mb-4">
              <label for="lecture_password" class="form-label fw-bold">Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="lecture_password" name="lecture_password" 
                       value="Qwert12345@" required>
                <button class="btn btn-outline-secondary toggle-password" type="button">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
              <div class="form-text">
                <i class="fas fa-info-circle me-1"></i> Set this as default password
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
              <i class="fas fa-save me-2"></i> Add Admin
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h4 class="mb-0"><i class="fas fa-users me-2"></i>Admin List</h4>
          <span class="badge bg-light text-dark">
            Total: <?= $collection->countDocuments() ?>
          </span>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped" id="adminTable">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($lectures as $lecture): ?>
                  <tr>
                    <td class="fw-bold"><?= htmlspecialchars($lecture['Lecture_id']) ?></td>
                    <td><?= htmlspecialchars($lecture['Lecture_name']) ?></td>
                    <td><?= htmlspecialchars($lecture['Lecture_email']) ?></td>
                    <td>

                        <span class="badge bg-secondary">Protected</span>

                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .toggle-password {
    cursor: pointer;
  }
  
  #adminTable th {
    white-space: nowrap;
  }
  
  .table-hover tbody tr:hover {
    background-color: rgba(78, 115, 223, 0.1);
  }
</style>

<script>
  document.querySelector('.toggle-password').addEventListener('click', function() {
    const passwordInput = document.getElementById('lecture_password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  });


  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const adminId = this.dataset.id;
      const adminName = this.dataset.name;
      
      Swal.fire({
        title: 'Confirm Delete',
        html: `Are you sure you want to delete <b>${adminName}</b> (ID: ${adminId})?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          
          Swal.fire(
            'Deleted!',
            'The admin has been removed.',
            'success'
          ).then(() => {
           
            window.location.reload();
          });
        }
      });
    });
  });
  

  document.getElementById('adminForm').addEventListener('submit', function(e) {
    const password = document.getElementById('lecture_password').value;
    if (password.length < 8) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Weak Password',
        text: 'Password must be at least 8 characters long',
      });
    }
  });
</script>

<?php include 'footer.php'; ?>