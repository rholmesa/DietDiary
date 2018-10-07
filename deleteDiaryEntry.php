<?php

require 'db_connect.php';

$myid = $_POST["myid"];

$sql = "DELETE FROM diary WHERE did=".$myid.";";
//echo $sql;
$result = $mysqli->query($sql) or die ($mysqli->error);
$mysqli->close();
