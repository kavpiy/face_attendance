<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

require '../vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

date_default_timezone_set("Asia/Colombo");


$client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");
$db = $client->face_attendance;
$studentsCol = $db->students;
$sessionsCol = $db->sessions;
$coursesCol = $db->courses;


$studentId = $_SESSION['student_id'];
try {
    $student = $studentsCol->findOne(['_id' => new ObjectId($studentId)]);
    if (!$student) {
        $student = $studentsCol->findOne(['Student_id' => $studentId]);
    }
} catch (Exception $e) {
    $student = $studentsCol->findOne(['Student_id' => $studentId]);
}

if (!$student) {
    die("<div class='alert alert-danger'>Student not found. Please login again.</div>");
}

$academicYear = $student['Academic_year'] ?? null;
if (!$academicYear) {
    die("<div class='alert alert-danger'>Academic year not found for student.</div>");
}


$courseMap = [];
$courses = $coursesCol->find();
foreach ($courses as $course) {

    $possibleIdFields = ['_id', 'Course_id', 'course_id', 'id'];
    foreach ($possibleIdFields as $field) {
        if (isset($course[$field])) {
            $courseId = is_object($course[$field]) ? (string)$course[$field] : $course[$field];
            if (isset($course['Course_name'])) {
                $courseMap[(string)$courseId] = $course['Course_name'];
            }
        }
    }
}


$currentTime = new DateTime();
$yesterday = (clone $currentTime)->modify('-24 hours');


$sessions = $sessionsCol->find([
    'Academic_year' => $academicYear,
    'Date' => ['$gte' => $yesterday->format('Y-m-d')]
]);

$filteredSessions = [];
foreach ($sessions as $session) {
    if (!isset($session['Date'], $session['Starttime'], $session['Endtime'], $session['Course_id'])) {
        continue;
    }

    $sessionDate = $session['Date'];
    $startTime = $session['Starttime'];
    $endTime = $session['Endtime'];
    

    $courseId = is_object($session['Course_id']) ? (string)$session['Course_id'] : $session['Course_id'];
    $courseId = trim($courseId);

    try {
        $sessionStart = new DateTime($sessionDate . ' ' . $startTime);
        $sessionEnd = new DateTime($sessionDate . ' ' . $endTime);
    } catch (Exception $e) {
        continue;
    }


    $status = '';
    if ($currentTime >= $sessionStart && $currentTime <= $sessionEnd) {
        $status = 'Active';
    } elseif ($sessionStart > $currentTime) {
        $status = 'Upcoming';
    } else {
        $status = 'Completed';
    }


    $courseName = $courseMap[$courseId] ?? null;
    if (!$courseName) {

        foreach ($courseMap as $id => $name) {
            if (trim($id) === $courseId || trim(strtolower($id)) === strtolower($courseId)) {
                $courseName = $name;
                break;
            }
        }
    }

    $filteredSessions[] = [
        'Course_name' => $courseName ?? 'Unknown Course (ID: ' . $courseId . ')',
        'Date' => $sessionDate,
        'Time' => $startTime . ' - ' . $endTime,
        'Status' => $status,
        'Start' => $sessionStart,
        'End' => $sessionEnd
    ];
}


usort($filteredSessions, function($a, $b) {

    $statusOrder = ['Upcoming' => 1, 'Active' => 2, 'Completed' => 3];
    $aStatus = $statusOrder[$a['Status']];
    $bStatus = $statusOrder[$b['Status']];
    
    if ($aStatus !== $bStatus) {
        return $aStatus <=> $bStatus;
    }
    

    return $a['Start'] <=> $b['Start'];
});

include 'header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>My Class Sessions</h4>
                    <span class="badge bg-light text-dark">
                        Showing <?= count($filteredSessions) ?> sessions
                    </span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($filteredSessions)): ?>
                        <div class="alert alert-info m-4">No recent or upcoming sessions found.</div>
                    <?php else: ?>
                        <div class="table-responsive p-2">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Course</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($filteredSessions as $index => $session): ?>
                                        <tr>
                                            <td class="fw-bold"><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($session['Course_name']) ?></td>
                                            <td><?= htmlspecialchars($session['Date']) ?></td>
                                            <td><?= htmlspecialchars($session['Time']) ?></td>
                                            <td>
                                                <span class="badge <?= 
                                                    $session['Status'] === 'Active' ? 'bg-success' : 
                                                    ($session['Status'] === 'Upcoming' ? 'bg-warning text-dark' : 'bg-secondary')
                                                ?>">
                                                    <?= $session['Status'] ?>
                                                    <?php if ($session['Status'] === 'Active'): ?>
                                                        <i class="fas fa-circle-notch fa-spin ms-1"></i>
                                                    <?php endif; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>