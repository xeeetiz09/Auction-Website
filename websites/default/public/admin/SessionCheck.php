<?php
// starting session
session_start();
// requiring autoload class for autoloading classes via magic method
require '../AutoloadClass.php';
$allQueries = new AllQueries;
// if adminname is not set in session, they cannot access any admin pages
if (!isset($_SESSION['adminname'])){
    header('Location: /login.php');
}
?>