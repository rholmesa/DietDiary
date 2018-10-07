<?php
if ($_GET['randomId'] != "idkZsMboCxNiqKbbxGK_mI0OSqYROQ5iVQvKkeHukWG_wZJMMOiE0KqQaGmBb9jU") {
    echo "Access Denied";
    exit();
}

// display the HTML code:
echo stripslashes($_POST['wproPreviewHTML']);

?>  
