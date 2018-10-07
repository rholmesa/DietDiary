<?php

	require 'db_connect.php';
	require 'functions.php';
	sec_session_start(); // Our custom secure way of starting a php session. 

	if (login_check($mysqli)) {
		echo "Logged In";
	} else {
		echo "Not Logged In";
	};
