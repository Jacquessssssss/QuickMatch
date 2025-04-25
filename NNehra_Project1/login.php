<?php
session_start();
require 'db_connect.php';

// Handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        die("Please enter email and password.");
    }

    $query = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $query->store_result();

    if ($query->num_rows === 1) {
        $query->bind_result($userId, $userName, $realPassword);
        $query->fetch();

        if (password_verify($password, $realPassword)) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $userName;
            echo "Login successful! <a href='index.html'>Go to Homepage</a>";
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "Email not found.";
    }

    $query->close();
    $conn->close();
}
?>
