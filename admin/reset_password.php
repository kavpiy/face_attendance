<?php
require '../vendor/autoload.php';
use MongoDB\Client;

$token = $_GET['token'] ?? '';
$client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");
$lectures = $client->face_attendance->lectures;

$user = $lectures->findOne(['reset_token' => $token]);
$statusMessage = '';
$statusType = '';

if (!$user || $user['reset_expires'] < time()) {
    $statusMessage = 'Invalid or expired token.';
    $statusType = 'danger';
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPass = $_POST['password'];

    $lectures->updateOne(
        ['reset_token' => $token],
        [
            '$set' => ['Lecture_password' => $newPass],
            '$unset' => ['reset_token' => "", 'reset_expires' => ""]
        ]
    );
    $statusMessage = 'Password has been reset successfully.';
    $statusType = 'success';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <?php if (!empty($statusMessage)): ?>
          <div class="alert alert-<?= $statusType ?> alert-dismissible fade show text-center" role="alert">
            <?= htmlspecialchars($statusMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if ($statusType !== 'danger'): ?>
        <div class="card shadow-sm py-4">
          <div class=" text-center ">
            <h4 class="mb-0">Set a New Password</h4>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="mb-3">
                <label for="password" class="form-label">Enter New Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Reset Password</button>
              </div>
            </form>
          </div>
          <div class=" text-center">
            <a href="admin_login.php" class="text-decoration-none text-primary">
              <i class="fas fa-arrow-left me-1"></i>Back to Login
            </a>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</body>
</html>
