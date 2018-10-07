<?php

require 'db_connect.php';
require 'commonHeader.php';
//sec_session_start();

date_default_timezone_set('UTC');
$str = $mysqli->real_escape_string($_POST["myid"]);
$userid = $_SESSION['user_id'];
$sql = "SELECT food_code as `Food_Code`, `convenient_food_name` as Food, `amount` FROM `diary` WHERE `did`='".$str."' AND `userid`='".$userid."';";
$result = $mysqli->query($sql) or die ($mysqli->error());
if ($result) {
    while ($data = $result->fetch_assoc()) {
        $outputdata[] = $data;
    }
    $result->close();
} else {
    $outputdata[]="";
}
echo json_encode($outputdata);
$mysqli->close();