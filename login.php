<?php
session_start(); // Start the session at the top

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . '/includes/dbh.inc.php';

    // Capture and sanitize the username input
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Prepare the SQL query using a prepared statement
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $username); // "s" means string, binding the username value
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if the user exists
    if ($user) {
        // Verify the password
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"]; // Store user id in session
            header("Location: home.php");
            exit;
        } else {
            $is_invalid = true; // Password is incorrect
        }
    } else {
        $is_invalid = true; // User not found
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if ($is_invalid): ?>
        <em>Invalid username or password</em>
    <?php endif; ?>
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($_POST["username"] ?? "") ?>" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <button id="back-button" onclick="window.location.href='signup.html'">Signup</button>
</body>
</html>
