<?php
defined('is_running') or die('Not an entry point...');

class AdminSitePageExtra{

	var $folder;
	var $areas = array();
	var $postiion;
	function Init($position=1){
		global $langmessage, $dataDir;
		global $addonPathData;

		//$this->folder = $addonPathData.'/_extra';'case
		//$this->permission = admin_tools::HasPermission('Admin_SitePageExtra');
		$this->folder = $dataDir.'/data/_extra/_page';
		$this->position=array_key_exists('position',$_REQUEST) && isset($_REQUEST['position'])?(int)$_REQUEST['position']:$position;
		$this->areas = $this->getAreas();//gpFiles::ReadDir($this->getFolder());
		//asort($this->areas);
	}

	function AdminSitePageExtra(){
		$this->Init();
		$cmd = common::GetCommand();

		$show = true;
		switch($cmd){
			case 'delete';
				$this->DeleteArea();
			break;

			case 'save_inline': //gpEasy 4.5+
			case 'save':
				if( $this->SaveExtra() ){
					break;
				}
			case 'edit':
				if( $this->EditExtra() ){
					$show = false;
				}
			break;

			case 'deletedefault';
				$this->DeleteExtraDefault();
			break;
			case 'savedefault':
				if( $this->SaveExtraDefault() ){
					break;
				}
			case 'editdefault':
				if( $this->EditExtraDefault() ){
					$show = false;
				}
			break;

			case 'rawcontent':
				//$this->RawContent();
			break;

			case 'inlineedit':
				$this->InlineEdit();
			die();

		}

		if( $show ){
			$this->ShowExtras();
		}
	}


	function InlineEdit(){
		$title = gp_edit::CleanTitle($_REQUEST['file']);
		if( empty($title) ){
			echo 'false';
			return false;
		}

		$data = array();
		$data['type'] = 'text';
		//$data['content'] = '<p> </p>';
		$data['content']=$this->LoadContent($title);
/*
		$file = $this->getFolder().'/'.$title.'.php';
		$content = '';

		if( file_exists($file) ){
			ob_start();
			include($file);
			$data['content'] = ob_get_clean();
		}

		if (isset($_REQUEST['default']) && $_REQUEST['default']==1) {
			$data['content']=$this->LoadContentDefault(null,$this->DefaultContent());
			$data['default']=True;
		}else {
			$data['content']=$this->LoadContent($title);
			$data['default']=False;
		}
*/

		includeFile('tool/ajax.php');
		gpAjax::InlineEdit($data);
	}

	/**
	 * Send the content of the extra area to the client in a json response
	 *
	 */
	function RawContent(){
		global $page,$langmessage;

		//for ajax responses
		$page->ajaxReplace = array();

		$title = gp_edit::CleanTitle($_REQUEST['file']);
		if( empty($title) ){
			message($langmessage['OOPS']);
			return false;
		}
/*
		$file = $this->getFolder().'/'.$title.'.php';
		$content = '';

		if( file_exists($file) ){
			ob_start();
			include($file);
			$content = ob_get_clean();
		}
*/
		$content=$this->LoadContent($title);

		$page->ajaxReplace[] = array('rawcontent','',$content);
	}

	function DefaultContent(){
		return '<p>&nbsp;</p>';
	}
	/**
	 * Delete an extra content area
	 *
	 */
	function DeleteArea(){
		global $langmessage;

		$title =& $_POST['file'];
		$file = $this->ExtraExists($title);
		if( !$file ){
			message($langmessage['OOPS'].'aoeu');
			return;
		}

		if( unlink($file) ){
			message($langmessage['SAVED']);
			unset($this->areas[$title]);
		}else{
			message($langmessage['OOPS']);
		}
	}
	/**
	 * Delete an extra content area
	 *
	 */
	function DeleteExtraDefault(){
		global $langmessage;

		$position=$this->position;
		if( !$position){
			message($langmessage['SAVED']);
			return;
		}
		$file = $this->getFileDefault($position);

		if( unlink($file) ){
			message($langmessage['SAVED']);
		}else{
			message($langmessage['OOPS']);
		}
	}

