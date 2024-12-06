<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
</head>
<body>
    <?php if (isset($_SESSION['user_id'])) : ?>
        <!-- Links visible when the user is logged in -->
        <a href="game.html">Game</a>
        <a href="leaderboard.html">Leaderboard</a>
        <a href="logout.php">Logout</a>
    <?php else : ?>
        <!-- Links visible when the user is not logged in -->
        <a href="login.php">Login</a> or
        <a href="signup.html">Signup</a>
    <?php endif; ?>
</body>
</html>
