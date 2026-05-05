<?php
include "db.php";

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

$sql = "INSERT INTO users(name,email,password)
VALUES('$name','$email','$password')";

if(mysqli_query($conn,$sql)){
    echo "success";
}else{
    echo "error";
}
?>