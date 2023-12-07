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
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];

        $query = $mysqli->prepare('UPDATE products SET product_name=?, description=?, price=?, stock_quantity=? WHERE id=?');
        $query->bind_param('ssdii', $product_name, $description, $price, $stock_quantity ,$id);
        $query->execute();



        $response = ['status' => 'product updated successfully'];
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