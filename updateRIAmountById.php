<?php

require 'db_connect.php';
date_default_timezone_set('UTC');

$ingredientid = $mysqli->real_escape_string($_POST["ingredientid"]);
$newamount = $mysqli->real_escape_string($_POST["newamount"]);

echo $ingredientid." -> ".$newamount;

$sql = "UPDATE recipe_ingredients SET qty = '".$newamount."' WHERE recipe_ingredients.ingredientid = '".$ingredientid."';";

$result = $mysqli->query($sql) or die("Failed to update ingredient amount in database (".mysqli_errno($mysqli).")");
$mysqli->close();
