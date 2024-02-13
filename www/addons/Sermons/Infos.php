<?php

global $sermonsconfig;

function SermonsCompareInfos($a, $b) {
	if ($a['time'] == $b['time']) return strcmp($a['title'], $b['title']);
	else if ($a['time'] > $b['time']) return -1;
	return 1;
}


function SermonsRemoveAccents($string) {
	// Revised version by marksteward?otmail*com
	// Again revised by James Heinrich (19-June-2006)
	return strtr(
		strtr(
			$string,
			"\x8A\x8E\x9A\x9E\x9F\xC0\xC1\xC2\xC3\xC4\xC5\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD1\xD2\xD3\xD4\xD5\xD6\xD8\xD9\xDA\xDB\xDC\xDD\xE0\xE1\xE2\xE3\xE4\xE5\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF1\xF2\xF3\xF4\xF5\xF6\xF8\xF9\xFA\xFB\xFC\xFD\xFF",
			'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy'
		),
		array(
			"\xDE" => 'TH',
			"\xFE" => 'th',
			"\xD0" => 'DH',
			"\xF0" => 'dh',
			"\xDF" => 'ss',
			"\x8C" => 'OE',
			"\x9C" => 'oe',
			"\xC6" => 'AE',
			"\xE6" => 'ae',
			"\xB5" => 'u'
		)
	);
}

function SermonsMoreNaturalSort($ar1, $ar2) {
	if ($ar1 === $ar2) {
		return 0;
	}
	$len1     = strlen($ar1);
	$len2     = strlen($ar2);
	$shortest = min($len1, $len2);
	if (substr($ar1, 0, $shortest) === substr($ar2, 0, $shortest)) {
		// the shorter argument is the beginning of the longer one, like "str" and "string"
		if ($len1 < $len2) {
			return -1;
		} elseif ($len1 > $len2) {
			return 1;
		}
		return 0;
	}
	$ar1 = SermonsRemoveAccents(strtolower(trim($ar1)));
	$ar2 = SermonsRemoveAccents(strtolower(trim($ar2)));
	$translatearray = array('\''=>'', '"'=>'', '_'=>' ', '('=>'', ')'=>'', '-'=>' ', '  '=>' ', '.'=>'', ','=>'');
	foreach ($translatearray as $key => $val) {
		$ar1 = str_replace($key, $val, $ar1);
		$ar2 = str_replace($key, $val, $ar2);
	}

	if ($ar1 < $ar2) {
		return -1;
	} elseif ($ar1 > $ar2) {
		return 1;
	}
	return 0;
}

function SermonsPath($path = null) {
	global $sermonsconfig;
	if ($path === null) {
		$path =$sermonsconfig['path'];
		$dirs = glob($path . '/*' , GLOB_ONLYDIR);
		$query = explode('&', $_SERVER['QUERY_STRING']);
		$page = null;
		foreach ($query as $token) {
			$t = explode('=', $token);
			if (count($t) == 2 && strcmp($t[0], 'gp_rewrite') == 0) {
				$page = $t[1];
			}
		}
		if ($page != null) {
			foreach ($dirs as $dir) {
				if (strcmp($page, substr($dir, strlen($path)+1)) == 0) {
					$path = $dir;
				}
			}
		}
	}
	return $path;
}

