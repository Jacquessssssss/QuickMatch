<?php
$host = 'localhost';
$db   = 'quickmatch';
$user = 'root';
$pass = ''; // default XAMPP MySQL password is blank

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
