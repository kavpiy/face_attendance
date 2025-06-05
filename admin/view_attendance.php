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


$levels = ['Level 1', 'Level 2', 'Level 3', 'Level 4'];


$selectedLevel = $_GET['level'] ?? '';
$selectedCourse = $_GET['course'] ?? '';
$selectedYear = $_GET['academic_year'] ?? '';


$allCourses = $coursesCol->find([], ['sort' => ['Course_name' => 1]])->toArray();


$coursesForLevel = [];
if ($selectedLevel) {
    $coursesForLevel = $coursesCol->find(['Level' => $selectedLevel], ['sort' => ['Course_name' => 1]])->toArray();
}


$academicYears = $studentsCol->distinct('Academic_year');
sort($academicYears);


$sessionDates = [];
if ($selectedCourse) {
    $sessionCursor = $sessionsCol->find(['Course_id' => $selectedCourse]);
    foreach ($sessionCursor as $session) {
        $sessionDates[] = $session['Date'];
    }
    sort($sessionDates);
}

$students = [];
if ($selectedCourse && $selectedYear) {
    $students = $studentsCol->find(
        ['Academic_year' => $selectedYear],
        ['sort' => ['Student_id' => 1]] 
    )->toArray();
}


$attendanceMap = [];
if ($selectedCourse && !empty($sessionDates)) {
    $attCursor = $attendanceCol->find([
        'Course_id' => $selectedCourse,
        'Attendance_date' => ['$in' => $sessionDates]
    ]);

    foreach ($attCursor as $att) {
        $sid = $att['Student_id'];
        $date = $att['Attendance_date'];
        $status = $att['Attendance_status'];
        $attendanceMap[$sid][$date] = $status;
    }
}


?>

<?php include 'header.php'; ?>
<div class="container-fluid my-2">

