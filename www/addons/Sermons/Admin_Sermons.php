<?php

defined('is_running') or die('Not an entry point...');

require_once('Setup.php');

if (isset($_POST['date']) && common::LoggedIn()) {
	$sermonsconfig = array( 'dateformat' => $_POST['dateformat'], 'date' => $_POST['date'], 'topic' => $_POST['topic'], 'preacher' => $_POST['preacher'], 'length' => $_POST['length'], 'listen' => $_POST['listen'],
		'all' => $_POST['all'], 'upload' => $_POST['upload'], 'postonfacebook' => $_POST['postonfacebook'], 'sendmail' => $_POST['sendmail'], 'download' => $_POST['download'], 'utf8encode' => $_POST['utf8encode'] == 'true', 'path' => $_POST['path'], 'podcast' => $_POST['podcast'],
		'podtitle' =>$_POST['podtitle'], 'poddesc' => $_POST['poddesc'], 'podauthor' => $_POST['podauthor'], 'podlink' => $_POST['podlink'], 'podlang' => $_POST['podlang'], 'podimage' => $_POST['podimage'], 'podcopyright' => $_POST['podcopyright'],
		'podemail' => $_POST['podemail'], 'podmax' => $_POST['podmax'], 'podcategory' => $_POST['podcategory'], 'podsubcategory' => $_POST['podsubcategory'], 'podiconlink' => $_POST['podiconlink'], 'podkeywords' => $_POST['podkeywords'],
		'home' => $sermonsconfig['home'], 'homeurl' => $sermonsconfig['homeurl'] , 'homedir' => $sermonsconfig['homedir'], 'version' => $sermonsconfig['version']);
	gpFiles::Save($addonDataFolder.'/config.php', '<?php global $sermonsconfig; ' . gpFiles::ArrayToPHP('sermonsconfig', $sermonsconfig) . '?>');
} else {
	require_once($addonDataFolder.'/config.php');
}

//require_once('Sermons.php');
function SermonsUploadLink($label) { global $sermonsconfig; echo common::Link('Admin_Browser', $sermonsconfig['upload'], 'dir=%2Fsermons'); }

