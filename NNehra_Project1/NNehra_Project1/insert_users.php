<?php
require 'db_connect.php';

// Add 4 test users
$users = [
    ['alice', 'alice@example.com', password_hash('alice123', PASSWORD_DEFAULT)],
    ['bob', 'bob@example.com', password_hash('bob123', PASSWORD_DEFAULT)],
    ['charlie', 'charlie@example.com', password_hash('charlie123', PASSWORD_DEFAULT)],
    ['diana', 'diana@example.com', password_hash('diana123', PASSWORD_DEFAULT)],
    ['eve', 'eve@example.com', password_hash('eve123', PASSWORD_DEFAULT)],
];

foreach ($users as $person) {
    $name = $person[0];
    $email = $person[1];
    $hashedPass = password_hash($person[2], PASSWORD_DEFAULT);

    $query = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $query->bind_param("sss", $name, $email, $hashedPass);
    $query->execute();
    $query->close();
}

echo "<h2>Inserted 5 users successfully!</h2>";

// Show all users
$allUsers = $conn->query("SELECT id, username, email FROM users");
echo "<h3>Current Users:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th></tr>";
while ($user = $allUsers->fetch_assoc()) {
    echo "<tr><td>{$user['id']}</td><td>{$user['username']}</td><td>{$user['email']}</td></tr>";
}
echo "</table>";

$conn->close();
?>
