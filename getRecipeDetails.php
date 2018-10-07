<?php

require 'db_connect.php';
date_default_timezone_set('UTC');

$str = $_POST["recipeid"];

$sql1 = "select `RID`,
		`Recipe_For_How_Many` as How_Many, 
		`Recipe_Portion_Name` as Servings
	from `recipe_names`
	where `food_code` = '".$str."';";

$sql1 = $mysqli->real_escape_string($sql1);

$result = $mysqli->query($sql1) or die (mysqli_error($mysqli));
if ($result) {
    while ($data = $result->fetch_assoc()) {
            $outputdata[] = $data;
            echo json_encode($outputdata);
    }
    $result->close();
}
$mysqli->close();