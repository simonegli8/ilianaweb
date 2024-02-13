<?php
/**
 * PHP version 5
 *
 * @package    UASparser
 * @author     Jaroslav Mallat (http://mallat.cz/)
 * @copyright  Copyright (c) 2008 Jaroslav Mallat
 * @copyright  Copyright (c) 2010 Alex Stanev (http://stanev.org)
 * @version    0.4.2 beta
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link       http://user-agent-string.info/download/UASparser
 */

// view source this file and exit
// if ($_GET['source'] == "y") { 	show_source(__FILE__); 	exit; }

class UASparser 
{
	public $InfoUrl   		= 'http://user-agent-string.info';
	public $cache_dir       	= null;

	private $_data    		= array();
	private $_ret    		= array();
	private $test			= null;
	private $id_browser		= null;
	private $os_id			= null;
	
	public function __construct() {	
		global $addonPathData;
		$this->cache_dir = $addonPathData;
		$this->_data = $this->_loadData();
	}
	
	public function Parse($useragent = null) {
		$this->_ret['typ']			= 'unknown';
		$this->_ret['ua_family']		= 'unknown';
		$this->_ret['ua_name']		= 'unknown';
		$this->_ret['ua_url']			= 'unknown';
		$this->_ret['ua_company']		= 'unknown';
		$this->_ret['ua_company_url']		= 'unknown';
		$this->_ret['ua_icon']		= 'unknown.png';
		$this->_ret["ua_info_url"]		= 'unknown';
		$this->_ret["os_family"]		= 'unknown';
		$this->_ret["os_name"]		= 'unknown';
		$this->_ret["os_url"]			= 'unknown';
		$this->_ret["os_company"]		= 'unknown';
		$this->_ret["os_company_url"]		= 'unknown';
		$this->_ret["os_icon"]		= 'unknown.png';
		if (!isset($useragent)) {
			$useragent = $_SERVER['HTTP_USER_AGENT'];
		}

		if ($useragent=='')
			return $this->_ret;
		if (!file_exists($this->cache_dir.'/uasdata.ini'))
			return $this->_ret;
		//echo 'useragent='.$useragent.'<br>';
		//$this->_data = $this->_loadData();
		if($this->_data) {

			// crawler
			foreach ($this->_data['robots'] as $this->test) {
				if ($this->test[0] == $useragent) {
					$this->_ret['typ']										= 'Robot';
					if ($this->test[1]) $this->_ret['ua_family']							= $this->test[1];
					if ($this->test[2]) $this->_ret['ua_name']								= $this->test[2];
					if ($this->test[3]) $this->_ret['ua_url']								= $this->test[3];
					if ($this->test[4]) $this->_ret['ua_company']							= $this->test[4];
					if ($this->test[5]) $this->_ret['ua_company_url']						= $this->test[5];
					if ($this->test[6]) $this->_ret['ua_icon']								= $this->test[6];
					if ($this->test[7]) { // OS set
						if ($this->_data['os'][$this->test[7]][0]) $this->_ret['os_family'] 		= $this->_data['os'][$this->test[7]][0];
						if ($this->_data['os'][$this->test[7]][1]) $this->_ret['os_name']			= $this->_data['os'][$this->test[7]][1];
						if ($this->_data['os'][$this->test[7]][2]) $this->_ret['os_url']			= $this->_data['os'][$this->test[7]][2];
						if ($this->_data['os'][$this->test[7]][3]) $this->_ret['os_company']		= $this->_data['os'][$this->test[7]][3];
						if ($this->_data['os'][$this->test[7]][4]) $this->_ret['os_company_url']	= $this->_data['os'][$this->test[7]][4];
						if ($this->_data['os'][$this->test[7]][5]) $this->_ret['os_icon']			= $this->_data['os'][$this->test[7]][5];
					}
					if ($this->test[8]) $this->_ret['ua_info_url']							= $this->InfoUrl.$this->test[8];
					return $this->_ret;
				}
			}
			
			// browser
			foreach ($this->_data['browser_reg'] as $this->test) {
				if (@preg_match($this->test[0],$useragent,$info)) { // $info contains version
					$this->id_browser = $this->test[1];
					break;
		  		}
	 		}
			if (($this->id_browser)) { // browser detail
				if ($this->_data['browser_type'][$this->_data['browser'][$this->id_browser][0]][0]) $this->_ret['typ']	= $this->_data['browser_type'][$this->_data['browser'][$this->id_browser][0]][0];
				if ($this->_data['browser'][$this->id_browser][1]) $this->_ret['ua_family']						= $this->_data['browser'][$this->id_browser][1];
//				if ($info[2]) { //it's inside
//					$this->_ret["ua_name"] = $this->_data['browser'][$this->id_browser][1].' '.$info[3].' ('.$info[1].' '.$info[2].' inside)';
//			  	} 
//				else {
					$this->_ret['ua_name'] = $this->_data['browser'][$this->id_browser][1].       (isset($info[1]) ? ' '.$info[1]:'');
//				}
				//echo'<pre>'; print_r($info); print_r($this->_ret); echo'</pre>';

				if ($this->_data['browser'][$this->id_browser][2]) $this->_ret['ua_url']							= $this->_data['browser'][$this->id_browser][2];
				if ($this->_data['browser'][$this->id_browser][3]) $this->_ret['ua_company']						= $this->_data['browser'][$this->id_browser][3];
				if ($this->_data['browser'][$this->id_browser][4]) $this->_ret['ua_company_url']					= $this->_data['browser'][$this->id_browser][4];
				if ($this->_data['browser'][$this->id_browser][5]) $this->_ret['ua_icon']							= $this->_data['browser'][$this->id_browser][5];
				if ($this->_data['browser'][$this->id_browser][6]) $this->_ret['ua_info_url']						= $this->InfoUrl.$this->_data['browser'][$this->id_browser][6];
			}
			
			// browser OS
			if (isset($this->_data['browser_os'][$this->id_browser])) { // os detail
				$this->os_id = $this->_data['browser_os'][$this->id_browser][1];
				if ($this->_data['os'][$this->os_id][0]) $this->_ret['os_family'] 		= $this->_data['os'][$this->os_id][0];
				if ($this->_data['os'][$this->os_id][1]) $this->_ret['os_name']			= $this->_data['os'][$this->os_id][1];
				if ($this->_data['os'][$this->os_id][2]) $this->_ret['os_url']			= $this->_data['os'][$this->os_id][2];
				if ($this->_data['os'][$this->os_id][3]) $this->_ret['os_company']		= $this->_data['os'][$this->os_id][3];
				if ($this->_data['os'][$this->os_id][4]) $this->_ret['os_company_url']	= $this->_data['os'][$this->os_id][4];
				if ($this->_data['os'][$this->os_id][5]) $this->_ret['os_icon']			= $this->_data['os'][$this->os_id][5];
				return $this->_ret;
			}
			foreach ($this->_data['os_reg'] as $this->test) {
				if (@preg_match($this->test[0],$useragent)) {
					$this->os_id = $this->test[1];
					break;
		  		}
	 		}
			if ($this->os_id) { // os detail
				if ($this->_data['os'][$this->os_id][0]) $this->_ret['os_family'] 		= $this->_data['os'][$this->os_id][0];
				if ($this->_data['os'][$this->os_id][1]) $this->_ret['os_name']			= $this->_data['os'][$this->os_id][1];
				if ($this->_data['os'][$this->os_id][2]) $this->_ret['os_url']			= $this->_data['os'][$this->os_id][2];
				if ($this->_data['os'][$this->os_id][3]) $this->_ret['os_company']		= $this->_data['os'][$this->os_id][3];
				if ($this->_data['os'][$this->os_id][4]) $this->_ret['os_company_url']		= $this->_data['os'][$this->os_id][4];
				if ($this->_data['os'][$this->os_id][5]) $this->_ret['os_icon']			= $this->_data['os'][$this->os_id][5];
			}
			return $this->_ret;
		}
		return $this->_ret;
	}

	private function _loadData() {
		if (file_exists($this->cache_dir.'/uasdata.ini')) {
			return @parse_ini_file($this->cache_dir.'/uasdata.ini', true);
		}
		//else die('ERROR: No datafile (uasdata.ini in Cache Dir), maybe update the file manually.'.$this->cache_dir.'/uasdata.ini');
	}
}
?>
