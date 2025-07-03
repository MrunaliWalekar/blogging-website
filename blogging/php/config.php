<?php
$db_host="localhost:3309";
$db_user="root";
$db_password="";
$db_name="blogging";
//create connection
$conn = new mysqli($db_host,$db_user,$db_password,$db_name);

//check Connection
if(!$conn){
    die("connection problem");
}
else {
   print("connected");
}
?>