<?php

require 'db_connect.php';

$string = $_POST["q"];

$words = explode(' ', $string);
$sql = "SELECT Food_Code, 
				Recipe_Name, 
				calories, 
				portion_name 
			FROM perportionrecipevals 
			WHERE";
$sql_end = '';
foreach($words as $word) {
    $sql_end .= " AND Recipe_Name LIKE '%{$word}%'";
}
$sql_end = substr($sql_end, 4);
$sql = $sql.$sql_end;
//echo $sql ." is the sql string<br>";

$result = $mysqli->query($sql);

if ($result) {
    echo '
    <form name="selection-list" id="selection-list" 	
	z-index="10"
    width="300px"
    height="175px">
	<select	size="10" style="width:100%" 
			onchange="setOption(options[selectedIndex].value, options[selectedIndex].text);"
	>';
    $i = 0;
	$foodcodes = array();
    while ($obj = $result->fetch_assoc()) {
		$foodcodes[$i] = $obj['Food_Code'];
        $i++;
        echo '
        <option value="'.$obj['Food_Code'].'"';

        echo '
        >'
        . $obj['Recipe_Name'] . " - "
        . $obj['Portion_Name'] ." = "
        . $obj['Calories'] . " Cals</option>";
    }
	echo '</select></form>';
			
    // free result set 
    $result->close();
}

// close connection 
$mysqli->close();

