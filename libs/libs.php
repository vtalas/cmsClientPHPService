<?php
require  'CmsOAuthToken.php';

if (!function_exists("preprint")) { 
	function preprint($s, $return=false) { 
		$x = "<pre>"; 
		$x .= print_r($s, 1); 
		$x .= "</pre>"; 
		if ($return) return $x; 
		else print $x; 
	} 
} 
function fatal($message){
	$list['error'] = $message; 
	echo json_encode($list);
	exit;

}

function isimage($file){
	$p = pathinfo($file);
	if (isset($p['extension']))
	{
		$e = strtolower($p['extension']);
		return 	($e == 'jpg'||$e == 'jpeg' || $e == 'gif' ||	$e == 'png');
	}
	return false;
}

function ExceptionToJson($ex, $inner="") {
	$rs["message"] = $ex->getMessage(); 
	$rs["innerException"] = $inner;
	$rs["status"] = 0; 
	header(':', true, 404);
	return json_encode($rs);
}

function curPageURL() {
	$pageURL = 'http';
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"];
	}
	return $pageURL;
}

function parse_response($response)
{
    $headers = array();
	list($header_text, $body) = explode("\r\n\r\n", $response, 2);

    foreach (explode("\r\n", $header_text) as $i => $line)
        if ($i === 0)
            $headers['http_code'] = $line;
        else
        {
            list ($key, $value) = explode(': ', $line);

            $headers[$key] = $value;
        }

    return array('body' => $body, 'header' => $headers);
}

function getArrayProperty($array, $property)
{
	if (isset($array[$property])) {
		return $array[$property];
	}
	return null;
}

function getContent($url, $method=CURLOPT_HTTPGET, $formdata=null) {
	$oauthCookie = getCookieFromSession();
	$requestHeaders = $arrayName = array();

	$ch = curl_init();
	$timeout = 5;

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	
	curl_setopt($ch, $method, 1);
	
	if ($formdata != null) {
		array_push($requestHeaders, 'Content-Type: application/json;charset=UTF-8');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $formdata);
	}

	if ($oauthCookie !=  null) {
		curl_setopt($ch, CURLOPT_COOKIE, $oauthCookie);
	}
	$g = apache_request_headers();

	//preprint(getallheaders());

	if (array_key_exists ("If-None-Match",$g)) { 
		array_push($requestHeaders, 'If-None-Match: '.$g["If-None-Match"]);
	}

	curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders); 
	$data = curl_exec($ch);
	
	if (!$data) {
		header(':', true, 500);
		return;
	}

	$parserdData = parse_response($data);
	$responseHeader = $parserdData["header"];

	header("Etag: ".getArrayProperty($responseHeader, "ETag"));
	header("Cache-Control: ".$responseHeader["Cache-Control"]);
	header($responseHeader["http_code"]);
	
	//preprint($responseHeader);
	//preprint($parserdData);

	$response["content"] = $parserdData["body"] != null ? $parserdData["body"] : null;

	return $response;
}

function getContentXX($url, $method, $formcontent=null)
{
	$cookies = getCookieFromSession();

	$opts = array(

		'http'=>array(
			'method'=>
			$method,
			'header'=>
			"Accept:application/json, text/plain, */*\r\n"
			."Content-Type:application/json;charset=UTF-8\r\n"
			)
		);

	if ($formcontent != null && ($method == "POST" || $method == "PUT")){
		$opts['http']['content'] = $formcontent;
	}
	if ($cookies != null){
		$opts['http']['header'] .= $cookies;
	}

	$context = stream_context_create($opts);

	if (!$file = file_get_contents($url, false, $context)) {
		header($http_response_header[0]);
	}
	return $file;
}


function resolve($baseurl, $action, $id=null)
{
	$baseurl = $baseurl."/".$action;
	if ($id != null) {
		$baseurl = $baseurl."/".$id;
	}
	return $baseurl;
}


function getCookieFromSession()
{

	$oauth = isset($_SESSION["oauth"]) ? $_SESSION["oauth"] : null;
	$cookie = null;

	if ($oauth){
		$x = new CmsOAuthToken($oauth);

		if ($x->isAccessExpired()){
    //todo refresh token 
    //return;
		}
		$cookie = "oauth_token=".$x->accessToken;
	}

	return $cookie;
}
?>