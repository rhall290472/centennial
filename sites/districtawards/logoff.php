<?php
session_unset();
//session_destroy();
session_start();

require_once 'CDistrictAwards.php';
$cDistrictAwards = cDistrictAwards::getInstance();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page
$cDistrictAwards->GotoURL("index.php");
exit;
