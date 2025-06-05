<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    .password-card {
      border-radius: 12px;
      border: none;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .form-control:focus {
      border-color: #4e73df;
      box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
  </style>
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">


  <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-info alert-dismissible fade show position-absolute top-0 mt-3 col-md-4 col-10" role="alert">
      <i class="fas fa-info-circle me-2"></i>
      <?= htmlspecialchars($_GET['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card password-card shadow-lg" style="width: 100%; max-width: 450px;">
      <div class="card-header bg-primary text-white text-center py-4">
        <h4 class="fw-bold mb-0">
          <i class="fas fa-key me-2"></i> Reset Your Password
        </h4>
      </div>


      <form action="send_reset.php" method="POST">
        <div class="mb-3 px-4 mt-3">
          <label for="email" class="form-label fw-bold">
             Email Address
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light">
              <i class="fas fa-envelope me-2"></i>
            </span>
            <input type="email" class="form-control" id="email" name="email" 
                   placeholder="you@example.com" required>
          </div>
          <small class="text-muted">Enter your registered email address</small>
        </div>


        <div class="px-4">
          <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mt-1">
          <i class="fas fa-paper-plane me-2"></i> Send Reset Link
        </button>
        </div>
      </form>


        <div class="text-center py-3 mt-1">
          <a href="admin_login.php" class="text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Back to Login
          </a>
        </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>