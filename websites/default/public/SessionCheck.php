<?php
session_start();
// requiring AutoloadClass to load the required classes automatically (autoloading class)
require 'AutoloadClass.php';
// AllQueries class called through magic function to call functions for crud operations
$allQueries = new AllQueries;
// if user name is set in session
if (isset($_SESSION['username'])) {
    // if the user exists
    $userData = $allQueries->find('users', 'id', $_SESSION['userid']);
    // if the user have registered but have not completed the profile, they must complete it first for further page accessing
    if($userData['verified'] == 2){
        // prompts message 'complete profile before proceeding' and redirecting to complete profile page
        echo '<script>alert("Complete your profile before proceeding!")
        
        window.location.href = "complete_profile.php";
        </script>';
    	exit();
   	}
}
?>