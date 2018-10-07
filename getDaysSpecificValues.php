<?php
require 'db_connect.php';
include 'commonHeader.php';
// first get the list of entries for this date
$mydate = $mysqli->escape_string($_POST['mydate']);
$mycolumn = $mysqli->escape_string($_POST['mycolumn']);

// get the gdas and a friendly name for the desired info
$sql = "SELECT `targetvals` AS gda, `friendlyname` FROM gdas WHERE `gdas`.`columnName` = '".$mycolumn."' ";
$standardsrow = $mysqli->query($sql) or die("Failed to get standards from gdas (".$mysqli->error().")");
$standards = $standardsrow->fetch_assoc();
$gda = $standards['gda'];
$friendlyname = $standards['friendlyname'];
$userid = $_SESSION['user_id'];

$totalval = 0.00;

// now get each of the recipe entries for this day
$sql = "SELECT food_code as recipeFoodCode, amount as Amount FROM diary where entrydate = '".$mydate."' AND userid='".$userid."';";
$entryrow = $mysqli->query($sql) or die("Failed to get diary entries for ".mydate." (".$mysqli->error().")");

if ($entryrow) { // for each of the recipes logged for today
    while ($entries = $entryrow->fetch_assoc()) {
        $amount = $entries['Amount'];
        $recipeid = $entries['recipeFoodCode'];

        $sql1 = "select truncate(sum(`constituents`.`".$mycolumn."`*`qty`/`constituents`.`portion_qty`/`recipe_for_how_many`),2) as ppValues
            from `recipe_names`, `recipe_ingredients`, `constituents`
            where `recipe_ingredients`.`recipe_food_code` = `recipe_names`.`food_code`
            and `recipe_ingredients`.`food_code` = `constituents`.`food_code`
            and `recipe_names`.`food_code` = '".$recipeid."';";
        $result = $mysqli->query($sql1) or die("Failed to get values for ".$recipeid." (".mysqli_errno($mysqli).")"); 
        $data = $result->fetch_assoc();
        $ppVals = $data['ppValues'];
        $totalval += $ppVals * $amount;
        $result->close();
    }
    $a_json = array();
    $a_json_row = array();

    $a_json_row["Value"] = number_format((float)$totalval, 2, '.', '');
    $a_json_row["gda"] = $gda;
    $a_json_row["friendlyname"] = $friendlyname;
    array_push($a_json, $a_json_row); 
    $entryrow->close();
}
$json = json_encode($a_json);
echo $json;

$standardsrow->close();
/* close connection */
$mysqli->close();
