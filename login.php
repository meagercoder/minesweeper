<?php

$is_invalid = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mysqli = require __DIR__ . '/dbh.inc.php';
    $sql = sprintf("SELECT * FROM users WHERE name = ''", $mysqli->real_escape_string($_POST['username']));
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    if($user) {
        if(password_verify($_POST['password'], $user['password_hash'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header("Location: home.html");
            exit;  
        }
    }
    $is_invalid = true;
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
    <form methoc="post">
        <label>Username:</label>
        <input type="text" name="username" id="username" required value="<?= htmlspecialchars($_POST['username'] ?? '' )?>">
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>