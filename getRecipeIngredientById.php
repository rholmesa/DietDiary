<?php

require 'db_connect.php';

$string = $_POST["recipeid"];

$sql1 = "select `Food_Name`,
                `Qty` as Quantity
	from `recipe_ingredients`, `constituents`
	where `constituents`.`food_code`=`recipe_ingredients`.`food_code`
	and `recipe_ingredients_id` = '".$string."';";

$sql1 = $mysqli->real_escape_string($sql1);

$result = $mysqli->query($sql1) or die($mysqli->error());
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