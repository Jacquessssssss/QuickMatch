<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}

$userId = $_SESSION['user_id'];

$old = $_POST['old_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (empty($old) || empty($new) || empty($confirm)) {
    die("All fields are required.");
}

if ($new !== $confirm) {
    die("New passwords do not match.");
}

// Get current password
$query = $conn->prepare("SELECT password FROM users WHERE id = ?");
$query->bind_param("i", $userId);
$query->execute();
$query->bind_result($realPassword);
$query->fetch();
$query->close();

if (!password_verify($old, $realPassword)) {
    die("Current password is incorrect.");
}

$newHashed = password_hash($new, PASSWORD_DEFAULT);

$update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$update->bind_param("si", $newHashed, $userId);
if ($update->execute()) {
    echo "Password changed successfully. <a href='user.php'>Go back</a>";
} else {
    echo "Error updating password.";
}
$update->close();
$conn->close();
?>
