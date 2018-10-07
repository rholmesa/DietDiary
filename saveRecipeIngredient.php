<?php
require 'db_connect.php';

$foodcode = isset($_GET['Food_Code']) ? $_GET['Food_Code'] : "";
$foodname = isset($_GET['Food_Name']) ? $_GET['Food_Name'] : "";
$currentDate = isset($_GET['Date']) ? $_GET['Date'] : "";
$amount = isset($_GET['Amount']) ? $_GET['Amount'] : "";

$sql = "INSERT INTO diary 
		(Food_Code, Convenient_Food_Name, Amount, entrydate) 
		VALUES ('$foodcode', '$foodname', '$amount', '$currentDate');";

//$result = $mysqli->query($sql);
//echo $sql;
if ($result) {
	echo "pass";
	/* close the result object */
	$result->close();
} else {
	echo "fail : " + $sql;
}
/* close connection */
$mysqli->close();