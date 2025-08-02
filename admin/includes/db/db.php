<?php
$dns = "mysql:host=localhost; dbname=news_system";
$username = "root";
$password = "";
$option =     array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
try{
  $connect = new PDO($dns, $username, $password,$option);
  $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
  echo "DBError".$e->getMessage();
}
?>