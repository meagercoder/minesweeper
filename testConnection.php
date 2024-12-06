<?php
$mysqli = require __DIR__ . '/includes/dbh.inc.php';

if ($mysqli) {
    echo "Database connection successful!";
}
