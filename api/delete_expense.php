<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];
$id = $_POST['id'];

mysqli_query($conn,
"DELETE FROM expenses WHERE id='$id' AND user_id='$user_id'");

echo "deleted";
?>