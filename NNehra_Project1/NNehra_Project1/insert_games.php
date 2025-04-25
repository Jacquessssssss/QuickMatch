<?php
// Connect to the database
require 'db_connect.php';


// Insert 3 game modes

$allGames = [
    ['Timed Game', 16],
    ['Attempts Game', 16],
    ['Mystery Game', 8]
];

foreach ($allGames as $gameRow) {
    $query = $conn->prepare("INSERT INTO games (name, card_count) VALUES (?, ?)");
    $query->bind_param("si", $gameRow[0], $gameRow[1]);
    $query->execute();
    $query->close();
}

// Show all games from database

echo "<h2>Games Table:</h2>";

$gamesResult = $conn->query("SELECT * FROM games");

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Card Count</th></tr>";
while ($game = $gamesResult->fetch_assoc()) {
    echo "<tr><td>{$game['id']}</td><td>{$game['name']}</td><td>{$game['card_count']}</td></tr>";
}
echo "</table>";

// Close connection when done
$conn->close();
?>