<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Attendance Management</h5>
  </div>
  <div class="p-2">
    
  <!-- Filter Form -->
  <form method="GET" class="row mb-4" id="filterForm">
    <div class="col-md-3">
      <label class="form-label fw-bold">Level</label>
      <select name="level" class="form-select" id="levelSelect" required>
        <option value="">-- Select Level --</option>
        <?php foreach ($levels as $level): ?>
          <option value="<?= $level ?>" <?= $selectedLevel === $level ? 'selected' : '' ?>>
            <?= $level ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label fw-bold">Course</label>
      <select name="course" class="form-select" id="courseSelect" required>
        <option value="">-- Select Course --</option>
        <?php foreach ($coursesForLevel as $course): ?>
          <option value="<?= $course['Course_id'] ?>" <?= $selectedCourse === $course['Course_id'] ? 'selected' : '' ?>>
            <?= $course['Course_name'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label fw-bold">Academic Year</label>
      <select name="academic_year" class="form-select" required>
        <option value="">-- Select Year --</option>
        <?php foreach ($academicYears as $year): ?>
          <option value="<?= $year ?>" <?= $selectedYear === $year ? 'selected' : '' ?>>
            <?= $year ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>


    <div class="col-md-3 d-flex align-items-end">
      <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i> Search</button>
    </div>
  </form>
  </div>
</div>



  <?php if ($selectedCourse && $selectedYear && count($sessionDates) > 0): ?>
    
<div class="card">
<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap">
  <h5 class="mb-0">
    <i class="fas fa-calendar-alt me-2"></i>
    <?= htmlspecialchars($coursesCol->findOne(['Course_id' => $selectedCourse])['Course_name'] ?? 'Selected Course') ?>
  </h5>
  
  <div class="d-flex flex-wrap gap-2">
    <span class="badge bg-light text-dark">
      <i class="fas fa-users me-1"></i> <?= count($students) ?> Students
    </span>
    <span class="badge bg-light text-dark">
      <i class="fas fa-calendar-day me-1"></i> <?= count($sessionDates) ?> Sessions
    </span>
  </div>
</div>

  
<div class="row mb-3 align-items-end px-2 mt-2">
  <?php if (!empty($students)): ?>
    <div class="col-md-4 ">
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-filter"></i></span>
        <input type="number" id="percentageFilter" class="form-control" placeholder="Filter by minimum attendance %" min="0" max="100">
      </div>
    </div>
  <?php endif; ?>

  <div class="col-md-3 ms-auto  text-md-start text-center">
    <button id="downloadExcel" class="btn btn-success w-100 w-md-auto">
      <i class="fas fa-file-excel me-1"></i> Download as Excel
    </button>
  </div>
</div>

  
    <div class="table-responsive px-2">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>Student ID</th>
            <th>Student Name</th>
            <?php foreach ($sessionDates as $date): ?>
              <th><?= htmlspecialchars($date) ?></th>
            <?php endforeach; ?>
            <th>Average Attendance</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($students as $student): ?>
            <?php
              $presentCount = 0;
              $totalSessions = count($sessionDates);
            ?>
            <tr>
              <td><?= $student['Student_id'] ?></td>
              <td><?= $student['Student_name'] ?></td>
<?php foreach ($sessionDates as $date): ?>
  <td>
    <?php
      $sid = $student['Student_id'];
      $status = $attendanceMap[$sid][$date] ?? 'Absent';
      if ($status === 'Present') {
        echo '<span class="badge bg-success ">Present</span>';
        $presentCount++;
      } else {
        echo '<div class="d-flex justify-content-between align-items-center">';
        echo '  <span class="badge bg-danger me-2" style="font-size: 0.75rem;">Absent</span>';
        echo '  <button class="btn btn-outline-danger btn-sm btn-mark" style="background-color: #fff;" data-student="' . $sid . '" data-date="' . $date . '" data-course="' . $selectedCourse . '">✔</button>';
        echo '</div>';

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
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
</div>


  <?php elseif ($selectedCourse): ?>
    <div class="alert alert-warning mt-3">No sessions found for selected course.</div>
  <?php endif; ?>

    <?php if ($selectedCourse && $selectedYear && count($sessionDates) > 0): ?>

    <?php endif; ?>
  <div class="text-center mt-4">
    <a href="admin_dashboard.php" class="col-md-2 btn btn-secondary">
      <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
    </a>
  </div>

</div>


<script>
  const allCourses = <?= json_encode($allCourses) ?>;
  const levelSelect = document.getElementById("levelSelect");
  const courseSelect = document.getElementById("courseSelect");

  levelSelect.addEventListener("change", function () {
    const selectedLevel = this.value;
    courseSelect.innerHTML = '<option value="">-- Select Course --</option>';

    const filtered = allCourses.filter(c => c.Level === selectedLevel);
    filtered.forEach(c => {
      const opt = document.createElement("option");
      opt.value = c.Course_id;
      opt.text = c.Course_name;
      courseSelect.appendChild(opt);
    });

    courseSelect.selectedIndex = 0;
  });
</script>

<script>
  const percentageInput = document.getElementById('percentageFilter');
  if (percentageInput) {
    percentageInput.addEventListener('input', function () {
      const minPercentage = parseFloat(this.value.trim());
      const rows = document.querySelectorAll("table tbody tr");

      rows.forEach(row => {
        const avgCell = row.cells[row.cells.length - 1];
        const avgValue = parseFloat(avgCell.textContent.replace('%', '').trim());

        if (isNaN(minPercentage)) {
          row.style.display = ''; 
        } else {
          row.style.display = avgValue >= minPercentage ? '' : 'none';
        }
      });
    });
  }
</script>


<script>
  document.getElementById("downloadExcel")?.addEventListener("click", function () {
    const table = document.querySelector("table");
    const workbook = XLSX.utils.table_to_book(table, { sheet: "Attendance" });
    XLSX.writeFile(workbook, "attendance_report.xlsx");
  });
</script>


<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".btn-mark").forEach(btn => {
    btn.addEventListener("click", function () {
      const studentId = this.dataset.student;
      const date = this.dataset.date;
      const courseId = this.dataset.course;
      const button = this;

      Swal.fire({
        title: 'Are you sure?',
        text: `Mark student ${studentId} as Present on ${date}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, mark as Present',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch("mark_present.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ studentId, date, courseId }),
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              const cell = button.closest("td");
              cell.innerHTML = '<span class="badge bg-success">Present</span>';
              Swal.fire('Success!', 'Attendance updated.', 'success');
            } else {
              Swal.fire('Error', data.message || 'Something went wrong.', 'error');
            }
          })
          .catch(err => {
            Swal.fire('Error', 'Request failed: ' + err, 'error');
          });
        }
      });
    });
  });
});
</script>



<?php include 'footer.php'; ?>
