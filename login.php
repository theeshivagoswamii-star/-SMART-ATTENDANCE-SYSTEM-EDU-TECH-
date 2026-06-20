<?php
// ============================================
// Login API
// Endpoint: /api/login.php
// Method: POST
// ============================================

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include('../config/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

// Use prepared statement to prevent SQL injection
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);

    $_SESSION['user_id']  = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role'];

    // Fetch additional info based on role
    $extra = [];
    if ($user['role'] === 'student') {
        $s = mysqli_prepare($conn, "SELECT * FROM students WHERE user_id = ?");
        mysqli_stmt_bind_param($s, "i", $user['user_id']);
        mysqli_stmt_execute($s);
        $extra = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
    } elseif ($user['role'] === 'teacher') {
        $t = mysqli_prepare($conn, "SELECT * FROM teachers WHERE user_id = ?");
        mysqli_stmt_bind_param($t, "i", $user['user_id']);
        mysqli_stmt_execute($t);
        $extra = mysqli_fetch_assoc(mysqli_stmt_get_result($t));
    }

    echo json_encode([
        'success'  => true,
        'role'     => $user['role'],
        'username' => $user['username'],
        'extra'    => $extra,
        'redirect' => $user['role'] . '_dashboard.html'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
}

mysqli_close($conn);
?>
