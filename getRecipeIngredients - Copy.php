<?php

require 'db_connect.php';

$string = $mysqli->real_escape_string($_POST["recipe"]);


$sql= "SELECT recipeid, recipe_names.food_code, 
	recipe_name,
        recipe_portion_name AS Servings,
        portion_name as ingredientPortionName,
	recipe_for_how_many AS How_Many, 
	ricode AS RID,
        ingredientid,
	food_name AS Ingredient, 
	qty AS Quantity, 
	truncate((energy_kcal_kcal*qty/portion_qty),2) AS ppCalories
FROM recipe_names,  (
	SELECT constituents.food_code, 
			food_name, 
			recipe_food_code,
                        ingredientid,
                        portion_name,
			recipe_ingredients.food_code as ricode,
			qty, 
			energy_kcal_kcal, 
			portion_qty,
                        constituents.constituentid
	FROM constituents, recipe_ingredients
	WHERE constituents.food_code = recipe_ingredients.food_code) t1
WHERE recipe_names.food_code = t1.recipe_food_code
AND recipe_names.food_code ='".$string."' Order By ingredientid;" ;

//$sql = $mysqli->real_escape_string($sql);

$result = $mysqli->query($sql) or die("No Ingredients returned for recipe ".$string." error -- ".$mysqli->error);
if ($result) {
    $numrows = $result->num_rows;
    if ($numrows>0) {
        while ($data = $result->fetch_array()) {
                $outputdata[] = $data;
        }
        echo json_encode($outputdata);
    } else {
        echo json_encode("No Ingredients");
    }
    /* close the result object */
    $result->close();
} else {
    echo json_encode("No Entries");
}
/* close connection */
$mysqli->close();