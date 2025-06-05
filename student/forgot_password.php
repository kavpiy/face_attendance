<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <style>
    .password-card {
      border-radius: 12px;
      border: none;
      overflow: hidden;
    }
    .form-control:focus {
      border-color: #4e73df;
      box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
  </style>
</head>
<body class="bg-light">
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card password-card shadow-lg" style="width: 100%; max-width: 450px;">
      <!-- Card Header -->
      <div class="card-header bg-primary text-white text-center py-4">
        <h4 class="fw-bold mb-0">
          <i class="fas fa-key me-2"></i> Reset Your Password
        </h4>
      </div>
      
      <!-- Card Body -->
      <div class="card-body p-4 ">
        <?php if (isset($_GET['msg'])): ?>
          <div class="alert alert-info alert-dismissible fade show">
            <i class="fas fa-info-circle me-2"></i>
            <?= htmlspecialchars($_GET['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <form method="POST" action="send_reset.php">
          <div class="mb-4">
            <label for="email" class="form-label fw-bold">
              Student Email
            </label>
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="fas fa-envelope"></i>
              </span>
              <input type="email" name="email" class="form-control" 
                     placeholder="student@stu.cmb.ac.lk" required>
            </div>
            <small class="text-muted">Enter your registered email address</small>
          </div>
          
          <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-3">
            <i class="fas fa-paper-plane me-2"></i> Send Reset Link
          </button>
        </form>

        <div class="text-center mt-1">
          <a href="student_login.php" class="text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Back to Login
          </a>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>