<?php
ini_set('date.timezone', 'Europe/London');
define("HOST", "127.0.0.1:3306"); // The host you want to connect to.
define("USER", "ron"); // The database username.
define("PASSWORD", "jod1adam"); // The database password. 
define("DATABASE", "dietdiary"); // The database name.
 
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
// If you are connecting via TCP/IP rather than a UNIX socket remember to add the port number as a parameter.

if ($mysqli->connect_error) {
	die ("Database connection failed "  . $mysqli->connect_error);
}
