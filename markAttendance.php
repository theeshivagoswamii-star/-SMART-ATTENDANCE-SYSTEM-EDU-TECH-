<?php
// ============================================
// Mark Attendance API
// Endpoint: /api/markAttendance.php
// Method: POST
// ============================================

session_start();
header('Content-Type: application/json');

include('../config/db_connection.php');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$subject_id  = intval($_POST['subject_id'] ?? 0);
$date        = $_POST['date'] ?? date('Y-m-d');
$records     = json_decode($_POST['records'] ?? '[]', true);

if (!$subject_id || empty($records)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Get teacher_id from session
$t = mysqli_prepare($conn, "SELECT teacher_id FROM teachers WHERE user_id = ?");
mysqli_stmt_bind_param($t, "i", $_SESSION['user_id']);
mysqli_stmt_execute($t);
$teacher = mysqli_fetch_assoc(mysqli_stmt_get_result($t));
$teacher_id = $teacher['teacher_id'] ?? null;

$success_count = 0;
$errors = [];

foreach ($records as $record) {
    $student_id = intval($record['student_id']);
    $status     = in_array($record['status'], ['Present', 'Absent', 'Late']) ? $record['status'] : 'Absent';

    // INSERT or UPDATE
    $stmt = mysqli_prepare($conn,
        "INSERT INTO attendance (student_id, subject_id, date, status, marked_by)
         VALUES (?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE status = VALUES(status), marked_by = VALUES(marked_by)"
    );
    mysqli_stmt_bind_param($stmt, "iissi", $student_id, $subject_id, $date, $status, $teacher_id);

    if (mysqli_stmt_execute($stmt)) {
        $success_count++;
    } else {
        $errors[] = "Failed for student_id: $student_id";
    }
}

echo json_encode([
    'success' => true,
    'marked'  => $success_count,
    'errors'  => $errors,
    'message' => "Attendance marked for $success_count students"
]);

mysqli_close($conn);
?>
