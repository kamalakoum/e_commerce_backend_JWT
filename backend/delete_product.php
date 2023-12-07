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
        $id = $_POST['id'];
        $query = $mysqli->prepare('DELETE FROM products WHERE id=?');
        $query->bind_param('i', $id);
        $query->execute();

        $response = ['status' => 'products deleted successfully'];
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