<?php
defined('is_running') or die('Not an entry point...');

gpPlugin::incl('AdminSitePageExtra.php','require_once');

class SitePageExtraSearch extends AdminSitePageExtra{
	function SitePageExtraSearch($args){
		global $langmessage,$gp_titles;
		$this->Init();

		$search = $args[0];
		$label = common::GetLabelIndex('');

		foreach (array(1,2,3) as $position){
			$areas=$this->getAreas($position);
			foreach($areas as $file){
				$title= isset($gp_titles[$file])?$gp_titles[$file]['label']:$file;
				$content=$this->LoadContent($file);
				$search->FindString($content, $title, $file);
			}
		}
	}
}

