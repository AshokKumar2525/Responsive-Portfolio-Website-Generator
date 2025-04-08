<?php
session_start();
header('Content-Type: application/json'); // Set JSON header

// Database connection
$servername = "localhost";
$username = "root"; 
$password = "Ashok@123"; 
$dbname = "portfolio";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        die( "<script>alert('All Fields Are Required.'); window.location.href = 'login.html';</script>");
    }

    // Check if email exists
    $sql = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        die( "<script>alert('No Account Found With This Email!'); window.location.href = 'login.html';</script>");
    }

    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    // Verify password
    if (!password_verify($password, $hashed_password)) {
        die( "<script>alert('Incorrect Password! Try again.'); window.location.href = 'login.html';</script>");
    }

    // Regenerate session ID to prevent fixation
    // session_regenerate_id(true);

    // Store user ID in session
    $_SESSION['user_id'] = $user_id;
    // $_SESSION['last_activity'] = time();

    // // Set secure session cookie
    // setcookie(session_name(), session_id(), [
    //     'expires' => time() + 86400, // 1 day
    //     'path' => '/',
    //     'domain' => '',
    //     'secure' => true,
    //     'httponly' => true,
    //     'samesite' => 'Strict'
    // ]);

    header("Location: index.html");
    exit;
}

$conn->close();
?>