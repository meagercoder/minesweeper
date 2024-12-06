<?php

// Validate the input
if (empty($_POST['username'])) {
    die("Username is required.");
}

if (empty($_POST['password'])) {
    die("Password is required.");
}

if (strlen($_POST['password']) < 6) {
    die("Password must be at least 6 characters.");
}

if ($_POST['password'] !== $_POST['confirm-password']) {
    die("Passwords do not match.");
}

// Hash the password
$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
$username = $_POST['username'];  // This is the value from the form

$mysqli = require __DIR__ . '/includes/dbh.inc.php';
$username = $_POST['username'];  // This is the value from the form
//echo "Username received: " . htmlspecialchars($username);
// exit;  // Uncomment this line to stop the script after debugging (useful for checking the output)

// Prepare the SQL query
$sql = "INSERT INTO users (username, password) VALUES (?, ?)";
$stmt = $mysqli->stmt_init();

// Check if the statement can be prepared
if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

// Bind parameters and execute the query
$stmt->bind_param('ss', $username, $password_hash);
if ($stmt->execute()) {
    // Redirect to a success page
    header("Location: signup-successful.html");
    exit;
} else {
    // Handle the error if the insertion fails
    if ($mysqli->errno === 1062) {
        die("Username already exists.");
    } else {
        die("Error: " . $mysqli->error . " (" . $mysqli->errno . ")");
    }
}
?>
