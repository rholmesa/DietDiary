<?php

require 'db_connect.php';
date_default_timezone_set('UTC');
$myid = $_POST["myid"];
$amnt = $_POST["amount"];
$sql = "UPDATE diary SET amount = ".$amnt." WHERE did=".$myid.";";
//echo $sql;
$result = $mysqli->query($sql) or die ($mysqli->error());
echo $amnt;
$mysqli->close();