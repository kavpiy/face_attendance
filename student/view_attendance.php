<?php
session_start();
require '../vendor/autoload.php';
use MongoDB\Client;

$client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");
$db = $client->face_attendance;

$studentsCol = $db->students;
$coursesCol = $db->courses;
$sessionsCol = $db->sessions;
$attendanceCol = $db->attendance;


$studentId = $_SESSION['student_id'] ?? null;

if (!$studentId) {
    die("Unauthorized access");
}

$student = $studentsCol->findOne(['Student_id' => $studentId]);

if (!$student) {
    die("Student not found");
}

$studentName = $student['Student_name'];
$academicYear = $student['Academic_year'];


$levels = ['Level 1', 'Level 2', 'Level 3', 'Level 4'];
$selectedLevel = $_GET['level'] ?? '';


$courses = [];
if ($selectedLevel) {
    $courses = $coursesCol->find(['Level' => $selectedLevel])->toArray();
}
?>

<?php include 'header.php'; ?>

<div class="container-fluid my-3">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>View Attendance</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Select Level</label>
                    <select name="level" class="form-select" required>
                        <option value="">-- Select Level --</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?= $level ?>" <?= $selectedLevel === $level ? 'selected' : '' ?>>
                                <?= $level ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Search</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($selectedLevel && count($courses) > 0): ?>
        <?php foreach ($courses as $course): ?>
            <?php
                $courseId = $course['Course_id'];
                $courseName = $course['Course_name'];

                // Get all sessions for this course
                $sessionDates = iterator_to_array($sessionsCol->find([
                    'Course_id' => $courseId
                ], ['sort' => ['Date' => 1]]));
                
                $dates = array_column($sessionDates, 'Date');

                // Get attendance for student for this course
                $attendanceCursor = $attendanceCol->find([
                    'Course_id' => $courseId,
                    'Student_id' => $studentId,
                    'Attendance_date' => ['$in' => $dates]
                ]);

                $attendanceMap = [];
                foreach ($attendanceCursor as $att) {
                    $attendanceMap[$att['Attendance_date']] = $att['Attendance_status'];
                }

                $presentCount = 0;
                $totalSessions = count($dates);
            ?>
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-book me-2"></i><?= htmlspecialchars($courseName) ?></h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Course</th>
                                <?php foreach ($dates as $date): ?>
                                    <th><?= htmlspecialchars($date) ?></th>
                                <?php endforeach; ?>
                                <th>Attendance %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= htmlspecialchars($courseName) ?></td>
                                <?php foreach ($dates as $date): ?>
                                    <td>
                                        <?php
                                            $status = $attendanceMap[$date] ?? 'Absent';
                                            if ($status === 'Present') {
                                                echo '<span class="badge bg-success">Present</span>';
                                                $presentCount++;
                                            } else {
                                                echo '<span class="badge bg-danger">Absent</span>';
                                            }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <?php
                                        $percentage = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100) : 0;
                                        echo $percentage . '%';
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif ($selectedLevel): ?>
        <div class="alert alert-warning">No courses found for selected level.</div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="student_dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
