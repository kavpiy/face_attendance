<?php
session_start();
require '../vendor/autoload.php';

use MongoDB\Client;

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");
        $collection = $client->face_attendance->lectures;

        $lecture = $collection->findOne(['Lecture_email' => $email]);

        if ($lecture && $lecture['Lecture_password'] === $password) {
            $_SESSION['lecture_id'] = $lecture['Lecture_id'];
            $_SESSION['lecture_name'] = $lecture['Lecture_name'];
            $_SESSION['lecture_email'] = $lecture['Lecture_email'];
            
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password!";
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
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Load Bootstrap CSS first -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Then load Font Awesome - Updated CDN link -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        .password-toggle {
            cursor: pointer;
            transition: all 0.3s;
        }
        .password-toggle:hover {
            background-color:rgb(95, 95, 95) !important;
        }
        
        /* Debugging styles - can be removed */
        .debug-icon {
            color: red;
            font-size: 24px;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Debugging icon - should appear red if Font Awesome loads -->
    <div class="debug-icon" style="position: absolute; top: 10px; left: 10px;">
        
    </div>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="width: 100%; max-width: 450px;">
            <!-- Card Header -->
            <div class="card-header bg-primary bg-gradient text-white text-center py-4">
                <h3 class="fw-bold mb-0">
                    <i class="fas fa-user-shield me-2"></i> Admin Login
                </h3>
            </div>
            
            <!-- Card Body -->
            <div class="card-body p-4 p-md-5">
                <!-- Test icon in alert -->
                <div class="alert alert-info d-none">
                    <i class="fas fa-info-circle me-2"></i> Testing icons
                </div>

                <form method="post" action="">
                    <!-- Email Input -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold">
                            Email Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-envelope me-2"></i> 
                            </span>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   placeholder="Enter your admin email">
                        </div>
                    </div>

                    <!-- Password Input with Toggle -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">
                            Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" required
                                   placeholder="Enter your password">
                            <button class="btn btn-outline-secondary password-toggle" type="button" id="togglePassword">
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
                        <a href="forget_password.php" class="text-decoration-none text-primary">
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
                this.classList.add('active');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
                this.classList.remove('active');
            }
        });
        
        // Debugging: Check if Font Awesome loaded
        setTimeout(() => {
            if (!document.querySelector('.fa-bug').clientHeight) {
                alert('Font Awesome not loading! Check your network or CDN link.');
            }
        }, 1000);
    </script>
</body>
</html>