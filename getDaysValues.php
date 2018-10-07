<?php

require 'db_connect.php';
include 'commonHeader.php';
//sec_session_start();

// first get the list of entries for this date
$mydate = $mysqli->escape_string($_POST['mydate']);
$mycolumn = $mysqli->escape_string($_POST['mycolumn']);
$userid  = $_SESSION['user_id'];
$sql = "SELECT did as id, food_code as recipeFoodCode, amount as Amount FROM diary where entrydate = '".$mydate."' AND userid='".$userid."';";

$entries = $mysqli->query($sql) or die("Failed to get diary entries for ".mydate." (".$mysqli->error.")");

// now we have a list of the entries - get the calories per entry

$a_json = array();
$a_json_row = array();

if ($entries) {
    while ($row = $entries->fetch_assoc()) {
        $recipeid = $row['recipeFoodCode'];
        $sql1 = "select `recipe_name`, 
                        `recipe_names`.`food_code`, 
                        `recipe_portion_name`,
                        `qty`, `".$mycolumn."` as myvalue,
                        truncate(sum(`constituents`.`".$mycolumn."`*`qty`/`constituents`.`portion_qty`/`recipe_for_how_many`),2) as ppValues
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
            $a_json_row["Amount"] = $row['Amount'];
            $a_json_row["Portion Values"] = $data['myvalue'];
            $a_json_row["Values"] = $data['ppValues'];
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

