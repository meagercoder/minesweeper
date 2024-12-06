<?php
// Start the session (simulating that the user is logged in)
session_start();

rprintf("User ID: %s", $_SESSION['user_id']);
