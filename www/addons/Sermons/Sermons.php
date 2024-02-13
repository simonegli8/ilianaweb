<?php

defined('is_running') or die('Not an entry point...');

date_default_timezone_set("UTC");

global $SermonsData;

require_once('Setup.php');

require_once('Infos.php');

/*
function mb_rawurlencode($url){
	$encoded='';
	$length=mb_strlen($url);
	for($i=0;$i<$length;$i++){
		$encoded.='%'.wordwrap(bin2hex(mb_substr($url,$i,1)),2,'%',true);
	}
	return $encoded;
} */
$SermonsData = SermonsLoad();


if (!isset($page->SermonsScript)) { ?>

<script type="text/javascript">

    var rewriteurl = false;
    var playurl = '';
    var urlparams;

    (window.onpopstate = function () {
        var match,
            pl = /\+/g,  // Regex for replacing addition symbol with a space
            search = /([^&=]+)=?([^&]*)/g,
            decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
            query = window.location.search.substring(1);

        urlparams = {};
        while (match = search.exec(query))
            urlparams[decode(match[1])] = decode(match[2]);
  
        SermonsSelect(false);
    })();
 

    function RewriteUrl() {
        if (rewriteurl) {
            var url = window.location.protocol + "//" + window.location.host + window.location.pathname;
            var first = true;
            for (var key in urlparams) {
                if (urlparams.hasOwnProperty(key)) {
                    url += (first ? '?' : '&') + key + '=' + urlparams[key];
                }
                first = false;
            }
            playurl = url;
            if (window.location.href != url) window.history.pushState(null, null, url)
        }
    }

	function SermonsResetRows() {
		var rows = document.getElementsByClassName("Sermons-Table-AudioRow");
		for (var i = 0; i < rows.length; i++) {
			rows[i].style.display = "none";
		};
		var audios = document.getElementsByClassName("Sermons-Table-Audio");
		for (var i = 0; i < audios.length; i++) {
			if (!audios[i].paused) audios[i].pause();
		};
	}

	function SermonsPlay(id) {
	    rewriteurl = true;

	    var rowid = "Sermons-Table-AudioRow-" + id;
		var row = document.getElementById(rowid);
		var cell = row.firstElementChild;
		var audio = null;
		var e = cell.getElementsByClassName("audio");
		if (e.length == 1) {
			var link = e[0];
			link.className = '';
			var next = link.nextSibling;
			cell.removeChild(link);
			audio = document.createElement('AUDIO');
			audio.className = "Sermons-Table-Audio";
			audio.controls = true;
			audio.src = link.href;
			audio.style.verticalAlign = "middle";
			var p = document.createElement('P');
			p.appendChild(link);
			audio.appendChild(p);
			cell.insertBefore(audio, next);
		} else {
			audio = cell.getElementsByTagName('AUDIO')[0];
		}
		e = cell.getElementsByClassName("fb-share-button");
		if (e.length == 1) {
		    var div = e[0];
		    div.attributes["data-href"] = playurl;
		}

		if (row.style.display == 'none') {
			SermonsResetRows();
			row.style.display = '';
			audio.play();
			urlparams["play"] = id;
		} else {
			row.style.display = 'none';
			audio.pause();
			delete urlparams["play"];
		}
	    RewriteUrl();
	}

	function SermonsSelect(postback) {
	    SermonsResetRows();

	    var author = document.getElementById("Sermons-Authorfilter");
	    var year = document.getElementById("Sermons-Yearfilter");
	    var rows = document.getElementsByClassName("Sermons-Table-InfoRow");

	    if (author && year && rows) {
	        if (!postback) {
	            if ("author" in urlparams) author.value = urlparams["author"];
	            else { rewriteurl = true; urlparams["author"] = author.value; }
	            if ("year" in urlparams) year.value = urlparams["year"];
	            else { rewriteurl = true; urlparams["year"] =year.value; }
	        } else {
	            rewriteurl = !("author" in urlparams || "year" in urlparams) || urlparams["author"] != author.value || urlparams["year"] != year.value;
	            urlparams["author"] = author.value;
	            urlparams["year"] = year.value;
	        }

	        for (var i = 0; i < rows.length; i++) {
	            var html = rows[i].innerHTML;
	            if ((author.value == '*' || html.indexOf(author.value) > -1) && (year.value == '*' || html.indexOf(year.value) > -1)) rows[i].style.display = '';
	            else rows[i].style.display = 'none';
	        };

	        if ("play" in urlparams) SermonsPlay(urlparams["play"]);
	        else RewriteUrl();
	    }
	}

	function SermonsSend(url, title) {
	    window.open('mailto:?body=' + title + escape('\r\n\r\n') + url, '_top');
	}

	function SermonsRemovePlay(url) {
		return url.replace(/(&play=[^&]+)|(\?play=[^&]+$)/, "").replace(/(\?play=[^&]+&)/, "?");
	}
</script>

<?php
		 $page->SermonsScript = true;
	 }

function SermonsPlaytime($seconds) {
	echo floor($seconds/3600).':';
	echo sprintf("%02s", round($seconds/60)%60);
}

