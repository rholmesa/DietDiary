<?php

require 'db_connect.php';
date_default_timezone_set("UTC"); //otherwise error reporting is trash!!!
$sql = "select `columnName`, `friendlyname`
				from `gdas`
				where `include` = 'y'
				order by `friendlyname`;";	

$result = $mysqli->query($sql) or trigger_error(mysql_error()." in ".$sql);

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

