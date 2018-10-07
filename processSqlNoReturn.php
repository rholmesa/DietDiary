<?php

require 'db_connect.php';

$sql = $_POST["sqlstr"];

$result = $mysqli->query($sql) or die(mysqli_error($mysqli));



