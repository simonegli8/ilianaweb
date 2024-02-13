<?php

if (!defined('is_running')) {
  define('is_running', true);
  define('no_cms', true);
}

if (isset($addonPathData)) require_once($addonPathData.'/config.php');
else require_once(dirname(__FILE__).'/config.php');

function XEncode($s) {
	$s = str_replace('&', '&amp;', $s);
	$s = str_replace('<', '&lt;', $s);
	$s = str_replace('>', '&gt;', $s);
	$s = str_replace('\'', '&apos;', $s);
	$s = str_replace('"', '&quot;', $s);
	return $s;
}
require_once('Home.php');
require_once('Infos.php');

$max_feed = $sermonsconfig['podmax'];                                                                          //Maximum number of files to display in the feed
if ($max_feed == '') $max_feed = -1;
$tag_title = $sermonsconfig['podtitle'];                                                     //The Podcast Title
$tag_description = $sermonsconfig['poddesc'];                                          //The Poddcast Description
$tag_author = $sermonsconfig['podauthor'];
$tag_keywords = $sermonsconfig['podkeywords'];
$tag_image = $sermonsconfig['podimage'];
$tag_link = $sermonsconfig['podlink'];                                        //The URL for the person providing the podcast
$tag_lang = $sermonsconfig['podlang'];                                                                     //Language Tag
$tag_copyright = $sermonsconfig['podcopyright'];                                              //Copyright
$tag_owneremail = $sermonsconfig['podemail'];                                         //Author's email
$tag_category = $sermonsconfig['podcategory'];
$tag_subcategory = $sermonsconfig['podsubcategory'];
$tag_generator= 'Sermons gpEasy Plugin '.$sermonsversion;                        //Generator name (This script!)
$tag_ttl= 60;                                                                            //Podcast Time to Live

chdir($sermonsconfig['homedir']);

$path = $sermonsconfig['path'];      //Absolute reference of where the MP3's are stored on the server

/* Loop through the folder and compile the body tags in a string for each MP3 */
$infos = SermonsLoad($path)['infos'];



if (count($infos) == 0) {
	$date = date('r');
} else {
	$date = date('r', $infos[0]['time']);
}

// $url = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

header('Content-type: text/xml; charset=utf-8', true);

echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
echo "<rss version='2.0' xmlns:itunes='http://www.itunes.com/dtds/podcast-1.0.dtd'>\n";
echo "	<channel>\n";
echo "		<title>".XEncode($tag_title)."</title>\n";
echo "		<link>".XEncode($tag_link)."</link>\n";
echo "		<description><![CDATA[".$tag_description."]]></description>\n";
echo "		<itunes:summary><![CDATA[".$tag_description."]]></itunes:summary>\n";
echo "		<itunes:author>".XEncode($tag_author)."</itunes:author>\n";
echo "		<itunes:keywords>".XEncode($tag_keywords)."</itunes:keywords>\n";
echo "		<lastBuildDate>" . $date ."</lastBuildDate>\n";
echo "		<language>".$tag_lang."</language>\n";
echo "      <itunes:clean>yes</itunes:clean>\n";
echo "      <itunes:explicit>no</itunes:explicit>\n";
echo "		<copyright>".XEncode($tag_copyright)."</copyright>\n";
echo "		<itunes:owner>\n";
echo "			<itunes:name>".XEncode($tag_author)."</itunes:name>\n";
echo "			<itunes:email>".$tag_owneremail."</itunes:email>\n";
echo "		</itunes:owner>\n";
echo "		<itunes:category text=\"".XEncode($tag_category)."\">\n";
echo "			<itunes:category text=\"".XEncode($tag_subcategory)."\" />\n";
echo "		</itunes:category>\n";
echo "		<generator>".XEncode($tag_generator)."</generator>\n";
echo "		<webMaster>".XEncode($tag_owneremail)."</webMaster>\n";
if (strlen($tag_image) > 0) {
	if (!startsWith($tag_image, 'http')) $tag_image = 'http://'.$tag_image;
	echo "		<image>\n			<link>".$tag_link."</link>\n			<title>".$tag_title."</title>\n			<url>".HrefEncode($tag_image)."</url>\n		</image>\n";
	echo "		<itunes:image href='".HrefEncode($tag_image)."' />\n";
}
echo "		<ttl>".$tag_ttl."</ttl>\n";

while ($info = each($infos)[1] AND $max_feed != 0) {
	echo "		<item>\n";
	echo "			<title>".XEncode($info['title0'])."</title>\n";
	echo "			<link>". HrefEncode($info['path']) ."</link>\n";
    echo "          <guid isPermaLink='true'>". HrefEncode($info['path']) ."</guid>\n";
	echo "			<description>" . XEncode($info['speaker0']) ."</description>\n";
	echo "			<itunes:subtitle>". XEncode($info['speaker0']) ."</itunes:subtitle>\n";
	echo "			<pubDate>".date('r', $info['time'])."</pubDate>\n";
	echo "			<enclosure url='". HrefEncode($info['path']) ."' length='".$info['filesize']."' type='audio/mpeg' />\n";	// Training slash for XML
	echo "			<itunes:duration>".round($info['playtime_seconds'])."</itunes:duration>\n";
	if (isset($info['authorimg'])) {
		$img = $info['authorimg'];
		if (startsWith($img, './')) $img = substr($img, 1, strlen($img)-1);
		echo "			<itunes:image href=\"". HrefEncode($img) ."\" />\n"; }
	echo "		</item>\n";
	$max_feed--;
}

echo "	</channel>\n</rss>\n";
?>