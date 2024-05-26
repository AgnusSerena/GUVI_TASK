<?php
include "./dbconnection/mysql.php";
include "./dbconnection/mongodb.php";
include "./dbconnection/redis.php";
include "./dbconnection/response.php";

function getUserDetails($mongodbId){
    global $mongoClient,$mongodbDatabase,$mongodbCollection;
    $_id=new MongoDB\BSON\ObjectID($mongodbId); 
    $filter = ['_id' => $_id];

    $options = [];

    $query = new MongoDB\Driver\Query($filter, $options);

    $cursor = $mongoClient->executeQuery("$mongodbDatabase.$mongodbCollection", $query);

    $document = current($cursor->toArray());

    return $document;
    // sendRespose(200,$document);
}

session_start();


$email = $_POST['input1'];
$password = $_POST['input2'];

// Sanitize input
$email = $conn->real_escape_string($email);
$password = $conn->real_escape_string($password);

// Fetch user from database
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user["userpswd"])) {
        $session_id = uniqid();
        $userDetails=getUserDetails($user["mongodbId"]);
        $data=array(
            "username"=>$user["email"],
            "mongoDB"=>$user["mongodbId"],
            "userDetails"=>$userDetails,
        );
        $redis->set("session:$session_id", json_encode($data) );
        $redis->expire("session:$session_id", 10 * 60);
        $response = array(
            'status' => true,
            "test"=> $data,
            'message' => 'Success',
            'session_id' => $session_id,
            'data' => array(
                'emailid' => $user['email'], 'password' => $user["userpswd"], 'mongoDbId' => $user["mongodbId"],
            ),
        );

        sendRespose(200, $response);
    } else {
        // Invalid password
        $response = array(
            'status' => false,
            'message' => 'invalid password'
        );
        sendRespose(409, $response);
    }
} else {
    // No user found
    $response = array(
        'status' => false,
        'message' => 'No user found'
    );
    sendRespose(409, $response);
}

// Close connection
$conn->close();
?>


