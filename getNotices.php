<?php
// ============================================
// Notices API
// Endpoint: /api/getNotices.php
// ============================================

session_start();
header('Content-Type: application/json');
include('../config/db_connection.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$role = $_SESSION['role'];
$stmt = mysqli_prepare($conn,
    "SELECT * FROM notices
     WHERE target_role = 'all' OR target_role = ?
     ORDER BY created_at DESC LIMIT 10"
);
mysqli_stmt_bind_param($stmt, "s", $role);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$notices = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notices[] = $row;
}

echo json_encode(['success' => true, 'notices' => $notices]);
mysqli_close($conn);
?>
