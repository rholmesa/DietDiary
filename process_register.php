<?php
require 'db_connect.php';    
//	require 'functions.php';
//	sec_session_start(); // Our custom secure way of starting a php session. 
require 'commonHeader.php';

$err = array();						// Will hold our errors
// both username and password must have been completed
if((isset($_POST['username'])) && (isset($_POST['email'])) && (isset($_POST['hashedPassword']))) { 
    // If the Register form has been submitted and username and email are complete	
    $err = array();		
    if(strlen($_POST['username'])<4 || strlen($_POST['username'])>32)	{
        $err[]='Your username must be between 4 and 32 characters!';
    }		
    if(preg_match('/[^a-z0-9\-\_\.]+/i',$_POST['username']))	{
        $err[]='Your username contains invalid characters! ' .$_POST['username'];
    }		
    if(!checkEmail($_POST['email'])){
        $err[]='Your email is not valid!';
    }		
    if(count($err)) {
        $_SESSION['msg']['reg-err'] = implode('<br />', $err);
        header('Location: ./');	
    } else {
        // If there are no errors
        // The hashed password from the form
        $pass = $_POST['hashedPassword']; 
        // Create a random salt
        $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
        // Create salted password (Careful not to over season)
        $password = hash('sha512', $pass.$random_salt);
        $username = $_POST['username'];
        $email = $_POST['email'];
        // Add your insert to database script here. 
        // Make sure you use prepared statements!		

        $sql='INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)';

        /* Prepare statement */
        $insert_stmt = $mysqli->prepare($sql);

        if ($insert_stmt) { 
            /* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */			
            $insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt); 
            // Execute the prepared query.
            $insert_stmt->execute();	
            send_mail(	'info@rwgholmes.com',
                        $email,
                        'Nutrition Log - Registration System - Welcome',
                        'Please remember your password, it is fully encoded here so we cannot retrieve it.');
            $_SESSION['msg']['reg-success']='We sent you a confirmation email.';
            header("Location: ./index.html");

        } else {
            trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
            $err[]='This email is already taken!';
            $_SESSION['msg']['reg-err']= implode('<br />',$err);
            header("Location: ./index.html");
            exit("insert_stmt incorrect");
        }
    }		
    header("Location: ./index.html");
} else {
    $err[]='You must complete all fields.';
    $_SESSION['msg']['reg-err']= implode('<br />',$err);
    header("Location: ./index.html");
}				