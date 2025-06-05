<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php'; 

use MongoDB\Client;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");
    $students = $client->face_attendance->students;

    $student = $students->findOne(['Student_email' => $email]);

    if ($student) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + 3600; 

        $students->updateOne(
            ['Student_email' => $email],
            ['$set' => ['reset_token' => $token, 'reset_expires' => $expires]]
        );

        $resetLink = "http://localhost/face_attendance/student/reset_password.php?token=$token";

    
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kavindupiyumal0121@gmail.com'; 
            $mail->Password = 'veqkhygkenbehueq';  
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('kavindupiyumal0121@gmail.com', 'Student Support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body = "Hello,<br><br>Click the link below to reset your password:<br><a href='$resetLink'>$resetLink</a><br><br>This link will expire in 1 hour.";

            $mail->send();
            header("Location: forgot_password.php?msg=" . urlencode("Reset link sent to your email."));
            exit;
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        header("Location: forgot_password.php?msg=" . urlencode("Email not found."));
        exit;
    }
}
?>
