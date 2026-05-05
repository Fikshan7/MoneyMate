<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn,
"SELECT * FROM expenses WHERE user_id='$user_id' ORDER BY id DESC");

$data = array();

while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;
}

echo json_encode($data);
?>