<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Make sure this includes PHPMailer too

use MongoDB\Client;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Connect to MongoDB
    $client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");
    $lectures = $client->face_attendance->lectures;

    // Find user by email
    $user = $lectures->findOne(['Lecture_email' => $email]);

    if ($user) {
        // Generate token and expiration (1 hour from now)
        $token = bin2hex(random_bytes(16));
        $expires = time() + 3600;

        // Save token to MongoDB
        $lectures->updateOne(
            ['Lecture_email' => $email],
            ['$set' => ['reset_token' => $token, 'reset_expires' => $expires]]
        );

        // Send reset email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kavindupiyumal0121@gmail.com';
            $mail->Password = 'veqkhygkenbehueq'; // ✅ Use your App Password here (no spaces!)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('kavindupiyumal0121@gmail.com', 'Admin');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Link';
            $resetLink = "localhost/face_attendance/admin/reset_password.php?token=$token";
            $mail->Body = "Hello,<br><br>Click the link below to reset your password:<br><a href='$resetLink'>$resetLink</a><br><br>This link will expire in 1 hour.";

            $mail->send();
            header("Location: forget_password.php?msg=" . urlencode("Reset email sent!"));
            exit();

        } catch (Exception $e) {
            echo "Error sending email: " . $mail->ErrorInfo;
        }
    } else {
        header("Location: forget_password.php?msg=" . urlencode("Email not found."));
        exit();
    }
}
?>
