<?php
header('Access-Control-Allow-Origin:*');
include("connection.php");
//implementing JWT
require __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\KEY;

$headers = getallheaders();
// print_r($headers);
// exit();

if(!isset($headers['Authorization']) || empty($headers['Authorization'])){
    http_response_code(401);
    echo json_encode(["error" => "unauthorized"]);
    exit();
}

$authorizationHeader = $headers['Authorization'];
$token = null;

$token = trim(str_replace("Bearer", '' , $authorizationHeader));
// print_r($token);
// exit();

if(!$token){
    http_response_code(401);
    echo json_encode(["error" => "unauthorized"]);
    exit();
}

try{

    $key = "secrt_key";
    $decoded = JWT::decode($token ,new Key($key,'HS256'));
    // print_r($decoded);
    // exit();
    if($decoded->user_type_id == 1){

        $user_id = $_POST['user_id'];
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];



        $query = $mysqli->prepare('insert into products(user_id,product_name,description,price,stock_quantity) values(?,?,?,?,?)');
        $query->bind_param('issss', $user_id, $product_name ,$description, $price, $stock_quantity);
        $query->execute();

        $user_id = $mysqli->insert_id;
        echo "$user_id";

        $response = [];
        $response["status"] = "true";

        echo json_encode($response);
    } else {
        $response = [];
        $response['permission']="false";
     }
    echo json_encode($response);


 } catch(ExpiredException $e){
    http_response_code(401);
    echo json_encode(["error" => "expired"]);
}