function SermonsLoad($path = null) {

	global $sermonsconfig;
	/////////////////////////////////////////////////////////////////
	// die if magic_quotes_runtime or magic_quotes_gpc are set
	if (function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime()) {
		die('magic_quotes_runtime is enabled, getID3 will not run.');
	}
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
		die('magic_quotes_gpc is enabled, getID3 will not run.');
	}
	if (!defined('ENT_SUBSTITUTE')) { // defined in PHP v5.4.0
		define('ENT_SUBSTITUTE', ENT_QUOTES);
	}
	/////////////////////////////////////////////////////////////////
	
	$PageEncoding = 'UTF-8';

	require_once('getid3/getid3.php');

	require_once('Setup.php');

	// Needed for windows only. Leave commented-out to auto-detect, only define here if auto-detection does not work properly
	//define('GETID3_HELPERAPPSDIR', 'C:\\helperapps\\');

	// Initialize getID3 engine
	$getID3 = new getID3;
	$getID3->setOption(array('encoding' => $PageEncoding));

	$path = SermonsPath($path);

	SetupPath($path);

	//$mp3path = './data/_uploaded/sermons/*.[mM][pP]3';
	$mp3path = $path . '/*.[mM][pP]3';
	$mp3files = glob($mp3path);
	//$imgspath = './data/_uploaded/sermons/*.jpg';
	$imgspath = $path . '/*.jpg';
	$imgfiles = glob($imgspath);
 
	$speakerFilter = '*';
	$yearFilter = '*';

	if (isset($_REQUEST['authorfilter'])) $speakerFilter = $_REQUEST['authorfilter'];
	if (isset($_REUQEST['yearfilter'])) $yearfilter = $_REQUEST['yearfiter'];

	$speakers = array();
	$years = array();
	$infos = array();

	foreach ($mp3files as $file) {
		$info = $getID3->analyze($file);
		getid3_lib::CopyTagsToComments($info);

		$info['filename'] = basename($file);
		$info['path'] = $file;
		$created = filectime($file);
		$time = $created;
		$modified = filemtime($file);
		if ($modified < $created) $time = $modified;
		$matches = array();
		$nametime = null;
		$datepattern = '/(\.|^)(\d{1,2})[.-](\d{1,2})[.-](\d{2,4})(\.|$)/';
		$title = htmlentities(utf8_encode(pathinfo($file, PATHINFO_FILENAME)));
		if (preg_match($datepattern, $file, $matches) > 0) {
			$year = $matches[4];
			if (strlen($year) <= 2) $year = '20'.$year; 
			if ($sermonsconfig['dateformat'][0] == 'm' || $sermonsconfig['dateformat'][0] == 'n') $date = date_create_from_format('j-n-Y', $matches[3] .'-'.$matches[2].'-'.$year);
			else $date = date_create_from_format('j-n-Y-H-i-s-u', $matches[2] .'-'.$matches[3].'-'.$year.'-00-00-00-0');
			$time = $date->getTimestamp();
			$title = preg_replace($datepattern, "", $title);    
		}
		
		
		$info['time'] = $time;
		if (isset($info['comments_html'])) {
			$cmh = $info['comments_html'];
			$cm = $info['comments'];
			if (isset($cm['band'][0]) && $cm['band'][0] != null) {
				$speaker = $cm['band'] [0];
				$info['speaker'] = $cmh['band'][0];
				$info['speaker0'] = $cm['band'][0];
			} else if (isset($cm['artist'][0]) && $cm['artist'][0] != null) {
				$speaker = $cm['artist'][0];
				$info['speaker'] = $cmh['artist'][0];
				$info['speaker0'] = $cm['artist'][0];
			} else {
				$speaker = 'Unknown';
				$info['speaker'] = 'Unknown';
				$info['speaker0'] = 'Unknown';
			}
			$speakerpattern = '/'.preg_quote($info['speaker']).'\./i';
			$title = preg_replace($speakerpattern, '', $title);
			if (isset($cm['title'][0]) && is_string($cm['title'][0])) { 
				$info['title'] = $cmh['title'][0];
				$info['title0'] = $cm['title'][0];
			} else {
				$info['title'] = $title;
				$info['title0'] = $title;
			}
		} else {
			$speaker = 'Unknown';
			$info['speaker'] = 'Unknown';
			$info['speaker0'] = 'Unknown';
			$info['title'] = $title;
			$info['title0'] = $title;
		}
		foreach ($imgfiles as $img) {
			$imgname = pathinfo($img, PATHINFO_FILENAME);
			if ($sermonsconfig['utf8encode'] == true) $imgname = utf8_encode($imgname);
			if ($imgname == $speaker || $imgname == strtok($speaker, ' ') || $imgname == strtok(' ')) {
				$info['authorimg'] = $img;
			}
		}
		if (($speakerFilter == '*' || $info['speaker'] == $speakerFilter) && ($yearFilter == '*' || date('Y', $created) == $yearFilter)) $infos[] = $info;
		$speakers[] = $info['speaker'];
		$years[] = date('Y', $time);
	}

	usort($infos, "SermonsCompareInfos");
	usort($speakers, "SermonsMoreNaturalSort");
	rsort($years);
	$speakers = array_unique($speakers);
	$years = array_unique($years);

	return array('infos' => $infos, 'speakers' => $speakers, 'years' => $years);
}
?>