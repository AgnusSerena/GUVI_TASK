<?php
     $mongoHost = 'localhost';
     $mongoPort = 27017;
     $mongodbDatabase = 'GuviTask';
     $mongodbCollection = 'UserDetails';
     $mongoUsername = '';
     $mongoPassword = '';

     $mongoClient = new MongoDB\Driver\Manager("mongodb://$mongoHost:$mongoPort");
?>
