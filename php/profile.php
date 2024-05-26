<?php
include "./dbconnection/mongodb.php";
include "./dbconnection/redis.php";
include "./dbconnection/response.php";



if(!isset($_GET['redisID'])){
    $response=array("status"=>false,"message"=>"Redis ID Not Found");
}
$redisId = $_GET['redisID'];
$sessionDetails =json_decode($redis->get("session:$redisId"));
if(!$sessionDetails){
    // CACHE MISS
    $response= array(
        'status' => false,
        'message' => 'Invalid Session ID',
    );
    sendRespose(200,$response);

}

if(isset($_GET["action"]) && $_GET["action"]=="getUserDetails"){
    $response=array("status"=>true ,"userDetails" => $sessionDetails->userDetails);
    sendRespose(200,$response);
}else if(isset($_GET["action"]) && $_GET["action"]=="logout"){
    $redis->del("session:$redisId");
    $response = array(
        "status" => true,
        "message" => "Logout successful",
    );
    sendRespose(200,$response);    
}
