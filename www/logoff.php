<?php
session_start();
$_SESSION['loggedIn'] = false;
$_SESSION['adminStatus'] = false;
$_SESSION['isAdminLoggedInStatus'] = false;
header('Location: login.php');
exit;
