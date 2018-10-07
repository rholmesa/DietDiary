<?php

require 'db_connect.php';
include 'commonHeader.php';

$sql = "INSERT INTO `recipe_names` (`Food_Code`, `Recipe_Name`, `Recipe_Portion_Name`, `Recipe_For_How_Many`) VALUES ((select nextRecipeCode()), '";
$sql .= $mysqli->real_escape_string($_POST['recipename']);
$sql .= "', '";
$sql .= $mysqli->real_escape_string($_POST['portionname']);
$sql .= "', '";
$sql .= $mysqli->real_escape_string($_POST['numportions']);
$sql .= "');";

$result = $mysqli->query($sql);
echo $sql;
/* close connection */
$mysqli->close();