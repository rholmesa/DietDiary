<?php

require 'db_connect.php';
// contains utility functions mb_stripos_all() and apply_highlight()
require_once 'local_utils.php';

$term = $_GET['term'];

$sql1 = "select `Recipe_Name` AS `Recipe_Name`,
        `Food_Code` AS `Food_Code`,
        `Recipe_Portion_Name` AS `portion_name`,
            truncate(sum(ppValue),2) as calories, TRUNCATE(SUM(ppCarbs),2) AS carbs from
                (
                select 	`recipe_name`, 
                    `recipe_names`.`food_code`,
                    `recipe_names`.`recipe_portion_name`,
                    (`recipe_ingredients`.`qty`/`recipe_for_how_many`/`portion_qty`*`energy_kcal_kcal`) as ppValue,
                    (`recipe_ingredients`.`qty`/`recipe_for_how_many`/`portion_qty`*`carbohydrate_g`) AS ppCarbs
                from `recipe_ingredients`, `recipe_names`, `constituents`
                where `recipe_ingredients`.`recipeid` = `recipe_names`.`recipeid`
                and `constituents`.`food_code` = `recipe_ingredients`.`food_code` ";
$parts = explode(' ', $term);
$p = count($parts);
for($i = 0; $i < $p; $i++) {
  $sql1 .= ' AND `recipe_names`.`Recipe_Name` LIKE ' . "'%" . $mysqli->real_escape_string($parts[$i]) . "%'";
}
$sql1 .= ") t1
        group by `recipe_name`;";

$result = $mysqli->query($sql1);
$match ="";
$a_json = array();
$a_json_row = array();
 
$a_json_invalid = array(array("id" => "#", "value" => "", "label" => "Only letters and digits are permitted..."));
$json_invalid = json_encode($a_json_invalid);

if ($result) {

	while($data = $result->fetch_assoc()) {
		$a_json_row["id"] = $data['Food_Code'];
		$a_json_row["value"] = $data['Recipe_Name'];
		$a_json_row["label"] = $data['Recipe_Name'] . " - ".$data['portion_name']." - " . $data['calories'] .  "cals"." - " . $data['carbs'] .  "carb";
		array_push($a_json, $a_json_row);
	} 
	// highlight search results
	$a_json = apply_highlight($a_json, $parts);	 
	$json = json_encode($a_json);
	echo $json;	
	$result->close();
} 
/* close connection */
$mysqli->close();

