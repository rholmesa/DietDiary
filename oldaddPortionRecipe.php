<?php

require 'db_connect.php';

// ok - this routine enters a new Ingredient into the ingredient table
// and then ensures a 'portion' value is available for use within foods selection
//
// first get the info and insert into ingredients table (called constituents)
$foodname =  $mysqli->real_escape_string($_POST['food_name']); 
$energy = (empty($_POST['energy']) ? 0.0 : $mysqli->real_escape_string($_POST['energy']));
$fat = (empty($_POST['fat']) ? 0.0 : $mysqli->real_escape_string($_POST['fat']));
$satfats = (empty($_POST['satfats']) ? 0.0 : $mysqli->real_escape_string($_POST['satfats']));
$carbs = (empty($_POST['carbs']) ? 0.0 : $mysqli->real_escape_string($_POST['carbs']));
$sugars = (empty($_POST['sugars']) ? 0.0 : $mysqli->real_escape_string($_POST['sugars']));
$fibre = (empty($_POST['fibre']) ? 0.0 : $mysqli->real_escape_string($_POST['fibre']));
$protein = (empty($_POST['protein']) ? 0.0 : $mysqli->real_escape_string($_POST['protein']));
$sodium = (empty($_POST['sodium']) ? 0.0 : $mysqli->real_escape_string($_POST['sodium']));
$Cholesterol = (empty($_POST['Cholesterol']) ? 0.0 : $mysqli->real_escape_string($_POST['Cholesterol']));
$alcohol = (empty($_POST['alcohol']) ? 0.0 : $mysqli->real_escape_string($_POST['alcohol']));

$sql = "INSERT INTO `constituents` (`Food_Code`, `Food_Name`, `portion_qty`, `portion_name`, `Energy_kcal_kcal`, `fat_g`, `Satd_FA_/100g_fd_g`,"; 
$sql .= "`Carbohydrate_g`, `Total_sugars_g`, `AOAC_fibre_g`, `protein_g`, `Sodium_mg`,  `Cholesterol_mg`, `alcohol_g`) ";
$sql .= "Values ((select nextFoodCode()),";
$sql .= "'".$foodname."', ";
$sql .= "'100', 'grams'";
$sql .= ", ".$energy;
$sql .= ", ".$fat;
$sql .= ", ".$satfats;
$sql .= ", ".$carbs;
$sql .= ", ".$sugars;
$sql .= ", ".$fibre;
$sql .= ", ".$protein;
$sql .= ", ".$sodium;
$sql .= ", ".$Cholesterol;
$sql .= ", ".$alcohol;
$sql .= ");";
echo $sql."<br /><br />";
$result = $mysqli->query($sql) or die('There was an error inserting the ingredient into constituents [' . $mysqli->error . ']');
//
// ok we assume that was a success otherwise the routine will have exoted via the 'die' statement above
//
// now create a recipe
//
$portionname = (empty($_POST['portionname']) ? "grams" : $mysqli->real_escape_string($_POST['portionname']));
$portionsize = (empty($_POST['portionsize']) ? "100" : $mysqli->real_escape_string($_POST['portionsize']));

$sql = "INSERT INTO `recipe_names` (`Food_Code`, `Recipe_Name`, `Recipe_Portion_Name`, `Recipe_For_How_Many`) VALUES ((select nextRecipeCode()), '";
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

$sql = "SELECT Food_Code, constituentid from constituents WHERE Food_Name = '".$foodname."' limit 1;";
echo $sql."<br /><br />";
$ingredientcodes = $mysqli->query($sql) or die('There was an error getting the ingredient food_code and constituentid [' . $mysqli->error . ']');
$row = $ingredientcodes->fetch_row();
$ingredientfoodcode = $row[0];
$constituentid = $row[1];

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

/* close connection */
$mysqli->close();