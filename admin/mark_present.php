<?php
require '../vendor/autoload.php';
use MongoDB\Client;

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$studentId = $data['studentId'] ?? '';
$date = $data['date'] ?? '';
$courseId = $data['courseId'] ?? '';

if (!$studentId || !$date || !$courseId) {
  echo json_encode(['success' => false, 'message' => 'Missing data']);
  exit;
}

try {
  $client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");
  $collection = $client->face_attendance->attendance;


  $collection->updateOne(
    [
      'Student_id' => $studentId,
      'Attendance_date' => $date,
      'Course_id' => $courseId
    ],
    [
      '$set' => ['Attendance_status' => 'Present']
    ],
    ['upsert' => true]
  );

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
