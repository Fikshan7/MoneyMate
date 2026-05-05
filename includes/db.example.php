<?php

// Copy this file to db.php and fill in your credentials
// NEVER commit db.php to version control

$conn = mysqli_connect(
    "your_db_host",       // e.g. localhost or sql100.infinityfree.com
    "your_db_username",   // e.g. if0_xxxxxxx
    "your_db_password",   // your database password
    "your_db_name"        // e.g. if0_xxxxxxx_moneymate
);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

?>
