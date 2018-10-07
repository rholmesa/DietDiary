<?php
	require 'db_connect.php';
	require 'functions.php';
	sec_session_start(); // Our custom secure way of starting a php session. 

	// username and password will have been completed

	$username = $_POST['username'];
	$password = $_POST['password'];
	$hashedPassword = $_POST['hashedPassword'];
	$_POST['rememberMe'] = (int)$_POST['rememberMe'];

	if(login($username, $hashedPassword, $mysqli) == true) {
		// Login success
		setcookie('ddRemember',$_POST['rememberMe']);
//		echo "pass";
		header('Location: ./index.html#dailies');
//		header('Location: ./starter.html#accordion');
		return true;
	} else {
		// Login failed
//		echo "fail";
		header('Location: ./');
		return false;
	}
	