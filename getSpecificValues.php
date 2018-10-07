<?php

require 'db_connect.php';
include 'commonHeader.php';

$string = $mysqli->real_escape_string($_POST["mydate"]);
$column = $mysqli->real_escape_string($_POST["mycolumn"]);
date_default_timezone_set('UTC');

//$sql1 = "call getDaysSpecificValue('".$string."','".$column."');";

$sql1 = "SELECT  SUM(`diary`.`amount`*`";
$sql1 .= $column;
$sql1 .= "`) AS Cals, `gdavalfemale` AS gda, `friendlyname`";
$sql1 .= "FROM `diary`, `constituents`, `gdas`";
$sql1 .= "WHERE `diary`.`food_code` = `constituents`.`food_code`";
$sql1 .= "AND `entrydate` = '";
$sql1 .= $string;
$sql1 .= "' ";
$sql1 .= "AND `gdas`.`columnName` = '";
$sql1 .= $column;
$sql1 .= "' ";
$sql1 .= "AND `gdas`.`include` = 'y';"; 

$result = $mysqli->query($sql1) or die(mysqli_error($mysqli));

if ($result) {
	while ($data = $result->fetch_array()) {
		$outputdata[] = $data;
	}
	echo json_encode($outputdata);
	/* close the result object */
	$result->close();
} else {
	echo json_encode("");
}
/* close connection */
$mysqli->close();