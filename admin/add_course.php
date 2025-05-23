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

$courses = $db->courses;
$departments = $db->departments;

$success = '';
$error = '';

// Generate Course ID like C001, C002
$lastCourse = $courses->find([], ['sort' => ['Course_id' => -1], 'limit' => 1])->toArray();
$newIdNum = !empty($lastCourse) ? intval(substr($lastCourse[0]['Course_id'], 1)) + 1 : 1;
$newCourseId = 'C' . str_pad($newIdNum, 3, '0', STR_PAD_LEFT);

// Get department list
$departmentList = $departments->find([], ['sort' => ['Department_name' => 1]])->toArray();

// Handle course insert
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_course'])) {
    $courseName = trim($_POST['course_name']);
    $departmentId = $_POST['department_id'];
    $level = $_POST['level'];

    try {
        // Validate inputs
        if (empty($courseName)) {
            throw new Exception("Course name cannot be empty");
        }

        $courses->insertOne([
            'Course_id' => $newCourseId,
            'Course_name' => $courseName,
            'Department_id' => $departmentId,
            'Level' => $level,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);
        $success = "Course added successfully with ID: $newCourseId";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Filter logic
$filter = [];
$selectedDept = '';
$selectedLevel = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['filter'])) {
    if (!empty($_POST['filter_department'])) {
        $filter['Department_id'] = $_POST['filter_department'];
        $selectedDept = $_POST['filter_department'];
    }
    if (!empty($_POST['filter_level'])) {
        $filter['Level'] = $_POST['filter_level'];
        $selectedLevel = $_POST['filter_level'];
    }
}

// Fetch filtered course list
$filteredCourses = $courses->find($filter, ['sort' => ['Level' => 1, 'Course_name' => 1]]);
?>

<?php include 'header.php'; ?>

<style>
  .course-card {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  
  .course-card-header {
    border-radius: 10px 10px 0 0 !important;
  }
  
  .form-label {
    font-weight: 600;
    color: #495057;
  }
  
  .table-hover tbody tr:hover {
    background-color: rgba(78, 115, 223, 0.1);
  }
  
  .badge-level {
    font-size: 0.8rem;
    padding: 0.35em 0.65em;
  }
  
  .badge-level-1 { background-color: #4e73df; }
  .badge-level-2 { background-color: #1cc88a; }
  .badge-level-3 { background-color: #f6c23e; color: #212529; }
  .badge-level-4 { background-color: #e74a3b; }
  
  .department-badge {
    background-color: #858796;
    color: white;
    font-size: 0.75rem;
  }
</style>

<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-lg-6">
      <div class="card course-card mb-4">
        <div class="card-header bg-primary text-white course-card-header">
          <h4 class="mb-0"><i class="fas fa-book me-2"></i>Add New Course</h4>
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

          <form method="POST">
            <input type="hidden" name="add_course" value="1">
            <div class="mb-3">
              <label class="form-label">Course ID</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                <input type="text" class="form-control" value="<?= $newCourseId ?>" readonly>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Course Name</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-book"></i></span>
                <input type="text" class="form-control" name="course_name" required>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Department</label>
              <select class="form-select" name="department_id" required>
                <option selected disabled>Select Department</option>
                <?php foreach ($departmentList as $dept): ?>
                  <option value="<?= $dept['Department_id'] ?>">
                    <?= htmlspecialchars($dept['Department_name']) ?> (<?= htmlspecialchars($dept['Department_id']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-4">
              <label class="form-label">Level</label>
              <select class="form-select" name="level" required>
                <option selected disabled>Select Level</option>
                <option value="Level 1">Level 1</option>
                <option value="Level 2">Level 2</option>
                <option value="Level 3">Level 3</option>
                <option value="Level 4">Level 4</option>
              </select>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
              <i class="fas fa-plus-circle me-2"></i> Add Course
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card course-card">
        <div class="card-header bg-primary text-white course-card-header">
          <h4 class="mb-0"><i class="fas fa-filter me-2"></i>Course Management</h4>
        </div>
        <div class="card-body">
          <form method="POST" class="row g-3 mb-4">
            <input type="hidden" name="filter" value="1">
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <select name="filter_department" class="form-select">
                <option value="">All Departments</option>
                <?php foreach ($departmentList as $dept): ?>
                  <option value="<?= $dept['Department_id'] ?>" <?= ($selectedDept == $dept['Department_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dept['Department_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Level</label>
              <select name="filter_level" class="form-select">
                <option value="">All Levels</option>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                  <option value="Level <?= $i ?>" <?= ($selectedLevel == "Level $i") ? 'selected' : '' ?>>
                    Level <?= $i ?>
                  </option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-secondary w-100 py-2">
                <i class="fas fa-filter me-2"></i> Apply Filters
              </button>
            </div>
          </form>

          <h5 class="mb-3"><i class="fas fa-list me-2"></i>Course List</h5>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-dark">
                <tr>
                  <th>Course</th>
                  <th>Level</th>
                  <th>Department</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($filteredCourses as $course): 
                  $deptName = '';
                  foreach ($departmentList as $dept) {
                    if ($dept['Department_id'] === $course['Department_id']) {
                      $deptName = $dept['Department_name'];
                      break;
                    }
                  }
                  $levelNum = substr($course['Level'], -1);
                ?>
                  <tr>
                    <td class="fw-bold"><?= htmlspecialchars($course['Course_name']) ?></td>
                    <td>
                      <span class="badge badge-level badge-level-<?= $levelNum ?>">
                        <?= htmlspecialchars($course['Level']) ?>
                      </span>
                    </td>
                    <td>
                      <span class="badge department-badge">
                        <?= htmlspecialchars($deptName) ?>
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

<?php include 'footer.php'; ?>