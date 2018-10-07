<?php
require 'db_connect.php'; 
require 'commonHeader.php';

if(login_check($mysqli)) {
    echo ("Success");
    return true;
} else {
    echo ("Fail");
    return false;
}


