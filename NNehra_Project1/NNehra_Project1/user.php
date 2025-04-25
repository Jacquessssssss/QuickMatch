<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Please log in first.");
}

$userId = $_SESSION['user_id'];

// Get user info
$userQuery = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$userQuery->bind_param("i", $userId);
$userQuery->execute();
$userQuery->bind_result($username, $email);
$userQuery->fetch();
$userQuery->close();

// Get wins per game
$wins = [];

$games = [
    1 => 'Timed Game',
    2 => 'Attempts Game',
    3 => 'Mystery Game'
];

foreach ($games as $gameId => $gameName) {
    $countQuery = $conn->prepare("
        SELECT COUNT(*) FROM leaderboards 
        WHERE user_id = ? AND game_id = ?
    ");
    $countQuery->bind_param("ii", $userId, $gameId);
    $countQuery->execute();
    $countQuery->bind_result($wins[$gameId]);
    $countQuery->fetch();
    $countQuery->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Profile</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <!-- CSS -->
    <link rel="stylesheet" href="styles.css">

    <!-- Header/Footer Loader -->
    <script defer src="script.js"></script>
</head>
<body class="body">

    <!-- Dynamic Header -->
    <nav>
        <div id="header"></div>
    </nav>

    <main style="padding: 20px;">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>

        <h2>Your Wins</h2>
        <ul>
            <?php foreach ($games as $id => $label): ?>
                <li><?php echo $label; ?>: <?php echo $wins[$id]; ?> win(s)</li>
            <?php endforeach; ?>
        </ul>
        <h2 style = "margin-top: 20px;"> Change password ? </h2>
        <div style = "display: flex;
                      justify-content: center;
                      align-items: center;  
                      margin-top: 10px;    
                      ">

        <form method="POST" action="change_password.php">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Change Password</button>
        </form>
        </div>
    </main>

    <!-- Dynamic Footer -->
    <div id="footer"></div>

    <style>
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        form input[type="password"],
        form button[type="submit"] {
            width: 250px;
            margin-bottom: 15px;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        form button[type="submit"] {
            background-color: #8e7cfb;
            color: white;
            border: none;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
        }

        form button[type="submit"]:hover {
            background-color: #7a6ae0;
        }
    </style>
</body>
</html>
