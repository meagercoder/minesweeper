<?php
// Start the session
session_start();

// Enable error reporting to help diagnose issues (for debugging only, remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if session data is available (e.g., user is logged in)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

// Retrieve the session data (e.g., user ID)
$user_id = $_SESSION['user_id'];

// Check if required POST data exists
if (isset($_POST['won'], $_POST['time_played'], $_POST['difficulty'], $_POST['moves'])) {
    $won = $_POST['won'];
    $time_played = $_POST['time_played'];
    $difficulty = $_POST['difficulty'];
    $moves = $_POST['moves'];

    // Sanitize and validate inputs (though PDO already handles this for SQL)
    if (!is_numeric($time_played) || !is_numeric($moves) || !in_array($difficulty, ['easy', 'medium', 'difficult'])) {
        echo json_encode(["status" => "error", "message" => "Invalid input data."]);
        exit;
    }

    try {
        // Establish the database connection
        $pdo = new PDO('mysql:host=localhost;dbname=minesweeper', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL statement to insert the game history
        $stmt = $pdo->prepare("INSERT INTO game_history (user_id, won, time_played, difficulty, moves) 
                               VALUES (?, ?, ?, ?, ?)");

        // Execute the statement with the session user ID and other game data
        $stmt->execute([$user_id, $won, $time_played, $difficulty, $moves]);

        // Return a success response
        echo json_encode(["status" => "success", "message" => "Game history saved successfully!"]);
    } catch (PDOException $e) {
        // Log the error to a file for debugging purposes
        error_log($e->getMessage(), 3, '/var/log/php_errors.log');

        // Return a generic error message
        echo json_encode(["status" => "error", "message" => "Database error, please try again later."]);
    }
} else {
    // Return an error if required POST data is missing
    echo json_encode(["status" => "error", "message" => "Error: Missing required POST data."]);
}
?>
