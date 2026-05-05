<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

$date = $_POST['date'];
$category = $_POST['category'];
$amount = $_POST['amount'];

$sql = "INSERT INTO expenses(user_id,date,category,amount)
VALUES('$user_id','$date','$category','$amount')";

if(mysqli_query($conn,$sql)){
    echo "success";
}else{
    echo "error";
}
?>