	/**
	 * Check to see if the extra area exists
	 *
	 */
	function ExtraExists($file){
		global $dataDir;

		if( !isset($this->areas[$file]) ){
			return false;
		}

		return $this->getFolder().'/'.$file.'.php';
	}


	function ShowExtras(){
		global $langmessage,$gp_titles;

		echo '<h2>'.$langmessage['theme_content'].'</h2>';
	foreach (array(1,2,3) as $position){

		echo '<h3>Position '.$position.'</h3>';
		echo '<table class="bordered full_width">';
		echo '<tr>';
			echo '<th>';
			echo 'Page';
			echo '</th>';
			echo '<th>';
			echo '&nbsp;';
			echo '</th>';
			echo '<th>';
			echo $langmessage['options'];
			echo '</th>';
			echo '</tr>';
		echo '<tr>';
			echo '<td style="white-space:nowrap">';
			echo '(Default)';
			echo '</td>';
			echo '<td>';
			$contents = $this->LoadContentDefault($position,'');
			$contents = strip_tags($contents);
			echo strlen($contents)>0&&!empty($contents)?'<span class="admin_note">"'.substr($contents,0,50).'..."</span></td>':'(blank) </td>';
			echo '<td style="white-space:nowrap">';
			echo common::Link('Admin_SitePageExtra',$langmessage['edit'],'cmd=editdefault&position='.$position);
			echo ' &nbsp; ';
			if (!empty($contents)) {
				$title = sprintf($langmessage['generic_delete_confirm'],'default extra content for Position '.$position);
				echo common::Link('Admin_SitePageExtra',$langmessage['delete'],'cmd=deletedefault&&position='.$position,'name="postlink" class="gpconfirm" title="'.$title.'"');
			}
			echo '</td>';
			echo '</tr>';

		$areas=$this->getAreas($position);
		//asort($areas);
		foreach($areas as $file){
			$extraName = (isset($gp_titles[$file]) && isset($gp_titles[$file]['label']))?$gp_titles[$file]['label']:$file;

			echo '<tr>';
				echo '<td style="white-space:nowrap">';
				echo $extraName;
				echo '</td>';
				echo '<td>"<span class="admin_note">';
				$full_path = $this->getFolder($position).'/'.$file.'.php';
				$contents = file_get_contents($full_path);
				$contents = strip_tags($contents);
				echo substr($contents,0,50);
				echo '</span>..."</td>';
				echo '<td style="white-space:nowrap">';
				echo common::Link('Admin_SitePageExtra',$langmessage['edit'],'cmd=edit&file='.$file.'&position='.$position);
				echo ' &nbsp; ';

				$title = sprintf($langmessage['generic_delete_confirm'],'extra content for page '.$extraName);
				echo common::Link('Admin_SitePageExtra',$langmessage['delete'],'cmd=delete&file='.$file.'&position='.$position,'name="postlink" class="gpconfirm" title="'.$title.'"');
				echo '</td>';
				echo '</tr>';
		}

		echo '</table>';
	}
/*
		echo '<p>';
		echo '<form action="'.common::GetUrl('Admin_SitePageExtra').'" method="post">';
		echo '<input type="hidden" name="cmd" value="edit" />';
		echo '<input type="text" name="file" value="" size="15" class="gpinput"/> ';
		echo '<input type="submit" name="" value="'.$langmessage['Add New Area'].'" class="gpsubmit"/>';
		echo '</form>';
		echo '</p>';
*/


	}