class Admin_Sermons {
	function Admin_Sermons() {
		global $sermonsconfig, $addonRelativeCode, $homeurl, $sermonsversion;
		$rss = str_replace('/./', '/', $homeurl . '/'.'Sermons_Podcast');
		?>
<form action="<?php echo  './Admin_Sermons' ?>" method="post">
	<h1>Sermons Plugin <?php echo $sermonsversion ?></h1>

	<h2>Upload Sermons</h2>

	<p><?php echo  SermonsUploadLink("Upload Sermons"); ?></p>

	<p>
		You can place the mp3 sermons in the folder that is set below (standard is ./data/_upload/sermons, so you can upload sermons with the filemanager, but if you upload with FTP, you might want to set the folder to
				./sermons). The mp3's get published immediately, but for this they need to have valid author & title mp3 tags.
				If the filename contains a date in the format DD.MM.YYYY, DD-MM-YYYY or MM-DD-YYYY & MM.DD.YYYY (If you preset the MM/DD/YYYY format in the settings), then
				this date will be displayed instead of the file modification date.
				You can also place portrait jpg files of the preachers with filenames as the case sensitive names, firstnames or lastnames of the preachers in the sermons folder.
				The portraits will be displayed when listening to a sermon. The images should be at least 200x200px in size for Facebook compatibility.
				If there are subfolders corresponding to the current page's path, then the media files from this folder are used for this page, i.e. if there is a page April/Sermons, and in the sermons folder is a folder
				April containing a folder Sermons, then the sermons on the page April/Sermons are taken from the subfolder April/Sermons.
	</p>

	<h2>Select Date Format</h2>

	<p>
		<select name="dateformat">
			<option <?php if ($sermonsconfig['dateformat'] == 'n/j/Y') { ?>selected <?php } ?>value="n/j/Y">MM/DD/YYYY</option>
			<option <?php if ($sermonsconfig['dateformat'] == 'j.n.Y') { ?>selected <?php } ?>value="j.n.Y">DD.MM.YYYY</option>
		</select>
	</p>

	<h2>Labels</h2>

	<table>
		<tr>
			<td>Date</td>
			<td>
				<input type="text" name="date" value="<?php echo  $sermonsconfig['date'] ?>"/></td>
		</tr>
		<tr>
			<td>Topic</td>
			<td>
				<input type="text" name="topic" value="<?php echo  $sermonsconfig['topic'] ?>" /></td>
		</tr>
		<tr>
			<td>Preacher</td>
			<td>
				<input type="text" name="preacher" value="<?php echo  $sermonsconfig['preacher'] ?>"/></td>
		</tr>
		<tr>
			<td>Length</td>
			<td>
				<input type="text" name="length" value="<?php echo  $sermonsconfig['length'] ?>" /></td>
		</tr>
		<tr>
			<td>Listen</td>
			<td>
				<input type="text" name="listen" value="<?php echo $sermonsconfig['listen'] ?>" /></td>
		</tr>
		<tr>
			<td>All</td>
			<td>
				<input type="text" name="all" value="<?php echo  $sermonsconfig['all'] ?>" /></td>
		</tr>
		<tr>
			<td>Download</td>
			<td>
				<input type="text" name="download" value="<?php echo  $sermonsconfig['download'] ?>" /></td>
		</tr>
		<tr>
			<td>Upload Sermon</td>
			<td>
				<input type="text" name="upload" value="<?php echo  $sermonsconfig['upload'] ?>" /></td>
		</tr>
		<tr>
			<td>Post on Facebook</td>
			<td>
				<input type="text" name="postonfacebook" value="<?php echo  $sermonsconfig['postonfacebook'] ?>" /></td>
		</tr>
		<tr>
			<td>Send as Email</td>
			<td>
				<input type="text" name="sendmail" value="<?php echo  $sermonsconfig['sendmail'] ?>" /></td>
		</tr>
		<tr>
			<td>Podcast</td>
			<td>
				<input type="text" name="podcast" value="<?php echo  $sermonsconfig['podcast'] ?>" /></td>
		</tr>
	</table>

	<p>
		<input type="checkbox" name="utf8encode" value="true" <?php if ($sermonsconfig['utf8encode'] == true) { ?>checked<?php } ?> />
		UTF8 encode filenames (Check on IIS).
	</p>

	<h2>Sermons Folder</h2>
	<p>
		<input type="text" name="path" value="<?php echo $sermonsconfig['path'] ?>" style="width:400px" />
	</p>

	<h2>Podcast</h2>
	<p>The Podcast RSS URL is: <a href="<?php echo $rss ?>"><?php echo $rss ?></a></p>
	<table>
		<tr>
			<td>Title</td>
			<td>
				<input type="text"  style="width:300px" name="podtitle" value="<?php echo  $sermonsconfig['podtitle'] ?>"/></td>
		</tr>
		<tr>
			<td>Description</td>
			<td>
				<textarea style="width: 300px; height: 60px;" name="poddesc"><?php echo  $sermonsconfig['poddesc'] ?></textarea></td>
		</tr>
		<tr>
			<td>Author</td>
			<td>
				<textarea style="width: 300px; height: 60px;" name="podauthor"><?php echo  $sermonsconfig['podauthor'] ?></textarea></td>
		</tr>
		<tr>
			<td>Keywords</td>
			<td>
				<textarea style="width: 300px; height: 60px;" name="podkeywords"><?php echo  $sermonsconfig['podkeywords'] ?></textarea></td>
		</tr>
		<tr>
			<td>
			Home URL<td>
				<input type="text" style="width:300px" name="podlink" value="<?php echo  $sermonsconfig['podlink'] ?>"/></td>
		</tr>
		<tr>
			<td>Language</td>
			<td>
				<input type="text"  style="width:300px" name="podlang" value="<?php echo  $sermonsconfig['podlang'] ?>" /></td>
		</tr>
		<tr>
			<td>Image</td>
			<td>
				<input type="text"  style="width:300px" name="podimage" value="<?php echo  $sermonsconfig['podimage'] ?>" /></td>
		</tr>
		<tr>
			<td>Copyright</td>
			<td>
				<input type="text"  style="width:300px" name="podcopyright" value="<?php echo $sermonsconfig['podcopyright'] ?>" /></td>
		</tr>
		<tr>
			<td>Email</td>
			<td>
				<input type="text"  style="width:300px" name="podemail" value="<?php echo  $sermonsconfig['podemail'] ?>" /></td>
		</tr>
		<tr>
			<td>Max. Entries</td>
			<td>
				<input type="text"  style="width:300px" name="podmax" value="<?php echo $sermonsconfig['podmax'] ?>" /></td>
		</tr>
		<tr>
			<td>Category</td>
			<td>
				<input type="text"  style="width:300px" name="podcategory" value="<?php echo $sermonsconfig['podcategory'] ?>" /></td>
		</tr>
		<tr>
			<td>Subcategory</td>
			<td>
				<input type="text"  style="width:300px" name="podsubcategory" value="<?php echo $sermonsconfig['podsubcategory'] ?>" /></td>
		</tr>
		<tr>
			<td title="The link of the Podcast icon below the sermons, &#10;linking to a page explaining how to setup the podcast on different devices.">Podcast Icon Link <img src="<?php echo $addonRelativeCode.'/question.png' ?>"  /></td>
			<td>
				<input type="text"  style="width:300px" name="podiconlink" value="<?php echo $sermonsconfig['podiconlink'] ?>" /></td>
		</tr>
	</table>

	<p>
		<script type="text/javascript">
				<!--
	function toggle_visibility(id) {
		var e = document.getElementById(id);
		if (e.style.display == 'inline')
			e.style.display = 'none';
		else
			e.style.display = 'inline';
	}
	//-->
		</script>
		<input id="submit" type="submit" value="Save" onclick="toggle_visibility('check');" />
		&nbsp;
		<img id="check" src="<?php echo  $addonRelativeCode.'/loaderB16.gif' ?>" alt="" style="<?php if (!isset($_POST['submit'])) { ?>display:none;<?php } else { ?>display:inline;<?php } ?>" />
	</p>
</form>
<?php
	}
}
?>



