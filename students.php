<?php
require 'vendor/autoload.php';

use MongoDB\Client;


$client = new Client("mongodb+srv://kavindupiyumal0121:7mQRouCy34geTQGS@cluster0.erbnzvi.mongodb.net/face_attendance");

$db = $client->face_attendance;
$students = $db->students;


$all_students = $students->find();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Students</title>
</head>
<body>

<h2>Student List</h2>

<table>
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Academic Year</th>
            <th>Department ID</th>
            <th>NIC</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($all_students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['Student_id']); ?></td>
                <td><?php echo htmlspecialchars($student['Student_name']); ?></td>
                <td><?php echo htmlspecialchars($student['Academic_year']); ?></td>
                <td><?php echo htmlspecialchars($student['Department_id']); ?></td>
                <td><?php echo htmlspecialchars($student['Student_nic']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
