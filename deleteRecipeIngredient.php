<?php

require 'db_connect.php';

$recipefoodcode = $mysqli->real_escape_string($_POST["recipefoodcode"]);
$ingredientfoodcode = $mysqli->real_escape_string($_POST["ingredientfoodcode"]);

$sql = "DELETE FROM recipe_ingredients WHERE recipe_ingredients.recipe_food_code = '".$recipefoodcode."' AND recipe_ingredients.food_code = '".$ingredientfoodcode."';";
//echo $sql;
$result = $mysqli->query($sql) or die("Failed to delete Ingredient ".ingredientfoodcode." from recipe ".$recipefoodcode." in database (".mysqli_errno($mysqli).")");
$mysqli->close();