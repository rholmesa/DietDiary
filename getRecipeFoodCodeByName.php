<?php
require 'db_connect.php';

$sql = "Select `food_code`, `recipeid` from `recipe_names` where `recipe_name` = '".$mysqli->real_escape_string($_POST["recipename"])."';";

$result = $mysqli->query($sql) or die('There was an error getting the food code from recipe [' . $mysqli->error . ']' + "SQL is " + $sql);

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
