<?php
require 'db_connect.php';

$game_id = isset($_GET['game_id']) ? intval($_GET['game_id']) : 1;

// query 
$sql = "SELECT u.username, u.email, l.score
        FROM leaderboards l
        JOIN users u ON l.user_id = u.id
        WHERE l.game_id = ?
        ORDER BY l.score ASC
        LIMIT 10";


// this section of the code is inspired by chatgpt
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $game_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
