<?php
require 'db_connect.php';

// Add some test scores
$scores = [
    [1, 1, 45],
    [2, 1, 30],
    [3, 1, 60],
    [4, 2, 12],
    [1, 2, 15],
    [2, 2, 10],
    [3, 3, 8],
    [4, 3, 5],
    [5, 3, 7],
    [1, 3, 6]
];

foreach ($scores as $scoreRow) {
    $query = $conn->prepare("INSERT INTO leaderboards (user_id, game_id, score) VALUES (?, ?, ?)");
    $query->bind_param("iii", $scoreRow[0], $scoreRow[1], $scoreRow[2]);
    $query->execute();
    $query->close();
}

echo "<h2>Scores Inserted!</h2>";

// Show scores for each game
$games = [
    1 => 'Timed Game',
    2 => 'Attempts Game',
    3 => 'Mystery Game'
];

foreach ($games as $gameId => $gameName) {
    echo "<h3>$gameName</h3>";

    $sql = "SELECT u.username, u.email, l.score
            FROM leaderboards l
            JOIN users u ON l.user_id = u.id
            WHERE l.game_id = ?
            ORDER BY l.score DESC";

    $query = $conn->prepare($sql);
    $query->bind_param("i", $gameId);
    $query->execute();
    $results = $query->get_result();

    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Username</th><th>Email</th><th>Score</th></tr>";
    while ($row = $results->fetch_assoc()) {
        echo "<tr><td>{$row['username']}</td><td>{$row['email']}</td><td>{$row['score']}</td></tr>";
    }
    echo "</table>";

    $query->close();
}

$conn->close();
?>
