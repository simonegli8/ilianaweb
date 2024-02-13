<?php

defined('is_running') or die('Not an entry point...');

global $homeurl, $pageurl, $sermonsversion;

$sermonsversion = '1.23';

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function HrefEncode($path) {
	global $homeurl, $sermonsconfig;
	//return str_replace(array('&', '"', "'"), array('&amp;', '&quot;', '&squot;'), $homeurl . ltrim(utf8_encode($path), '.'));
	if (startsWith($path, 'http://') || startsWith($path, 'https://')) return $path;
	if ($sermonsconfig['utf8encode'] == true) $path = utf8_encode($path);
	return $homeurl . str_replace('//', '/', ltrim(dirname($path), '.') . '/' . rawurlencode(basename($path)));
}

if (class_exists('common')) {
	$home = str_replace('/index.php/', '', common::GetUrl('/'));
	$sermonsconfig['home'] = $home;
	$sermonsconfig['homedir'] = getcwd();
} else {
	$home = $sermonsconfig['home'];
}

if ($home == '/') $home = '';
$host = 'http';
if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
    $_SERVER['HTTPS']='on';
}
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {$host .= 's';}
$host .= '://';
if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
    $host .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
} else {
    $host .= $_SERVER['SERVER_NAME'];
}
$homeurl = $host . $home;
if (endsWith($homeurl, '/')) $homeurl = substr($homeurl, 0, strlen($homeurl)-1);

$pageurl = $host . $_SERVER['REQUEST_URI'];

$sermonsconfig['homeurl'] = $homeurl;

?>