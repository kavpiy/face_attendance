<?php
session_start();
require '../vendor/autoload.php';

use MongoDB\Client;

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentId = $_POST['student_id'];
    $password = $_POST['password'];

    try {
        $client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");
        $collection = $client->face_attendance->students;

        $student = $collection->findOne(['Student_id' => $studentId]);

        if ($student && $student['Student_password'] === $password) {
            $_SESSION['student_id'] = $student['Student_id'];
            $_SESSION['student_name'] = $student['Student_name'];
            $_SESSION['student_nic'] = $student['Student_nic'];
            $_SESSION['student_email'] = $student['Student_email'];
            
            header("Location: student_dashboard.php");
            exit();
        } else {
            $error = "Invalid Student ID or Password!";
        }
    } catch (Exception $e) {
        $error = "Database connection error!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="width: 100%; max-width: 450px;">
            <!-- Card Header -->
            <div class="card-header bg-primary bg-gradient text-white text-center py-4">
                <h3 class="fw-bold mb-0">
                    <i class="fas fa-user-graduate me-2"></i> Student Login
                </h3>
            </div>
            
            <!-- Card Body -->
            <div class="card-body p-4 p-md-5">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <!-- Student ID Input -->
                    <div class="mb-4">
                        <label for="student_id" class="form-label fw-bold">
                            Student ID
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-user text"></i>
                            </span>
                            <input type="text" class="form-control" id="student_id" name="student_id" required 
                                   placeholder="Enter your student ID">
                        </div>
                    </div>

                    <!-- Password Input with Toggle -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">
                            Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-key text"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" required
                                   placeholder="Enter your password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>

                    <!-- Links -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="../login.php" class="text-decoration-none text-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to selection
                        </a>
                        <a href="forgot_password.php" class="text-decoration-none text-primary">
                            <i class="fas fa-question-circle me-1"></i> Forgot password?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Password Toggle Script -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    </script>
</body>
</html>