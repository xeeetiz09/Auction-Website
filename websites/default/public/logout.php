<?php
// starting the session 
session_start();
// destroying the session 
session_destroy();
// prmopting message and redirecting
echo '<script> alert("You have been logged out!");
window.location.href = "/";
</script>';
exit();
?>