<?php

require 'db_connect.php';
require 'phpFunctions.php';  



$sql = "INSERT INTO `constituents` (`Food_Code`, `Food_Name`, `barcode`, `portion_qty`, `portion_name`, `Energy_kcal_kcal`, `fat_g`, `Satd_FA_/100g_fd_g`,"; 
$sql .= "`Carbohydrate_g`, `Total_sugars_g`, `AOAC_fibre_g`, `protein_g`, `Sodium_mg`,  `Cholesterol_mg`, `alcohol_g`) ";
/* $sql .= "Values ((select nextFoodCode()),"; */
$sql .= "Values (nextFoodCode(),";
$sql .= "'".$mysqli->real_escape_string($_get['food_name'])."', ";
$sql .= "'".$mysqli->real_escape_string($_get['barcode'])."', ";
$sql .= "'100', '100g', ";
$sql .= "'".$mysqli->real_escape_string($_get['energy'])."', ";
$sql .= "'".$mysqli->real_escape_string($_get['fat'])."', ";
$sql .= "'".$mysqli->real_escape_string($_get['satfats'])."', ";
$sql .= "'".$mysqli->real_escape_string($_get['carbs'])."', ";
$sql .= "'".$mysqli->real_escape_string($_get['sugars'])."', ";
$sql .= "'".$mysqli->real_escape_string($_get['fibre'])."', ";
$sql .= "'".$mysqli->real_escape_string($_get['protein'])."', ";
$sql .= "'".$mysqli->real_escape_string($_get['sodium'])."', ";
$sql .= "'".$mysqli->real_escape_string($_get['Cholesterol'])."', ";
$sql .= "'".$mysqli->real_escape_string($_get['alcohol'])."');";

$result = $mysqli->query($sql) or die(mysqli_error($mysqli));
echo $result;

/* close connection */
$mysqli->close();