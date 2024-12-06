<?php
session_start();

// Print the session ID and session data for debugging
echo "Session ID: " . session_id() . "<br>";

echo "<pre>";
var_dump($_SESSION);  // Debug the session
echo "</pre>";

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    echo "<br>User is logged in with user ID: " . $_SESSION['user_id'];
} else {
    echo "<br>User is not logged in.";
}
?>
