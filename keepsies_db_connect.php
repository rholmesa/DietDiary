<?php
ini_set('date.timezone', 'Europe/London');
define("HOST", "localhost"); // The host you want to connect to.
define("USER", "rwgholme"); // The database username.
define("PASSWORD", "1Can'tRemember"); // The database password. 
define("DATABASE", "rwgholme_DietDiary"); // The database name.
 
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
// If you are connecting via TCP/IP rather than a UNIX socket remember to add the port number as a parameter.

if ($mysqli->connect_error) {
	die ("Database connection failed "  . $mysqli->connect_error);
}
