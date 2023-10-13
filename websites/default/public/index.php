<?php
session_start();
// requiring AutoloadClass to load the required classes automatically (autoloading class)
require 'AutoloadClass.php';
$allQueries = new AllQueries;
// title is set...
$title = 'Home';
require 'heading.php';
// if user id is set in session
if(isset($_SESSION['userid'])){
	// finding specific user's data with id set in session
	$userData = $allQueries->find('users', 'id', $_SESSION['userid']);
	// setting user's title
	if($userData['title'] == 1){
		$userTitle = 'Mr.';
	}
	else if($userData['title'] == 1){
		$userTitle = 'Mrs.';
	}
	else{
		$userTitle = 'Miss.';
	}
}
echo '<main class="home">';
echo '<h2 style = "text-align: center;">Welcome to Fotheby\'s Auction Houses</h2>';
// checking if username is set in session and showing their name in homepage with appropriate title
if(isset($_SESSION['username'])){
	echo 'Hello! '.$userTitle.' '.$userData['full_name'];
}
// including show stories...
include('show_stories.php');
echo '</main>';
// including footer page
require('footer.php');
?>
