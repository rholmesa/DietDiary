<?php

require 'db_connect.php';

$sql = $_POST["sqlstr"];

$result = $mysqli->query($sql) or die(mysqli_error($mysqli));

if ($result) {
    if ($result->num_rows !== 0) {
        while ($data = $result->fetch_array()) {
                $outputdata[] = $data;
        }
        echo json_encode($outputdata);
        /* close the result object */
        $result->close();
} else {
    echo json_encode("");
}
}
/* close connection */
$mysqli->close();

