<?php
require '../vendor/autoload.php';
use MongoDB\Client;

$token = $_GET['token'] ?? '';
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPassword = $_POST['new_password'];
    $token = $_POST['token'];

    $client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");
    $students = $client->face_attendance->students;

    $student = $students->findOne([
        'reset_token' => $token,
        'reset_expires' => ['$gt' => time()]
    ]);

    if ($student) {
        $students->updateOne(
            ['_id' => $student['_id']],
            [
                '$set' => ['Student_password' => $newPassword],
                '$unset' => ['reset_token' => "", 'reset_expires' => ""]
            ]
        );
        $message = "Password updated successfully. <a href='student_login.php'>Login</a>";
    } else {
        $message = "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
  <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
    <h4 class="text-center mb-3">Enter New Password</h4>

    <?php if ($message): ?>
      <div class="alert alert-info"><?= $message ?></div>
    <?php else: ?>
      <form method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="mb-3">
          <label for="new_password" class="form-label">New Password</label>
          <input type="password" name="new_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
