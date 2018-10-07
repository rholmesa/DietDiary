<?php

function nextFoodCode() {
	$sql = 'SELECT CONCAT(
		IF((poststring > 998), prestring + 1, prestring), "-", 
			LPAD(IF ((poststring > 998),"000",poststring + 1),3,"0")
		)
			FROM 
			(SELECT 
				LEFT(MAX(food_code), 2) AS prestring, 
				RIGHT(MAX(food_code), 3) AS poststring 
			FROM constituents) t1';

	echo "SQL -  + $sql";
	$newFoodCode = $mysqli->query($sql) or die(mysqli_error($mysqli));
	echo $newFoodCode;
	return $newFoodCode; 
}

function nextRecipeCode() {
	$sql = 'SELECT CONCAT(
		IF((poststring > 998), prestring + 1, prestring), "-", 
			LPAD(IF ((poststring > 998),"000",poststring + 1),3,"0")
		) AS nextcode
			FROM 
			(SELECT 
				LEFT(MAX(food_code), 2) AS prestring, 
				RIGHT(MAX(food_code), 3) AS poststring 
			FROM recipe_names) t1';

	$newRecipeCode = $mysqli->query($sql) or die(mysqli_error($mysqli));
	echo $newRecipeCode;
	return $newRecipeCode;
}

function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}
