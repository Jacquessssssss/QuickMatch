<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['firstname'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';
    $repeatPass = $_POST['repeat-password'] ?? '';

    if (empty($name) || empty($email) || empty($pass) || empty($repeatPass)) {
        die("Please fill in all fields.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if ($pass !== $repeatPass) {
        die("Passwords do not match.");
    }

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("Email is already registered.");
    }

    $check->close();

    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    $signup = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $signup->bind_param("sss", $name, $email, $hashed);

    if ($signup->execute()) {
        echo "Signup successful. <a href='login.html'>Login here</a>";
    } else {
        echo "Signup failed: " . $signup->error;
    }

    $signup->close();
    $conn->close();
}
?>
