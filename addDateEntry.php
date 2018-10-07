<?php

require 'db_connect.php';
include 'functions.php';
sec_session_start();

$foodcode = $mysqli->real_escape_string($_GET['Food_Code']);
$foodname = $mysqli->real_escape_string($_GET['Food_Name']);
$currentDate = $mysqli->real_escape_string($_GET['Date']);
$amount = $mysqli->real_escape_string($_GET['Amount']);

$userid = $_SESSION['user_id'];
$useremail = $_SESSION['useremail'];

$sql = "INSERT INTO `diary` 
		(`Food_Code`, `Convenient_Food_Name`, `Amount`, `entrydate`, `userid`, `useremail`) 
		VALUES ('$foodcode', '$foodname', '$amount', '$currentDate', '$userid', '$useremail');";

$result = $mysqli->query($sql);
//echo $sql;
if ($result) {
	echo "pass - entry added for user (".$userid.") email ".$useremail;
	/* close the result object 
	$result->close();*/
} else {
	echo "fail : entry NOT added for user (".$userid.") email ".$useremail." ".$sql;
}
/* close connection */
$mysqli->close();