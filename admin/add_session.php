<?php
session_start();
require '../vendor/autoload.php';
use MongoDB\Client;

// Check if admin is logged in
if (!isset($_SESSION['lecture_id'])) {
    header("Location: admin_login.php");
    exit();
}

$client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");
$db = $client->face_attendance;

$sessions = $db->sessions;
$courses = $db->courses;
$students = $db->students;

$success = '';
$error = '';

// Generate new Session ID
$lastSession = $sessions->find([], ['sort' => ['Session_id' => -1], 'limit' => 1])->toArray();
$newIdNum = !empty($lastSession) ? intval(substr($lastSession[0]['Session_id'], 1)) + 1 : 1;
$newSessionId = 'S' . str_pad($newIdNum, 3, '0', STR_PAD_LEFT);

// Get academic years
$academicYears = $students->distinct('Academic_year');

// Get all courses
$allCourses = $courses->find([], ['sort' => ['Course_name' => 1]])->toArray();

// Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseId = $_POST['course_id'];
    $academicYear = $_POST['academic_year'];
    $date = $_POST['date'];
    $start = $_POST['starttime'];
    $end = $_POST['endtime'];

    try {
        // Validate time
        if (strtotime($end) <= strtotime($start)) {
            throw new Exception("End time must be after start time");
        }

        $sessions->insertOne([
            'Session_id' => $newSessionId,
            'Course_id' => $courseId,
            'Academic_year' => $academicYear,
            'Date' => $date,
            'Starttime' => $start,
            'Endtime' => $end,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);
        $success = "Session added successfully with ID: $newSessionId";
    } catch (Exception $e) {
        $error = "Failed to add session: " . $e->getMessage();
    }
}

// Get last 10 sessions
$latestSessions = $sessions->find([], ['sort' => ['Date' => -1, 'Starttime' => -1], 'limit' => 10])->toArray();

// Load all courses into a key-value map
$courseMap = [];
foreach ($courses->find() as $course) {
    $courseMap[$course['Course_id']] = $course['Course_name'];
}

date_default_timezone_set('Asia/Colombo');

function getSessionStatus($session) {
    $now = new DateTime("now", new DateTimeZone('Asia/Colombo'));
    $start = DateTime::createFromFormat('Y-m-d H:i', $session['Date'] . ' ' . $session['Starttime'], new DateTimeZone('Asia/Colombo'));
    $end = DateTime::createFromFormat('Y-m-d H:i', $session['Date'] . ' ' . $session['Endtime'], new DateTimeZone('Asia/Colombo'));

    if ($now < $start) {
        return ['status' => 'upcoming', 'class' => 'bg-warning'];
    } elseif ($now >= $start && $now <= $end) {
        return ['status' => 'active', 'class' => 'bg-success'];
    } else {
        return ['status' => 'completed', 'class' => 'bg-secondary'];
    }
}
?>

<?php include 'header.php'; ?>

<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-lg-6">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Add New Session</h4>
        </div>
        <div class="card-body">
          <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center">
              <i class="fas fa-check-circle me-2"></i>
              <?= htmlspecialchars($success) ?>
            </div>
          <?php elseif ($error): ?>
            <div class="alert alert-danger d-flex align-items-center">
              <i class="fas fa-exclamation-circle me-2"></i>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="POST" id="sessionForm">
            <div class="mb-3">
              <label class="form-label fw-bold">Session ID</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                <input type="text" class="form-control" value="<?= $newSessionId ?>" readonly>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Level</label>
              <select id="level" class="form-select" required>
                <option selected disabled>Select Level</option>
                <option value="Level 1">Level 1</option>
                <option value="Level 2">Level 2</option>
                <option value="Level 3">Level 3</option>
                <option value="Level 4">Level 4</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Course</label>
              <select name="course_id" id="course" class="form-select" required>
                <option selected disabled>Select Course</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Academic Year</label>
              <select name="academic_year" class="form-select" required>
                <option selected disabled>Select Academic Year</option>
                <?php foreach ($academicYears as $year): ?>
                  <option value="<?= htmlspecialchars($year) ?>"><?= htmlspecialchars($year) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Date</label>
                <input type="date" name="date" class="form-control" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Start Time</label>
                <input type="time" name="starttime" class="form-control" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">End Time</label>
                <input type="time" name="endtime" class="form-control" required>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
              <i class="fas fa-save me-2"></i> Create Session
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h4 class="mb-0"><i class="fas fa-history me-2"></i>Recent Sessions</h4>
          <span class="badge bg-light text-dark">
            Showing <?= count($latestSessions) ?> sessions
          </span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive p-2">
            <table class="table table-hover mb-0">
              <thead class="table-dark">
                <tr>
                  <th>Session</th>
                  <th>Course</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($latestSessions as $session): 
                  $status = getSessionStatus($session);
                  $courseName = isset($courseMap[$session['Course_id']]) ? $courseMap[$session['Course_id']] : 'Unknown';
                ?>
                <tr>
                  <td class="fw-bold"><?= htmlspecialchars($session['Session_id']) ?></td>
                  <td><?= htmlspecialchars($courseName) ?></td>
                  <td><?= htmlspecialchars($session['Date']) ?></td>
                  <td><?= htmlspecialchars($session['Starttime']) ?> - <?= htmlspecialchars($session['Endtime']) ?></td>
                  <td>
                    <span class="badge <?= $status['class'] ?>">
                      <?= ucfirst($status['status']) ?>
                      <?php if ($status['status'] === 'active'): ?>
                        <i class="fas fa-circle-notch fa-spin ms-1"></i>
                      <?php endif; ?>
                    </span>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .table-hover tbody tr:hover {
    background-color: rgba(78, 115, 223, 0.1);
  }
  
  .badge.bg-success {
    color: white;
  }
  
  .badge.bg-warning {
    color: #212529;
  }
</style>

<script>
  // Courses embedded in JS
  const allCourses = <?= json_encode($allCourses) ?>;

  const levelSelect = document.getElementById("level");
  const courseSelect = document.getElementById("course");

  levelSelect.addEventListener("change", () => {
    const selectedLevel = levelSelect.value;
    courseSelect.innerHTML = '<option disabled selected>Select Course</option>';

    const filteredCourses = allCourses.filter(c => c.Level === selectedLevel);
    filteredCourses.forEach(course => {
      const option = document.createElement("option");
      option.value = course.Course_id;
      option.text = `${course.Course_name}`;
      courseSelect.appendChild(option);
    });
  });

  // Form validation
  document.getElementById('sessionForm').addEventListener('submit', function(e) {
    const startTime = document.querySelector('input[name="starttime"]').value;
    const endTime = document.querySelector('input[name="endtime"]').value;
    
    if (startTime && endTime && startTime >= endTime) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Invalid Time',
        text: 'End time must be after start time',
      });
    }
  });

  // Set default time to current time + 30 minutes
  document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    const later = new Date(now.getTime() + 30 * 60000);
    
    // Format time as HH:MM
    const formatTime = (date) => {
      return date.toTimeString().slice(0, 5);
    };
    
    document.querySelector('input[name="date"]').valueAsDate = now;
    document.querySelector('input[name="starttime"]').value = formatTime(now);
    document.querySelector('input[name="endtime"]').value = formatTime(later);
  });
</script>

<?php include 'footer.php'; ?>