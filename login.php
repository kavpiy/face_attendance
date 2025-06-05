<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Options</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden w-100" style="max-width: 450px;">
      
      <div class="card-header bg-primary bg-gradient text-white text-center py-4">
        <h2 class="fw-bold mb-0">
          <i class="fas fa-sign-in-alt me-2"></i> Login As
        </h2>
      </div>
      
      
      <div class="card-body p-4 p-md-5">
        <div class="d-grid gap-3">
          <a href="student/student_login.php" 
             class="btn btn-primary btn-lg rounded-3 py-3 text-white fw-bold">
            <i class="fas fa-user-graduate me-3 fs-4"></i> 
            <span class="align-middle">Student Login</span>
          </a>
          

          <a href="admin/admin_login.php" 
             class="btn btn-dark btn-lg rounded-3 py-3 text-white fw-bold">
            <i class="fas fa-user-shield me-3 fs-4"></i> 
            <span class="align-middle">Admin Login</span>
          </a>
          

          <div class="text-center mt-4">
            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4">
              <i class="fas fa-home me-2"></i> Return Home
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>