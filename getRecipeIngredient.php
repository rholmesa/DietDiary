<?php

require 'db_connect.php';

$recipecode = $mysqli->escape_string($_POST["recipe"]);
$ingredientcode = $mysqli->escape_string($_POST["ingredient"]);

$sql1 = "select `Food_Name`,
        `Qty` as Quantity,
        ingredientid
	from `recipe_ingredients`, `constituents`   
	where `constituents`.`food_code`=`recipe_ingredients`.`food_code`   
	and `recipe_ingredients`.`recipe_food_code` = '".$recipecode."'  
        and `recipe_ingredients`.`food_code` = '".$ingredientcode."';";
//echo $sql1;

$result = $mysqli->query($sql1) or die("Failed to get Ingredient ".$ingredientcode." from Recipe ".$recipecode." in database. (error ".mysqli_errno($mysqli).")");
if ($result) {
    while ($data = $result->fetch_array()) {
            $outputdata[] = $data;
    }
    echo json_encode($outputdata);
    /* close the result object */
    $result->close();
} else {
    echo json_encode("");
}
/* close connection */
$mysqli->close();