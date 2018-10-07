<?php

require 'db_connect.php';

$sql = $mysqli->real_escape_string($_POST["sqlstr"]);

$result = $mysqli->query($sql);

if ($result) {
    if ($result->num_rows !== 0) {
        while ($data = $result->fetch_array()) {
                $outputdata[] = $data;
        }
        echo json_encode($outputdata);
        /* close the result object */
        $result->close();
} else {
    echo json_encode("FAILED");
}
}
/* close connection */
$mysqli->close();