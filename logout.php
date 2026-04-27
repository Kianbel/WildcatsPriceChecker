<?php
session_start();
session_destroy(); // Clears all session variables
header("Location: login.php"); // Send back to login
exit();
?>