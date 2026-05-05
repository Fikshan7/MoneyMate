<?php
session_start();
include "db.php";

$email = trim($_POST['email']);
$password = trim($_POST['password']);

$sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){

    $row = mysqli_fetch_assoc($result);

    $_SESSION['user_id'] = $row['id'];
    $_SESSION['name'] = $row['name'];
    $_SESSION['email'] = $row['email'];

   if(strtolower($row['name']) == 'gj' || strtolower($row['name']) == 'tt'){
    echo "admin";
} else {
    echo "user";
}

}else{
    echo "invalid";
}
?>