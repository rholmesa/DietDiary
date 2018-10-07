<?php

require 'db_connect.php';

// first get the list of entries for this date
$mydate = $mysqli->escape_string($_POST['mydate']);
$sql = "SELECT did as id, food_code as recipeFoodCode, amount as Amount FROM diary where entrydate = '".$mydate."';";

$entries = $mysqli->query($sql) or die("Failed to get diary entries for ".mydate." (".$mysqli->error().")");

// now we have a list of the entries - get the calories per entry

$a_json = array();
$a_json_row = array();

if ($entries) {
    while ($row = $entries->fetch_assoc()) {
        $recipeid = $row['recipeFoodCode'];
        $sql1 = "select `recipe_name`, 
                        `recipe_names`.`food_code`, 
                        `recipe_portion_name`,
                        `qty`,
                        `energy_kcal_kcal`,
                        truncate(sum(`constituents`.`energy_kcal_kcal`*`qty`/`constituents`.`portion_qty`/`recipe_for_how_many`),2) as ppCalories
                from `recipe_names`, `recipe_ingredients`, `constituents`
                where `recipe_ingredients`.`recipe_food_code` = `recipe_names`.`food_code`
                and `recipe_ingredients`.`food_code` = `constituents`.`food_code`
                and `recipe_names`.`food_code` = '".$recipeid."';"; 
        $result = $mysqli->query($sql1) or die("Failed to get ppCalories for ".$recipeid." (".mysqli_errno($mysqli).")"); 
        if ($result) {
            $data = $result->fetch_assoc();
            $a_json_row["ID"] = $row['id'];
            $a_json_row["Food"] = $data['recipe_name'];
            $a_json_row["Portion"] = $data['recipe_portion_name'];
            $a_json_row["Amount"] = $data['qty'];
            $a_json_row["Portion Calories"] = $data['energy_kcal_kcal'];
            $a_json_row["Calories"] = $data['ppCalories'];
            array_push($a_json, $a_json_row); 
            $result->close();
        }
    }
    $json = json_encode($a_json);
    echo $json;
    $entries->close();
}

/* close connection */
$mysqli->close();


