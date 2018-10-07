<?php

require 'db_connect.php';

$recipeid = $mysqli->real_escape_string($_GET['recipeid']);

$sql = "select 	`recipe_name`, 
		`recipe_names`.`food_code`, 
		`recipe_portion_name` as foodportionname,
        truncate(sum(`constituents`.`energy_kcal_kcal`*`qty`/`constituents`.`portion_qty`/`recipe_for_how_many`),2) as ppCalories,
        truncate(sum(`constituents`.`carbohydrate_g`*`qty`/`constituents`.`portion_qty`/`recipe_for_how_many`),2) as ppCarbs
from `recipe_names`, `recipe_ingredients`, `constituents`
where `recipe_ingredients`.`recipe_food_code` = `recipe_names`.`food_code`
and `recipe_ingredients`.`food_code` = `constituents`.`food_code`
and `recipe_names`.`food_code` = '".$recipeid."';"; 

//$sql = $mysqli->real_escape_string($sql);

$result = $mysqli->query($sql) or die(mysqli_error($mysqli));
if ($result) {
    while ($data = $result->fetch_array()) {
            $outputdata[] = $data;
    }
    echo json_encode($outputdata);
    /* close the result object */
    $result->close();
} else {
    echo json_encode("No Entries");
}
/* close connection */
$mysqli->close();

