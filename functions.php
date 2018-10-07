<?php
/*
It important not to just put "session_start();" on the top of every page that you want to use php sessions.
If you're really concerned about security then this is how you should do it. 

We are going to create a function called "sec_session_start", this will start a php session in a secure way. 
You should call this function at the top of any page you wish to access a php session variable. 
If you are really concerned about security and the privacy of your cookies, 
have a look at this article Create-a-Secure-Session-Managment-System-in-Php-and-Mysql.

This function makes your login script a whole lot more secure. 
It stops hackers been able to access the session id cookie through javascript (For example in an XSS attack).
Also by using the "session_regenerate_id()" function, which regenerates the session id on every page reload, 
helping prevent session hijacking.

Note: If you are using https in your login application set the "$secure" variable to true.

*/
function sec_session_start() {
	$session_name = 'ddLogin'; // Set a custom session name
	$secure = false; // Set to true if using https.
	$httponly = true; // This stops javascript being able to access the session id. 

	ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
	$cookieParams = session_get_cookie_params(); // Gets current cookies params.
	session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
	session_name($session_name); // Sets the session name to the one set above.
	session_start(); // Start the php session
	session_regenerate_id(); // regenerated the session, delete the old one. 
}
/*
This function will check the username and password against the database, it will return true if there is a match.
*/
function login($username, $password, $mysqli) {
   // Using prepared Statements means that SQL injection is not possible. 
   if ($stmt = $mysqli->prepare("SELECT id, username, email, password, salt FROM members WHERE username = ? LIMIT 1")) { 
      $stmt->bind_param('s', $username); // Bind "$username" to parameter.
      $stmt->execute(); // Execute the prepared query.
      $stmt->store_result();
      $stmt->bind_result($user_id, $username, $db_email, $db_password, $salt); // get variables from result.
      $stmt->fetch();
      $password = hash('sha512', $password.$salt); // hash the password with the unique salt.
 
      if($stmt->num_rows == 1) { // If the user exists
         // We check if the account is locked from too many login attempts
         if(checkbrute($user_id, $mysqli) == true) { 
            // Account is locked
            echo "Account is locked!";
            return false;
         } else {
//		 echo "'Passwords: '.$db_password.' <br>  '.$password";
         if($db_password == $password) { // Check if the password in the database matches the password the user submitted. 
            // Password is correct!
            $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.

            $user_id = preg_replace("/[^0-9]+/", "", $user_id); // XSS protection as we might print this value
            $_SESSION['id'] = $user_id; 
            $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username); // XSS protection as we might print this value
            $_SESSION['username'] = $username;
            $_SESSION['login_string'] = hash('sha512', $password.$user_browser);
            $_SESSION['user_id'] = $user_id;
            $_SESSION['useremail'] = $db_email;
            
               // Login successful.
              return true;    
         } else {
            // Password is not correct
            // We record this attempt in the database
            $now = time();
            $mysqli->query("INSERT INTO login_attempts (user_id, time) VALUES ('$user_id', '$now')");
            return false;
         }
      }
      } else {
         // No user exists. 
         return false;
      }
   }
}
/*
Brute force attacks are when a hacker will try 1000s of different passwords on an account, 
either randomly generated passwords or from a dictionary. 

In thids script if a user account has a failed login more than 5 times their account is locked.
*/
function checkbrute($user_id, $mysqli) {
   // Get timestamp of current time
   $now = time();
   // All login attempts are counted from the past 2 hours. 
   $valid_attempts = $now - (2 * 60 * 60); 
 
   if ($stmt = $mysqli->prepare("SELECT time FROM login_attempts WHERE user_id = ? AND time > '$valid_attempts'")) { 
      $stmt->bind_param('i', $user_id); 
      // Execute the prepared query.
      $stmt->execute();
      $stmt->store_result();
      // If there has been more than 5 failed logins
      if($stmt->num_rows > 5) {
         return true;
      } else {
         return false;
      }
   }
}
/*
Check logged in status.
We do this by checking the the "user_id" and the "login_string" SESSION variables. 
The "login_string" SESSION variable has the users Browser Info hashed together with the password. 
We use the Browser Info because it is very unlikely that the user will change their browser mid-session. 
Doing this helps prevent session hijacking.
*/
function login_check($mysqli) {
   // Check if all session variables are set
   if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
     $user_id = $_SESSION['user_id'];
     $login_string = $_SESSION['login_string'];
     $username = $_SESSION['username'];
 
     $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.
 
     if ($stmt = $mysqli->prepare("SELECT password FROM members WHERE id = ? LIMIT 1")) { 
        $stmt->bind_param('i', $user_id); // Bind "$user_id" to parameter.
        $stmt->execute(); // Execute the prepared query.
        $stmt->store_result();
 
        if($stmt->num_rows == 1) { // If the user exists
           $stmt->bind_result($password); // get variables from result.
           $stmt->fetch();
           $login_check = hash('sha512', $password.$user_browser);
           if($login_check == $login_string) {
              // Logged In!!!!
              return true;
           } else {
              // Not logged in
              return false;
           }
        } else {
            // Not logged in
            return false;
        }
     } else {
        // Not logged in
        return false;
     }
   } else {
     // Not logged in
     return false;
   }
}

function checkEmail($str)
{
    return preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $str);
}


function send_mail($from,$to,$subject,$body)
{
	$headers = '';
	$headers .= "From: $from\n";
	$headers .= "Reply-to: $from\n";
	$headers .= "Return-Path: $from\n";
	$headers .= "Message-ID: <" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Date: " . date('r', time()) . "\n";

	mail($to,$subject,$body,$headers);
}

function createTable($name, $query)
{
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
    echo "Table '$name' created or already exists.<br />";
}

function queryMysql($query)
{
    $result = mysql_query($query) or die(mysql_error());
	 return $result;
}

function destroySession()
{
    $_SESSION=array();
    
    if (session_id() !== "" || isset($_COOKIE[session_name()])){
        setcookie(session_name(), '', time()-2592000, '/');
    }
    session_destroy();
}
