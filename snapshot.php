<?php 
require  'libs/libs.php';

function saveToFile($destination, $content)
{
	$file = $destination;
	file_put_contents($file, $content);
}

$data = json_decode(file_get_contents('php://input'));
$content = isset($data->{"data"}) ? $data->{"data"} : null;
$destination = isset($data->{"path"}) ? $data->{"path"} : null;

saveToFile("snapshots".$destination, $content);

preprint($content);

?>
