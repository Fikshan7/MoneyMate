<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

mysqli_query($conn,
"DELETE FROM expenses WHERE user_id='$user_id'");

echo "done";
?>