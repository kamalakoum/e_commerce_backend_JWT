<?php
header('Access-Controll-Allow-Origin:*');
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
    if($decoded->user_type_id == 2){

        $query=$mysqli->prepare('select product_name, description, price, stock_quantity from products');
        $query->execute();
        $array=$query->get_result();

        $response=[];
        while($product=$array->fetch_assoc()){
            $response[]=$product;
        }
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
?>