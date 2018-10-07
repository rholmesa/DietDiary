<?php

require 'db_connect.php';

$sql = "select constituentid from constituents where food_code = '".$mysqli->real_escape_string($_POST['ingredientfoodcode'])."' limit 1;";
$result = $mysqli->query($sql);
$constituentid = mysqli_fetch_array($result);

$sql = "INSERT INTO `recipe_ingredients` (`recipeid`, `recipe_food_code`, `Food_Code`, `Qty`, constituentid) VALUES ('";
$sql .= $mysqli->real_escape_string($_POST['recipeid']);
$sql .= "', '";
$sql .= $mysqli->real_escape_string($_POST['recipefoodcode']);
$sql .= "', '";
$sql .= $mysqli->real_escape_string($_POST['ingredientfoodcode']);
$sql .= "', '";
$sql .= $mysqli->real_escape_string($_POST['ingredientamount']);
$sql .= "', '";
$sql .= $constituentid['constituentid'];
$sql .= "');";

$result = $mysqli->query($sql);

/* close connection */
$mysqli->close();

