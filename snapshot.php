<?php 
require  'libs/libs.php';
$basepath = "../../snapshots";


function saveToFile($destination, $content)
{
	$file = $destination;
	file_put_contents($file, $content);
}

$data = json_decode(file_get_contents('php://input'));
$content = isset($data->{"data"}) ? $data->{"data"} : null;
$destination = isset($data->{"path"}) ? $data->{"path"} : "";

$stack = explode('/', $destination);
$filename = array_pop($stack);

$directory = $basepath.implode('/', $stack);

if (!file_exists($directory)) {
    mkdir($directory, 0777, true);
}

saveToFile($directory.'/'.$filename.'.html', $content);

?>
