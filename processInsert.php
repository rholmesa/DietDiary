<?php

require 'db_connect.php';

$sql = $mysqli->real_escape_string($_POST["sqlstr"]);

$result = $mysqli->query($sql) or die(mysqli_error($mysqli));

/* close connection */
$mysqli->close();
