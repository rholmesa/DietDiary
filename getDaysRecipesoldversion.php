<?php

require 'db_connect.php';

$string = $_POST["mydate"];
if ($string == "today") {
    $string = date('Y-m-d');		// if today is typed the use todays date
}
$sql1 = "select `did` as ID,
		`recipe_name` as Food, 
		`recipe_portion_name` as Portion, 
		`amount` as Amount, 
		`Energy_kcal_kcal` as 'Portion Calories', 
		TRUNCATE((`amount`*`Energy_kcal_kcal`),2) as Calories
    from `recipe_names`, `diary`, `constituents`
    where `entrydate` = '" .$string ."' 
    and `diary`.`food_code` = `recipe_names`.`food_code`
    and `diary`.`food_code` = `constituents`.`food_code`
;";

$result = $mysqli->query($sql1) or die($mysqli->error());
if ($result && $result->num_rows !== 0) {
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
