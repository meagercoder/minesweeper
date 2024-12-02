<?php
session_start();

// Database connection
require 'dbh.inc.php';

// Query to fetch top 10 scores
$sql = "SELECT username, score, time, difficulty FROM leaderboard ORDER BY score DESC, time ASC LIMIT 10";
$result = mysqli_query($conn, $sql);

// Check for errors
if (!$result) {
    echo json_encode(['error' => 'Database query failed']);
    exit;
}

// Fetch results
$leaderboard = [];
while ($row = mysqli_fetch_assoc($result)) {
    $leaderboard[] = [
        'username' => $row['username'],
        'score' => $row['score'],
        'time' => $row['time'],
        'difficulty' => $row['difficulty']
    ];
}

// Return results as JSON
echo json_encode($leaderboard);

// Close database connection
mysqli_close($conn);
?>
