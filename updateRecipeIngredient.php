<?php

require 'db_connect.php';
date_default_timezone_set('UTC');
$recipefoodcode = $mysqli->real_escape_string($_POST["recipefoodcode"]);
$ingredientfoodcode = $mysqli->real_escape_string($_POST["ingredientfoodcode"]);
$newamount = $mysqli->real_escape_string($_POST["newamount"]);
echo $recipefoodcode ." -- " .$ingredientfoodcode." -> ".$newamount;
$sql = "UPDATE recipe_ingredients SET qty = '".$newamount."' WHERE recipe_ingredients.recipe_food_code = '".$recipefoodcode."' AND recipe_ingredients.food_code = '".$ingredientfoodcode."';";
$result = $mysqli->query($sql) or die("Failed to update ingredient amount in database (".mysqli_errno($mysqli).")");
$mysqli->close();