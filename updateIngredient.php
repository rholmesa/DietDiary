<?php

require 'db_connect.php';
require 'phpFunctions.php';  

$ingredientfoodcode = $mysqli->real_escape_string($_POST['ingredientfoodcode']);
$foodname = $mysqli->real_escape_string($_POST['foodname']);
$sql = "UPDATE `constituents` SET 
        `Energy_kcal_kcal`= '".$mysqli->real_escape_string($_POST['energy'])."', 
        `fat_g` = '".$mysqli->real_escape_string($_POST['fat'])."', 
        `Satd_FA_/100g_fd_g` = '".$mysqli->real_escape_string($_POST['satfats'])."', 
        `Carbohydrate_g` = '".$mysqli->real_escape_string($_POST['carbs'])."', 
        `Total_sugars_g` = '".$mysqli->real_escape_string($_POST['sugars'])."', 
        `AOAC_fibre_g` = '".$mysqli->real_escape_string($_POST['fibre'])."', 
        `protein_g` = '".$mysqli->real_escape_string($_POST['protein'])."', 
        `Sodium_mg` = '".$mysqli->real_escape_string($_POST['sodium'])."', 
        `Cholesterol_mg` = '".$mysqli->real_escape_string($_POST['Cholesterol'])."', 
        `alcohol_g` = '".$mysqli->real_escape_string($_POST['alcohol'])."'
        WHERE `constituents`.`food_code` = '".$ingredientfoodcode ."';";   
echo $sql;
$result = $mysqli->query($sql) or die("Failed to update recipe ".$ingredientfoodcode." in database. (error ".mysqli_error($mysqli).")");
//
// ok we assume that was a success otherwise the routine will have exoted via the 'die' statement above
//
// now create a recipe - but here - only if values are passed!!!!
//
if (!empty($_POST['portionname'])) {  // a portion name is specified
    $portionname = $mysqli->real_escape_string($_POST['portionname']);
    $portionsize = (empty($_POST['portionsize']) ? 100 : $mysqli->real_escape_string($_POST['portionsize']));

    /* $sql = "INSERT INTO `recipe_names` (`Food_Code`, `Recipe_Name`, `Recipe_Portion_Name`, `Recipe_For_How_Many`) VALUES ((select nextRecipeCode()), '"; */
    $sql = "INSERT INTO `recipe_names` (`Food_Code`, `Recipe_Name`, `Recipe_Portion_Name`, `Recipe_For_How_Many`) VALUES ($nextRecipeCode, '";
    $sql .= $foodname;
    $sql .= "', '";
    $sql .= $portionname;
    $sql .= "', '";
    $sql .= "1";
    $sql .= "');";
    echo $sql."<br /><br />";
    $result = $mysqli->query($sql) or die('There was an error inserting the recipe into recipe_names [' . $mysqli->error . ']');
    //
    // recipe created (or we died!) - we need to get the recipe_food_code AND the ingredient_food_code and relate them
    // in the recipe ingredients table
    //
    $sql = "SELECT food_code, recipeid 
            FROM recipe_names 
            WHERE recipe_name = '".$foodname."' limit 1;";
    echo $sql."<br /><br />";
    $recipecode = $mysqli->query($sql) or die('There was an error getting the recipe food_code and recipeid from recipe_names [' . $mysqli->error . ']');
    $row = $recipecode->fetch_row();

    $recipefoodcode = $row[0];
    $recipeid = $row[1];

    $sql = "SELECT constituentid from constituents WHERE Food_Code = '".$ingredientfoodcode."' limit 1;";
    echo $sql."<br /><br />";
    $ingredientcodes = $mysqli->query($sql) or die('There was an error getting the constituentid [' . $mysqli->error . ']');
    $row = $ingredientcodes->fetch_row();
    $constituentid = $row[0];

$sql = "INSERT INTO `recipe_ingredients` (`recipeid`, `recipe_food_code`, `Food_Code`, `Qty`, constituentid) VALUES ('";
    $sql .= $recipeid;
    $sql .= "', '";
    $sql .= $recipefoodcode;
    $sql .= "', '";
    $sql .= $ingredientfoodcode;
    $sql .= "', '";
    $sql .= $portionsize;
    $sql .= "', '";
    $sql .= $constituentid;
    $sql .= "');";
    echo $sql."<br /><br />";
    $result = $mysqli->query($sql) or die('There was an error inserting the entry into the recipe_ingredients table [' . $mysqli->error . ']');

}
/* close connection */
$mysqli->close();
