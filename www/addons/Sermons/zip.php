<?php

defined('is_running') or define('is_running', true);

if (isset($addonCodePath)) {
	require_once($addonCodePath.'/config.php');
	require_once($addonCodePath.'/ImprovedZipArchive.php');
	$path = $sermonsconfig['path'];
} else {
	require_once('ImprovedZipArchive.php');
	$path = '.';
}

mb_internal_encoding('UTF-8');

if (strpos(PHP_OS, 'WIN') === 0) {
    define('FS_ENCODING', 'CP1252');
} else {
    define('FS_ENCODING', 'UTF-8');
}

$zipFile = 'Sermons.zip';
$zipPath = $path.'/'.$zipFile;

$cwd = getcwd();

$lastMod = 0;
$lastModFile = '';
foreach (glob($path . '/*.{mp3,jpg,jpeg,Mp3,MP3,JPG,Jpg,JPEG,Jpeg,php,zip}', GLOB_BRACE) as $entry) {
    if (is_file($entry) && filectime($entry) > $lastMod) {
        $lastMod = filectime($entry);
        $lastModFile = $entry;
    }
}

if ($entry != $zipPath) {
 
	function endsWith($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}

	$zip = new ImprovedZipArchive(FS_ENCODING, 'UTF-8', 'CP850');
	$res = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE | ZipArchive::CM_STORE);
	if($res === TRUE) 
	{
		$zip->addGlob($path . '/*.{mp3,jpg,jpeg,Mp3,MP3,JPG,Jpg,JPEG,Jpeg}', GLOB_BRACE, ['remove_all_path' => true]);

		if (!$zip->status == ZipArchive::ER_OK) {
			echo "Failed to write files to zip\n";
			die();
		}
		$zip->close();
	}
	else  { echo 'Could not create a zip archive'; die(); }
}

ob_get_clean();
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=" . $zipFile . ";" );
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . filesize($zipPath));
readfile($zipPath);

?>