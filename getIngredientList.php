<?php

require 'db_connect.php';
// contains utility functions mb_stripos_all() and apply_highlight()
require_once 'local_utils.php';

$term = $_GET['term'];

$sql = "SELECT Food_Code, 
            Food_Name, 
            truncate(Energy_kcal_kcal/portion_qty,2) as calories,
			truncate(Carbohydrate_g/portion_qty,2) as carbs,
            portion_name,
            portion_qty
        FROM constituents 
        ";			
$parts = explode(' ', $term);
$p = count($parts);
// for the first entry
$sql .= 'WHERE Food_Name LIKE ' ."'%" .$mysqli->real_escape_string($parts[0]) ."%'";
for($i = 1; $i < $p; $i++) {
  $sql .= ' AND Food_Name LIKE ' . "'%" . $mysqli->real_escape_string($parts[$i]) . "%'";
}
$sql .= ' ORDER BY Food_Name';

$result = $mysqli->query($sql);
$match ="";
$a_json = array();
$a_json_row = array();
 
$a_json_invalid = array(array("id" => "#", "value" => "", "label" => "Only letters and digits are permitted..."));
$json_invalid = json_encode($a_json_invalid);

if ($result) {

	while($data = $result->fetch_assoc()) {
		$a_json_row["id"] = $data['Food_Code'];
		$a_json_row["value"] = $data['Food_Name'];
		$a_json_row["label"] = $data['Food_Name'] . " - ".$data['portion_name']." - " . $data['calories'] .  "cals";
				$a_json_row["calories"] = $data['calories'];
				$a_json_row["carbs"] = $data['carbs'];
                $a_json_row["portionName"] = $data['portion_name'];
                $a_json_row["portionqty"] = $data['portion_qty'];
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

