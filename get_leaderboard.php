<?php
// Include database connection
include_once 'includes/dbh.inc.php';

// Set headers to return JSON
header('Content-Type: application/json');

try {
    // Connect to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // SQL query to fetch top 10 leaderboard entries
    $sql = "
        SELECT 
            users.username, 
            game_history.score, 
            game_history.time_played, 
            game_history.difficulty 
        FROM 
            game_history 
        INNER JOIN 
            users 
        ON 
            game_history.user_id = users.id 
        ORDER BY 
            game_history.score DESC, game_history.time_played ASC 
        LIMIT 10;
    ";

    // Execute the query
    $result = $conn->query($sql);

    // Check for errors
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    // Fetch the data as an associative array
    $leaderboard = [];
    while ($row = $result->fetch_assoc()) {
        $leaderboard[] = [
            'username' => htmlspecialchars($row['username']),
            'score' => (int)$row['score'],
            'time' => (int)$row['time_played'],
            'difficulty' => htmlspecialchars($row['difficulty']),
        ];
    }

    // Output the JSON-encoded data
    echo json_encode($leaderboard);

} catch (Exception $e) {
    // Handle errors and send an error response
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Close the connection
    $conn->close();
}
