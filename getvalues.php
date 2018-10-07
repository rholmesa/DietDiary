<?php

require 'db_connect.php';

$string = $_POST["mydate"];

if ($string == "today") {
	$string = date('Y-m-d');		// if today is typed the use todays date
};

$sql="";
$sql = "SELECT getDaysCalories('" .$string ."') as Calories;";

$result = $mysqli->query($sql);
$val = "";
if ($result) {
    while ($obj = $result->fetch_assoc()) {
        $val = $obj['Calories'];
        echo ($val == null ? "No entries for this date." : $val ." calories on this date.");
//			echo $val;
    }
} 
$result->close();
/* close connection */
$mysqli->close();