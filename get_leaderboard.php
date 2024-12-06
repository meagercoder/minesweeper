<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit;
}

if (isset($_GET['difficulty'])) {
    $difficulty = $_GET['difficulty'];
} else {
    echo json_encode(["error" => "Difficulty parameter missing."]);
    exit;
}

// Check if the sort parameter is passed, default to 'wins'
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'wins';
$sortColumn = ($sort === 'time') ? 'MIN(gh.time_played)' : 'COUNT(*)';
$sortOrder = ($sort === 'time') ? 'ASC' : 'DESC';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=minesweeper', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch leaderboard data for the requested difficulty
    $stmt = $pdo->prepare("
        SELECT u.username, COUNT(*) AS wins, MIN(gh.time_played) AS best_time
        FROM game_history gh
        JOIN users u ON gh.user_id = u.id
        WHERE gh.difficulty = :difficulty
        GROUP BY u.username
        ORDER BY $sortColumn $sortOrder
        LIMIT 5
    ");
    $stmt->bindParam(':difficulty', $difficulty, PDO::PARAM_STR);
    $stmt->execute();
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data as JSON
    echo json_encode($leaderboard);

} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
