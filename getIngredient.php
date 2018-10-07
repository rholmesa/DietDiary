<?php

require 'db_connect.php';

$ingredientfoodcode = $mysqli->real_escape_string($_POST["ingredientfoodcode"]);

$sql = "SELECT `Food_Code`, `food_name`, `portion_qty`, `portion_name`, `Energy_kcal_kcal` as energy, `fat_g` as fat, `Satd_FA_/100g_fd_g` as satfats,"; 
$sql .= "`Carbohydrate_g` as carbs, `Total_sugars_g` as sugars, ";
$sql .= "`AOAC_fibre_g` as fibre, `protein_g` as protein, `Sodium_mg` as sodium,  `Cholesterol_mg` as Cholesterol, `alcohol_g` as alcohol ";
$sql .= "FROM `constituents` ";
$sql .= "WHERE `food_code` = '".$ingredientfoodcode."';";

$result = $mysqli->query($sql) or die("Failed to find ingredient ".$ingredientfoodcode." (error ".mysqli_error($mysqli).").");

if ($result) {
    $obj = $result->fetch_assoc();
    $outputdata[] = $obj;
    echo json_encode($outputdata);
    $result->close();
} else {
    echo "Failed to get a result".$ingredientfoodcode;
}

/* close connection */
$mysqli->close();

