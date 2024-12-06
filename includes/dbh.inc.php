<?php
$host = "localhost";
$dbname = "minesweeper";
$username = "root";
$password = "";

// Establish connection
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
return $mysqli;
