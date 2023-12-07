<?php
header('Access-Control-Allow-Origin:*');
include("connection.php");

$email = $_POST['email'];
$username = $_POST['username'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$password = $_POST['password'];
$address = $_POST['address'];
$phone_number = $_POST['phone_number'];
$user_type_id = $_POST['user_type_id'];

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$query = $mysqli->prepare('INSERT INTO users (user_type_id, username, password, email, last_name, first_name, phone_number, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$query->bind_param('isssssss', $user_type_id,$username,$hashed_password,$email,$last_name ,$first_name,$phone_number, $address);
$query->execute();

$user_id = $mysqli->insert_id;
echo "$user_id";

$response = [];
$response["status"] = "true";

echo json_encode($response);