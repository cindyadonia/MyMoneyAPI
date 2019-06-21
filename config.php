<?php 

$server = "localhost";
$user= "root";
$password="";
$db_name ="mymoney_new";

$connect = mysqli_connect($server,$user,$password,$db_name);
if(!$connect)
{
	die("Connection failed! :" . mysqli_connect_error());
}

 ?>