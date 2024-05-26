<?php
// REDIS
$redisLink='redis-15625.c62.us-east-1-4.ec2.redns.redis-cloud.com';
$redisPort=15625;
$redisAuth="zKju2p4JZ9GmvZsnNidxB9AYmJaYm6lC";

//Connecting to Redis server on localhost
$redis = new Redis();

// $redis->connect('127.0.0.1', 6379);
$redis->connect($redisLink, $redisPort);
$redis->auth($redisAuth);

ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://127.0.0.1:6379');


?>
