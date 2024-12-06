<?php
$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . '/includes/dbh.inc.php';

    // Capture and sanitize the username input
    $username = $mysqli->real_escape_string($_POST["username"]);

    // Prepare the SQL query
    $sql = "SELECT * FROM users WHERE username = '$username'";
    
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    if ($user) {
        // Trim any spaces or characters in case of unwanted spaces
        $entered_password = trim($_POST["password"]);
        $stored_hash = trim($user["password"]);

        // Verify password (after trimming)
        if (password_verify($entered_password, $stored_hash)) {
            session_start();
            $_SESSION["user_id"] = $user["id"];
            header("Location: home.php");
            exit;
        } else {
            echo "Password does not match.";  // For debugging, remove or comment out later
            $is_invalid = true;
        }
    } else {
        echo "User not found.";
        $is_invalid = true;
    }
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
        <em>Invalid login</em>
    <?php endif; ?>
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($_POST["username"] ?? "") ?>">
        <label for="password">Password:</label>
        <input type="password" name="password">
        <button type="submit">Login</button>
    </form>
    <button id="back-button" onclick="window.location.href='signup.html'">Signup</button>
</body>
</html>