	function EditExtra(){
		global $langmessage;

		$title = gp_edit::CleanTitle($_REQUEST['file']);
		if( empty($title) ){
			message($langmessage['OOPS']);
			return false;
		}

/*
		$file = $this->getFolder().'/'.$title.'.php';
		$content = '<p>empty</p>';//'';

		if( file_exists($file) ){
			ob_start();
			include($file);
			$content = ob_get_clean();
		}
*/
		$content=$this->LoadContent($title);

		echo '<form action="'.common::GetUrl('Admin_SitePageExtra','file='.$title.'&position='.$this->position).'" method="post">';
		echo '<h2>';
		echo common::Link('Admin_SitePageExtra',$langmessage['theme_content']);
		echo ' &gt; '.str_replace('_',' ',$title).'</h2>';
		echo '<input type="hidden" name="cmd" value="save" />';

		gp_edit::UseCK($content);

		echo '<input type="submit" name="" value="'.$langmessage['save'].'" class="gpsubmit" />';
		echo '<input type="submit" name="cmd" value="'.$langmessage['cancel'].'" class="gpcancel"/>';
		echo '</form>';
		return true;
	}

	function EditExtraDefault(){
		global $langmessage;

		$content=$this->LoadContentDefault(null,$this->DefaultContent());

		echo '<form action="'.common::GetUrl('Admin_SitePageExtra','position='.$this->position).'" method="post">';
		echo '<h2>';
		echo common::Link('Admin_SitePageExtra',$langmessage['theme_content']);
		echo ' &gt; Position '.$this->position.' Default</h2>';
		echo '<input type="hidden" name="cmd" value="savedefault" />';

		gp_edit::UseCK($content);

		echo '<input type="submit" name="" value="'.$langmessage['save'].'" class="gpsubmit" />';
		echo '<input type="submit" name="cmd" value="'.$langmessage['cancel'].'" class="gpcancel"/>';
		echo '</form>';
		return true;
	}

	/**
	 * Save the posted content for an extra content area
	 *
	 */
	function SaveExtra(){
		global $langmessage,$page;

		//for ajax responses
		$page->ajaxReplace = array();


		if( empty($_REQUEST['file']) ){
			message($langmessage['OOPS']);
			return false;
		}

		$title = gp_edit::CleanTitle($_REQUEST['file']);
		$file = $this->getFolder().'/'.$title.'.php';
		$text =& $_POST['gpcontent'];
		gpFiles::cleanText($text);


		if( !gpFiles::SaveFile($file,$text) ){
			message($langmessage['OOPS']);
			$this->EditExtra();
			return false;
		}

		$page->ajaxReplace[] = array('ck_saved','','');
		message($langmessage['SAVED']);
		$this->areas[$title] = $title;
		return true;
	}
	function SaveExtraDefault(){
		global $langmessage,$page;

		//for ajax responses
		$page->ajaxReplace = array();

		$file = $this->getFileDefault();
		$text =& $_POST['gpcontent'];
		gpFiles::cleanText($text);

		if( !gpFiles::SaveFile($file,$text) ){
			message($langmessage['OOPS']);
			$this->EditExtra();
			return false;
		}

		$page->ajaxReplace[] = array('ck_saved','','');
		message($langmessage['SAVED']);
		return true;
	}

	function getAreas($position=null){
		return gpFiles::ReadDir($this->getFolder($position));
	}
	function getFolder($position=null){
		if ($position==null)
			$position=$this->position;
		return $this->folder.'/'.$position;
	}
	function getFileDefault($position=null){
		if ($position==null)
			$position=$this->position;
		return $this->getFolder('_default').'/'.$position.'.php';
	}
	function LoadContent($title, $content='<p>&nbsp;</p>'){
		$file = $this->getFolder().'/'.$title.'.php';

		if( file_exists($file) ){
			ob_start();
			include($file);
			$content = ob_get_clean();
		}
		return $content;
	}
	function LoadContentDefault($position=null, $content){
		if ($position==null)
			$position=$this->position;
		$file = $this->getFileDefault($position);

		if( file_exists($file) ){
			ob_start();
			include($file);
			$content = ob_get_clean();
		}
		return $content;
	}
}
