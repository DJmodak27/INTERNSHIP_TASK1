<?php
//host, dbname, user, pasword

try {
    $host = "localhost";
    $dbname = "pcm";
    $user = "root";
    $password = "";
    
    //dns 
    $conn = new PDO ("mysql:host=$host;dbname=$dbname",$user,$password);
    $conn ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 } catch(PDOException $e){
    echo $e->getMessage();
    //die("db error");
}
