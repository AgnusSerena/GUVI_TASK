<?php
function sendRespose($statuscode,$response){
    header('Content-Type: application/json');
    http_response_code($statuscode);
    $json_response = json_encode($response);
    
    die($json_response);
}
?>