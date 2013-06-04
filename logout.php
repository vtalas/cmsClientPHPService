<?php 
session_start();
require  'libs.php';

//todo call server logout
$_SESSION["oauth"] = null;
header("Location: ". $_SERVER["HTTP_REFERER"]);

?>