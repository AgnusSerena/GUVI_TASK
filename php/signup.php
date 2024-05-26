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

function CreateUser($email, $password, $insertStmt, $data) {
    global $mongoClient,$mongodbCollection,$mongodbDatabase, $redis;
    $bulk = new MongoDB\Driver\BulkWrite;
    $_id = $bulk->insert($data);

    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);

    $result = $mongoClient->executeBulkWrite($mongodbDatabase . '.' . $mongodbCollection, $bulk, $writeConcern);
    $mongoDbId = (string)$_id;          
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    mysqli_stmt_bind_param($insertStmt, "sss", $email, $hashedPassword, $mongoDbId);
    if (!mysqli_stmt_execute($insertStmt)) {
        die("Execution failed: " . mysqli_stmt_error($insertStmt));
    } else {
        $session_id = uniqid();
        $userDetails=getUserDetails($mongoDbId);
        $data=array(
            "userDetails"=>$userDetails,
        );
        $redis->set("session:$session_id", json_encode($data) );
        $redis->expire("session:$session_id", 10 * 60);
        // RESPONSE
        $response = array(
            'status' => true,
            'message' => 'Success',
            'session_id' => $session_id,
            'data' => array(
                'emailid' => $email, 'password' => $password, 'mongoDbId' => $mongoDbId,
            ),
        );
        sendRespose(200,$response);
    }
}
function UpdateUser($email,$data,$redisId){
    global $redis;
  
    $sessionDetails =json_decode($redis->get("session:$redisId"));
    global $mongoClient,$mongodbDatabase,$mongodbCollection;
    $bulk = new MongoDB\Driver\BulkWrite;
    $filter = ['email' => $email];

    // Construct the update operation using $set and upsert option
    $update = ['$set' => [
        'Name' => $data['Name'], 
        'Age' => $data['Age'],
        'PhoneNumber' => $data['PhoneNumber']
    ]];
    $options = ['upsert' => true];

    // Add the update operation to the BulkWrite object
    $bulk->update($filter, $update, $options);
    $result = $mongoClient->executeBulkWrite($mongodbDatabase . '.' . $mongodbCollection, $bulk);
    $response=array();
  
    if ($result->getModifiedCount() > 0 || $result->getUpsertedCount() > 0) {
      $response['status'] = true;
      $response['message'] = "Updated";
      $newData=getUserDetails($sessionDetails->userDetails->_id->{'$oid'});
      $sessionDetails->userDetails = $newData;
      $redis->set("session:$redisId", json_encode($sessionDetails));
      $redis->expire("session:$redisId", 10 * 60);
    } else {
      $response['status'] = false;
      $response['message'] = "Not Updated";
    }
  
    sendRespose(200,$response);
}

if(isset($_POST['input2'] , $_POST['input3'])){

    $password = $_POST['input2'];
    $email = $_POST['input3'];

    // Check if email already exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // Bind email parameter
    $stmt->execute();
    $result = $stmt->get_result();

   
    if ($result-> num_rows>0) {
        $response = array(
            'status' => false,
        );
        sendRespose(409,$response);
    } 
    else {
        $insertSql = "INSERT INTO users (email, userpswd, mongodbId) VALUES (?, ?, ?)";
        $insertStmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($insertStmt, $insertSql)) {
            die("SQL error: " . mysqli_stmt_error($insertStmt));
        }
        $data= array("email" => $email); 

        CreateUser($email, $password, $insertStmt, $data);
    }

    $stmt->close();
    $conn->close();
}  
if(isset($_POST["action"]) && $_POST["action"]=="update"){
    // DATA FROM POST
    $data = $_POST['profiledata'];
    $email = $_POST['emailid'];
    $redisId=$_POST['redisID'];
    UpdateUser($email,$data,$redisId);
}


?>