global $id, $url;
$id = 0;

function SermonsRender($table) {

	global $sermonsconfig, $SermonsData, $addonRelativeCode;

	$infos = $SermonsData['infos'];
	$speakers = $SermonsData['speakers'];
	$years = $SermonsData['years'];

	//$path_only = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

?>

<form>
	<table class="Sermons-Table">
		<?php if ($table) {
			$year = array_shift($years); ?>
		<thead>
			<tr>
				<td style="padding: 0 10px 0 10px;vertical-align:top;"><?php echo $sermonsconfig['date']; ?>
					<select id="Sermons-Yearfilter" style="width: 150px" onchange="SermonsSelect(true);">
						<option value="*"><?php echo $sermonsconfig['all'] ?></option>
						<option selected value="<?php echo  $year ?>"><?php echo  $year ?></option>
						<?php foreach ($years as $year) { ?>
						<option value="<?php echo  $year ?>"><?php echo  $year ?></option>
						<?php } ?>
					</select>
				</td>
				<td style="padding: 0 10px 0 10px;vertical-align:top;"><?php echo $sermonsconfig['topic'] ?></td>
				<td style="padding: 0 10px 0 10px;vertical-align:top;"><?php echo $sermonsconfig['preacher'] ?>
					<select id="Sermons-Authorfilter" style="width: 150px" onchange="SermonsSelect(true);">
						<option value="*"><?php echo $sermonsconfig['all'] ?></option>
						<?php foreach ($speakers as $speaker) { ?>
						<option value="<?php echo  $speaker ?>"><?php echo  $speaker ?></option>
						<?php } ?>
					</select>
				</td>
				<td style="padding: 0 10px 0 10px;vertical-align:top;"><?php echo $sermonsconfig['length'] ?></td>
			</tr>
		</thead>
		<?php }
			global $pageurl, $page;
			if (!isset($page->SermonsGadget)) $page->SermonsGadget = -1;
			$page->SermonsGadget++;
			if ($page->SermonsGadget > 0) $g = $page->SermonsGadget . '_';
			else $g = '';
			$img = $sermonsconfig['podimage'];

		foreach ($infos as $info) {
			$name = str_replace(' ', '_', $info['title']);
			$name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
			$id =  $g . date('Ymd', $info['time']) . '_' . $name;
			$rowid = "Sermons-Table-AudioRow-".$id;
			$src = HrefEncode($info['path']);
			if (isset($_GET['play'])) {
				$playid = $_GET['play'];
				if ($playid == $id) {
					$titlename = $info['title'] . ', ' . $info['speaker'];
					if (!strstr($page->label, $titlename)) {
						$page->label = $page->title .= ' - ' . $titlename;
						if (!isset($page->TitleInfo['keywords']) || $page->TitleInfo['keywords'] == '') {
							$page->TitleInfo['keywords'] = $titlename;
						} else {
							$page->TitleInfo['keywords'] .= ', ' . $titlename;
						}
						if (!isset($page->TitleInfo['description']) || $page->TitleInfo['description'] == '') {
							$page->TitleInfo['description'] = $titlename . ', ' . date($sermonsconfig['dateformat'], $info['time']) . '.';
						} else {
							$page->TitleInfo['description'] = $titlename . ', ' . date($sermonsconfig['dateformat'], $info['time']) . '. ' . $page->TitleInfo['description'];						
						}
						if (isset($info['authorimg'])) $img = HrefEncode($info['authorimg']);
					}
				}
			}
?>

		<tr class="<?php echo  $table ? 'Sermons-Table-InfoRow' : 'Sermons-Table-CurrentRow' ?>">
			<?php if ($table) { ?>
			<td style="padding: 0 10px 0 10px;"><a style="display:block;width:100%;height:100%;text-decoration:none;text-align:right;" href='javascript:SermonsPlay("<?php echo  $id ?>")'><?php echo  date($sermonsconfig['dateformat'], $info['time']) ?></a></td>
			<td style="padding: 0 10px 0 10px;"><a style="display:block;width:100%;height:100%;text-decoration:none;" href='javascript:SermonsPlay("<?php echo  $id ?>")'><?php echo  $info['title'] ?></a></td>
			<td style="padding: 0 10px 0 10px;"><a style="display:block;width:100%;height:100%;text-decoration:none;" href='javascript:SermonsPlay("<?php echo  $id ?>")'><?php echo  $info['speaker'] ?></a></td>
			<td style="padding: 0 10px 0 10px;"><a style="display:block;width:100%;height:100%;text-decoration:none;" href='javascript:SermonsPlay("<?php echo  $id ?>")'><?php echo  SermonsPlaytime($info['playtime_seconds']) ?></a></td>
			<td></td>
			<?php } else { ?>
			<td style="padding: 0 10px 0 10px;"><a style="display:block;width:100%;height:100%;text-decoration:none;" href='javascript:SermonsPlay("<?php echo  $id ?>")'><?php echo  $info['title'].', '.$info['speaker'] ?></a></td>
			<td></td>
			<td></td>
			<td></td>
			<?php } ?>
		</tr>
		<tr id="<?php echo  $rowid ?>"  class="Sermons-Table-AudioRow" style="display:none;">
			<td colspan="4">
				<?php if (isset($info['authorimg'])) { ?>
				<img style="margin: 3px; vertical-align:middle; max-height: 64px; border-radius:5px;" alt="" src="<?php echo  HrefEncode($info['authorimg']) ?>" />
				<?php } ?>
				<a href="<?php echo  $src ?>" class="audio" style="text-decoration:none; vertical-align:middle;"><?php echo $sermonsconfig['listen'] ?></a>
				<a href="<?php echo  $src ?>" class="download" style="text-decoration:none; vertical-align:middle;" download>
					<img src="<?php echo $addonRelativeCode.'/download.png' ?>" alt="<?php echo $sermonsconfig['download'] ?>" style="margin:3px 0px; vertical-align:middle;" title="<?php echo $sermonsconfig['download'] ?>"/>
				</a>
                <a href="javascript:SermonsSend(encodeURIComponent(location.href), '<?php echo $info['title'] . ', ' . $info['speaker'] ?>')" style="text-decoration:none; vertical-align:middle;">
                    <img src="<?php echo $addonRelativeCode.'/email.png' ?>" alt="<?php echo $sermonsconfig['sendmail'] ?>" style="margin:3px 0px; vertical-align:middle;" title="<?php echo $sermonsconfig['sendmail'] ?>" />
                </a>
                <a href="javascript:window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href), '_blank')" style="text-decoration:none; vertical-align:middle;">
                    <img src="<?php echo $addonRelativeCode.'/facebookblue.png' ?>" alt="<?php echo $sermonsconfig['postonfacebook'] ?>" style="margin:3px 0px; vertical-align: middle;" title="<?php echo $sermonsconfig['postonfacebook'] ?>" />
                </a>
			</td>
		</tr>
		<?php
					if (!$table) break;
				} ?>
	</table>
	<?php


	if ($table) {
		$page->head .= "\r\n".'<link rel="image_src" href="' . $img . '" />' .
			"\r\n<meta property='og:url' content='" . $pageurl . "' />" .
			"\r\n<meta property='og:type' content='website' />" .
			"\r\n<meta property='og:title' content='" . $page->title . "' />" .
			"\r\n<meta property='og:image' content='" . $img . "' />";
		if (isset($page->TitleInfo['description'])) $page->head .= "\r\n<meta property='og:description' content='" . $page->TitleInfo['description'] . "' />";

		$podiconlink = $sermonsconfig['podiconlink'];
		if ($podiconlink == '')  { $podiconlink = $sermonsconfig['path'].'/rss.php'; }
	?>
	<a href="<?php echo HrefEncode($podiconlink) ?>">
		<img id="check" src="<?php echo  $addonRelativeCode.'/podcast.png' ?>" title="<?php echo $sermonsconfig['podcast'] ?>" style="margin:3px 0px; vertical-align: middle;" /></a>
	<a href="<?php echo HrefEncode($sermonsconfig['path'].'/zip.php') ?>" style="display:none;" download>
		<img id="zip" src="<?php echo  $addonRelativeCode.'/download.png' ?>" title="<?php echo $sermonsconfig['download'] ?>" style="margin:3px 0px; vertical-align: middle;" /></a>
	<a href="javascript:SermonsSend(encodeURIComponent(SermonsRemovePlay(location.href)), '<?php echo $page->title ?>')" style="text-decoration:none;">
        <img src="<?php echo $addonRelativeCode.'/email.png' ?>" alt="<?php echo $sermonsconfig['sendmail'] ?>" style="margin:3px 0px; vertical-align:middle;" title="<?php echo $sermonsconfig['sendmail'] ?>" /></a>
    <a href="javascript:window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(SermonsRemovePlay(location.href)), '_blank')">
        <img src="<?php echo $addonRelativeCode.'/facebookblue.png' ?>" alt="<?php echo $sermonsconfig['postonfacebook'] ?>" style="margin:3px 0px; vertical-align: middle;" title="<?php echo $sermonsconfig['postonfacebook'] ?>" /></a>
	<a href="http://www.johnshope.com/plugins/sermons" style="font-size:0.8em; color:lightgray; text-decoration:none; display:none;">&nbsp; &copy; johnshope.com</a>
    <h1 style="display:none;">johnshope_sermons_plugin</h1>
	<?php } ?>
	<script type="text/javascript">
	    SermonsSelect(false);
   	</script>
</form>
<?php
	global $page;

	$rsslink =  "\r\n" .'<link rel="alternate" type="application/rss+xml" title="'.$sermonsconfig['podtitle'].'" href="'.HrefEncode($sermonsconfig['path'].'/rss.php').'" />';
	if (strpos($page->head, $rsslink) == false) $page->head .= $rsslink;
}

function SermonsRenderPlayer() { SermonsRender(true); }

function SermonsRenderPlayerRecent() { SermonsRender(false); }

?>
