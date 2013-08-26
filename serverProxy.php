<?php 
require  'libs/libs.php';
require  'config.php';
session_start();

try {
	$action = isset($_GET["action"]) ? $_GET["action"] : null ;
	$id = isset($_GET["id"]) ? $_GET["id"] : null ;
	$u = resolve(BASE_URL, $action, $id);
	$rs = getContent($u);
	if (isset($rs["content"])) {
		echo '{ "data": '.$rs["content"].' , "snapshot": 1 }';
	}
}
catch(Exception $ex) {
	echo ExceptionToJson($ex, error_get_last());
}
?>
