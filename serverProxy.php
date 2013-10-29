<?php 
require  'libs/libs.php';
require  'config.php';
session_start();

try {
	$action = null;
	if (isset($_GET["action"])) {
		$action = $_GET["action"];
		unset($_GET["action"]);
	}
	$id = null;
	if (isset($_GET["id"])) {
		$id = $_GET["id"];
		unset($_GET["id"]);
	}
	$u = resolve(BASE_URL, $action, $id, $_GET);

	$rs = getContent($u);
	if (isset($rs["content"])) {
		echo '{ "data": '.$rs["content"].' , "snapshot": 1 }';
	}
}
catch(Exception $ex) {
	echo ExceptionToJson($ex, error_get_last());
}
?>
