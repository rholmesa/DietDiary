<?php

require 'db_connect.php';

include 'commonHeader.php';


// get the next available code from the db
$sql = 'SELECT CONCAT(
	IF((poststring > 998), prestring + 1, prestring), "-", 
		LPAD(IF ((poststring > 998),"000",poststring + 1),3,"0")
	)
		FROM 
		(SELECT 
			LEFT(MAX(food_code), 2) AS prestring, 
			RIGHT(MAX(food_code), 3) AS poststring 
		FROM recipe_names) t1';

echo "SQL - $sql</br>";

$nextCode = $mysqli->query($sql) or die(mysqli_error($mysqli));

$row = mysqli_fetch_array($nextCode,MYSQLI_NUM);
$newRecipeCode = $row[0];

echo "New Recipe is $newRecipeCode";

/* $sql = "INSERT INTO `recipe_names` (`Food_Code`, `Recipe_Name`, `Recipe_Portion_Name`, `Recipe_For_How_Many`) VALUES ((select nextRecipeCode()), '"; */
$sql = "INSERT INTO `recipe_names` (`Food_Code`, `Recipe_Name`, `Recipe_Portion_Name`, `Recipe_For_How_Many`) VALUES ('$newRecipeCode', '";
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
