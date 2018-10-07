<?php
	require 'db_connect.php';
	require 'functions.php';
	sec_session_start();
	if(login_check($mysqli) == true) {
	 
	   // Add your protected page content here!
	 
	} else {
	    echo 'You are not authorized to access this page, please login. <br/>';
		header('Location: ./index.php');
	}