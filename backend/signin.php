<?php
header('Access-Controll-Allow-Origin:*');
include("connection.php");

//implementing JWT
require __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;

$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];
$query=$mysqli->prepare('select id,user_type_id , email,username ,password from users where email=?');
$query->bind_param('s',$email);
$query->execute();
$query->store_result();
$num_rows=$query->num_rows;
$query->bind_result($id,$user_type_id,$email,$username,$hashed_password);
$query->fetch();


$response=[];
if($num_rows== 0){
    $response['status']= 'user not found';
    echo json_encode($response);
} else {
    if(password_verify($password,$hashed_password)){
        $key = "secrt_key";
        $payload_array =[]; 
        $payload_array["user_id"] = $id;
        $payload_array["name"] = $username;
        $payload_array["user_type_id"] = $user_type_id;
        $payload = $payload_array;

        $response['status']= 'logged in';
        

        $jwt = JWT::encode($payload,$key,'HS256');
        $response['jwt'] = $jwt;


        echo json_encode($response);
    } else {
        $response['status']= 'wrong credentials';
        echo json_encode($response);
    }
};