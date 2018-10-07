<?php

require 'db_connect.php';

$string = $_POST["q"];

$words = explode(' ', $string);
$sql = "select 	recipe_names.food_code as foodCode, 
		Recipe_Name as recipeName, 
		Recipe_Portion_Name as Portion, 
		Recipe_For_How_Many as Servings
	from recipe_names, constituents 
	where constituents.food_code = recipe_names.food_code ";
$sql_end = '';
foreach($words as $word) {
    $sql_end .= " AND Recipe_Name LIKE '%{$word}%'";
}
// $sql_end = substr($sql_end, 4);
$sql = $sql.$sql_end.";";
//echo $sql ." is the sql string<br>";

$result = $mysqli->query($sql) or die($mysqli->error());

if ($result) {
    while ($data = $result->fetch_array()) {
            $outputdata[] = $data;
    }
    echo json_encode($outputdata);
    /* close the result object */
    $result->close();
} else {
    echo json_encode("NO Matches");
}
/* close connection */
$mysqli->close();
