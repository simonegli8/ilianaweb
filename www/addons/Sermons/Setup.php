<?php


defined('is_running') or die('Not an entry point...');

if (!class_exists('gpFiles')) return;

if (!isset($addonCodeFolder)) {
	global $addonDataFolder, $addonRelativeCode, $addonRelativeData, $addonCodeFolder, $sermonsversion;
} else {
	$addonDataFolder = '.';
}

$sermonsconfig0 = array('dateformat' => 'j.n.Y', 'date' => "Date", 'topic' => "Topic", 'preacher' => "Preacher", 'length' => "Length", 'listen' => 'Listen', 'all' => "All", 'upload' => "Upload Sermon",'postonfacebook' => 'Post on Facebook', 'sendmail' => 'Send as Email',
	'download' => 'Download', 'utf8encode' => true, 'path' => './data/_uploaded/sermons', 'podcast' => 'Podcast', 'podtitle' => 'Sermons', 'poddesc' => 'Sermons', 'podauthor' => '', 'podlink' => '', 'podlang' => 'en', 'podimage' => '', 'podcopyright' => '',
	'podemail' => '', 'podmax' => '', 'podcategory' => 'Religion &amp; Spirituality', 'podsubcategory' => 'Christianity', 'podiconlink' => '', 'podkeywords' => 'feed:podcast');

if (file_exists($addonDataFolder.'/config.php')) {
	require_once($addonDataFolder.'/config.php');
} else {
	$sermonsconfig = array();
}

$save = false;

foreach ($sermonsconfig0 as $key => $value) {
	if (!array_key_exists($key, $sermonsconfig)) {
		$sermonsconfig[$key] = $value;
		$save = true;
	}
}

if ($sermonsconfig['path'] == null) {
	$sermonsconfig['path'] =  './data/_uploaded/sermons';
	$save = true;
}

require_once('Home.php');

if (!isset($sermonsconfig['version']) || $sermonsconfig['version'] != $sermonsversion) {
	$sermonsconfig['version'] = $sermonsversion;
	$save = true;
}


if ($save) gpFiles::Save($addonDataFolder.'/config.php', '<?php if (!isset($sermonsconfig)) global $sermonsconfig; ' . gpFiles::ArrayToPHP('sermonsconfig', $sermonsconfig) . '?>');

function SetupPath($path = null) {
	global $sermonsconfig, $addonCodeFolder;

    if (!class_exists('gpFiles')) return;

	if ($path === null) $path = $sermonsconfig['path'];

	$rss = "<?php require '$addonCodeFolder/rss.php'; ?>";
	if (!file_exists($path.'/rss.php') || strcmp(file_get_contents($path.'/rss.php'), $rss) != 0) {
		gpFiles::RmAll($path.'/rss.php');
		gpFiles::Save($path.'/rss.php', $rss);
	}

	/* $zip = "<?php require '$addonCodeFolder/zip.php'; ?>";
	if (!file_exists($path.'/zip.php') || strcmp(file_get_contents($path.'/zip.php'), $zip) != 0) {
		gpFiles::Save($path.'/zip.php', $zip);
	} */
}

SetupPath(null);

$config = "<?php require_once '$addonDataFolder/config.php'; ?>";
if (!file_exists($addonCodeFolder.'/config.php') || strcmp(file_get_contents($addonCodeFolder.'/config.php'), $config) != 0) {
	gpFiles::RmAll($addonCodeFolder.'/config.php');
	gpFiles::Save($addonCodeFolder.'/config.php', $config);
}

?>