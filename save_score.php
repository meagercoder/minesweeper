<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "error: not logged in";
    exit;
}

// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the database connection
    include 'dbh.inc.php'; // Ensure $conn is properly set up in this file

    // Retrieve and sanitize input
    $user_id = intval($_SESSION['user_id']); // Get the user ID from the session
    $score = isset($_POST['score']) ? intval($_POST['score']) : 0; // Ensure score is an integer
    $time_played = isset($_POST['time_played']) ? intval($_POST['time_played']) : 0; // Time in seconds
    $difficulty = isset($_POST['difficulty']) ? $_POST['difficulty'] : 'unknown'; // Default to 'unknown'

    // Validate difficulty input
    $valid_difficulties = ['easy', 'medium', 'difficult'];
    if (!in_array($difficulty, $valid_difficulties)) {
        echo "error: invalid difficulty";
        exit;
    }

    // Prepare SQL statement to insert the score, time, and difficulty
    $stmt = $conn->prepare("INSERT INTO leaderboard (user_id, score, time_played, difficulty) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        echo "error: failed to prepare statement";
        exit;
    }

    $stmt->bind_param("iiis", $user_id, $score, $time_played, $difficulty);

    // Execute the query
    if ($stmt->execute()) {
        echo "success"; // Indicate that the score was saved successfully
    } else {
        echo "error: could not save score"; // Handle execution errors
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "error: invalid request method"; // Ensure only POST requests are accepted
}
?>
