<?php
session_start();
include 'dbh.inc.php'; // Ensure this file connects to the database

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "error: User not logged in";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id']; // Get user ID from session
    $score = $_POST['score'];
    $timePlayed = $_POST['time_played'];
    $difficulty = $_POST['difficulty'];
    $won = $_POST['won']; // Whether the user won (1 for true, 0 for false)

    try {
        $stmt = $conn->prepare("INSERT INTO game_history (user_id, difficulty, time, wins) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isii", $userId, $difficulty, $timePlayed, $wins);

        // Set wins to 1 if the game was won, otherwise 0
        $wins = ($won == 1) ? 1 : 0;

        $stmt->execute();

        echo "success";
    } catch (Exception $e) {
        echo "error: " . $e->getMessage();
    }
} else {
    echo "error: Invalid request";
}
?>
