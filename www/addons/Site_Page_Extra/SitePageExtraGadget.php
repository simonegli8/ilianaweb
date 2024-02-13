<?php defined('is_running') or die('Not an entry point...');

gpPlugin::incl('AdminSitePageExtra.php','require_once');

class SitePageExtraGadget extends AdminSitePageExtra{

	function SitePageExtraGadget($position){
		$this->Init($position);
		$this->Run();
	}

	function Run(){
		global $gp_titles,$page;

		/* only show on defined pages
		if( !isset($gp_titles[$page->gp_index]) ){
			return;
		}
		*/

		$this->GetPageExtra();	
	}

	function GetPageExtra($name='Side_Menu'){
		global $page,$langmessage,$edit_index;

		if (!isset($page->gp_index)) {
			return;
		}

		$name=$page->gp_index;

		//load page specific extra content
		$extra_content = $this->LoadContent($name,'');
		if (empty($extra_content)) {
			//load default position specific extra content
			$extra_content=$this->LoadContentDefault(null,'');
		}

		//$extra_content = gpPlugin::Filter('GetExtra',array($extra_content,$name));
		$wrap = gpOutput::ShowEditLink('Admin_SitePageExtra');
		$permission = class_exists('admin_tools') && admin_tools::HasPermission('Admin_SitePageExtra');
		if( $wrap && $permission ){
			$edit_link = gpOutput::EditAreaLink($edit_index,'Admin_SitePageExtra',$langmessage['edit'],'file='.$name.'&position='.$this->position,' title="Page Extra '.$this->position.': '.$page->title.'" name="inline_edit_generic" ');

			ob_start();
			echo '<span class="nodisplay" id="ExtraEditLnks'.$edit_index.'">';
			echo $edit_link;
			echo common::Link('Admin_SitePageExtra',$langmessage['edit'].' Default','cmd=editdefault&file='.$name.'&position='.$this->position,' title="Page Extra '.$this->position.': '.$page->title.' Default"');
			echo common::Link('Admin_SitePageExtra','Site Page Extra','',' class="nodisplay"');
			echo '</span>';
			gpOutput::$editlinks .= ob_get_clean();

			echo '<div class="editable_area" id="ExtraEditArea'.$edit_index.'">'; // class="edit_area" added by javascript
			echo !empty($extra_content)?$extra_content:$this->DefaultContent();// '<p>&nbsp;</p'; //for editing menu to show
			echo '</div>';
		}else{
			echo $extra_content;
		}
	}
}

class SitePageExtraGadget1 extends SitePageExtraGadget {
	function SitePageExtraGadget1(){
		$this->SitePageExtraGadget(1);
	}
}
class SitePageExtraGadget2 extends SitePageExtraGadget {
	function SitePageExtraGadget2(){
		$this->SitePageExtraGadget(2);
	}
}
class SitePageExtraGadget3 extends SitePageExtraGadget {
	function SitePageExtraGadget3(){
		$this->SitePageExtraGadget(3);
	}
}
