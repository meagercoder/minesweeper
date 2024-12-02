<?php

if (empty($_POST['name'])) {
    die("Name and password are required.");
}
if(empty($_POST['password'])) {
    die("Password is required.");
}
if(strlen($_POST['password']) < 8) {
    die("Password must be at least 8 characters.");
}
if ($_POST['password'] !== $_POST['confirm-password']) {
    die("Passwords do not match.");
}

$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
$name = $_POST['name'];

$mysqli = require __DIR__ . '/dbh.inc.php';

$sql = "INSERT INTO users (name, password_hash) VALUES (?, ?)";
$stmt = $mysqli->stmt_init();

if(!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
};
$stmt->bind_param('ss', $name, $password_hash);
if(!stmt->execute()) {
    header("Location: signup-successful.html");
    exit;
} else {
    if ($mysqli->errno === 1062) {
        die("Username already exists.");
    }else {
    die("Error: " . $mysqli->error. " " . $mysqli->errno);
    }
}