<?php
// ============================================
// Get Attendance API
// Endpoint: /api/getAttendance.php
// Method: POST/GET
// ============================================

session_start();
header('Content-Type: application/json');

include('../config/db_connection.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$role = $_SESSION['role'];

// ---- STUDENT: get own attendance ----
if ($role === 'student') {
    $s = mysqli_prepare($conn, "SELECT student_id FROM students WHERE user_id = ?");
    mysqli_stmt_bind_param($s, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($s);
    $student = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
    $student_id = $student['student_id'] ?? 0;

    $stmt = mysqli_prepare($conn,
        "SELECT sub.subject_name, sub.subject_code,
                COUNT(a.attendance_id) AS total_classes,
                SUM(a.status = 'Present') AS present_count,
                SUM(a.status = 'Absent') AS absent_count,
                ROUND((SUM(a.status = 'Present') / COUNT(a.attendance_id)) * 100, 1) AS percentage
         FROM attendance a
         JOIN subjects sub ON a.subject_id = sub.subject_id
         WHERE a.student_id = ?
         GROUP BY sub.subject_id"
    );
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    // Overall percentage
    $overall = mysqli_prepare($conn,
        "SELECT COUNT(*) AS total, SUM(status = 'Present') AS present
         FROM attendance WHERE student_id = ?"
    );
    mysqli_stmt_bind_param($overall, "i", $student_id);
    mysqli_stmt_execute($overall);
    $ov = mysqli_fetch_assoc(mysqli_stmt_get_result($overall));
    $overall_pct = $ov['total'] > 0 ? round(($ov['present'] / $ov['total']) * 100, 1) : 0;

    echo json_encode(['success' => true, 'subjects' => $data, 'overall' => $overall_pct]);
}

// ---- TEACHER: get class attendance ----
elseif ($role === 'teacher') {
    $subject_id = intval($_POST['subject_id'] ?? $_GET['subject_id'] ?? 0);
    $date = $_POST['date'] ?? $_GET['date'] ?? date('Y-m-d');

    $stmt = mysqli_prepare($conn,
        "SELECT s.student_id, s.name, s.roll_no,
                COALESCE(a.status, 'Not Marked') AS status
         FROM students s
         LEFT JOIN attendance a ON a.student_id = s.student_id
             AND a.subject_id = ? AND a.date = ?
         WHERE s.branch = (SELECT branch FROM subjects WHERE subject_id = ?)
         ORDER BY s.roll_no"
    );
    mysqli_stmt_bind_param($stmt, "isi", $subject_id, $date, $subject_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'students' => $data]);
}

// ---- ADMIN: get full report ----
elseif ($role === 'admin') {
    $stmt = mysqli_prepare($conn,
        "SELECT s.name, s.roll_no, s.branch, s.semester,
                COUNT(a.attendance_id) AS total_classes,
                SUM(a.status = 'Present') AS present_count,
                ROUND((SUM(a.status = 'Present') / COUNT(a.attendance_id)) * 100, 1) AS percentage
         FROM students s
         LEFT JOIN attendance a ON a.student_id = s.student_id
         GROUP BY s.student_id
         ORDER BY s.branch, s.roll_no"
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'report' => $data]);
}

mysqli_close($conn);
?>
