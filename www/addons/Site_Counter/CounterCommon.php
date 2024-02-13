<?php defined('is_running') or die('Not an entry point...');

//echo "Start of CounterCommon.php";

define('timeonline','1800'); // standard is 30 minutes*60seconds, min.period betwen page requests to start a new visit.
define('external_address','http://www.topwebhosts.org/tools/ip-locator.php?query='); // this is reference to some ip database. it is always followed by visitor's ip address. some examplest: http://www.ipchecking.com/?ip= , http://www.ipgp.net/api/xml/ , http://www.topwebhosts.org/tools/ip-locator.php?query= , http://www.ip-adress.com/whois/, etc.
define('protect','<'.'?php defined(\'is_running\') or die(\'Not an entry point.\'); ?'.'>'.PHP_EOL); //protection string

// these constants must be equal to those defined in uasdata.ini in section [browser_type].
define('cat_filter1','Robot');
define('cat_filter2','Browser');
define('cat_filter3','Offline Browser');
define('cat_filter4','Mobile Browser');
define('cat_filter5','Email client');
define('cat_filter6','Library');
define('cat_filter7','Wap Browser');
define('cat_filter8','Validator');
define('cat_filter9','Feed Reader');
define('cat_filter10','Multimedia Player');
define('cat_filter11','Other');
define('cat_filter12','Useragent Anonymizer');
define('cat_filter13','unknown');

class CounterCommon
{
	// the following URLs are used to check and update UASParser's library datafile. See in menu Settings -> UAS Parser Datafile Update.
	public $IniUrl = 'http://user-agent-string.info/rpc/get_data.php?key=free&format=ini';

	public $VerUrl = 'http://user-agent-string.info/rpc/get_data.php?key=free&format=ini&ver=y';

	public $md5Url = 'http://user-agent-string.info/rpc/get_data.php?format=ini&md5=y';

	public $InfoUrl= 'http://user-agent-string.info';
	
	public $configFile; // string - path to config file
	public $dataFile; // string - path to stored records / datalog file
	
	public $config;	// array - loaded from config file
	public $ip;	// string - ip of current visitor
	public $useragent; // string - user agent of current visitor
	public $referer; // string - last entry referring address of current visitor
	public $uri; // string - requested uri
	public $now;	// integer - current time (page request time)
	public $today_start; // integer - the first second of this day
	public $today_end; // integer - the last second of this day
	public $tz_server; // server's time zone, for information.
	
	public $addresses; // ip addresses list
	public $pages; // requested pages list
	public $days; // daily statistics list (with visits)


	var $uati=array( 'Robot'=>1, 'Browser'=>2, 'Offline Browser'=>3, 'Mobile Browser'=>4, 
	'Email client'=>5, 'Library'=>6, 'Wap Browser'=>7, 'Validator'=>8, 'Feed Reader'=>9, 
	'Multimedia Player'=>10, 'Other'=>11, 'Useragent Anonymizer'=>12, 'unknown'=>13);
	
	function __construct()
	{
		global $addonPathData;
		//echo "Creating CounterCommon\n";
		$this->configFile = $addonPathData.'/config.php';
		$this->dataFile = $addonPathData.'/log.php';
		$this->tz_server = date_default_timezone_get();
		$this->config = array();
		$this->addresses = array();
		$this->pages = array();
		$this->days = array();
		$this->Init();
	}
	
	function __destruct()
	{
		; //echo "Destroying CounterCommon\n";
	}
	
	function Init()
	{
		//echo 'initializing... ';
		//Correct ip address with X-Forwarded-For http header if you are behind a proxy or load balancer.
		//http://core.trac.wordpress.org/attachment/ticket/9235/9235.2.diff //thanks to author for this ip recognition code
		if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) )
		{
			// this one can have multiple IPs separated by a coma
			$_SERVER['HTTP_X_FORWARDED_FOR'] = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$_SERVER['HTTP_X_FORWARDED_FOR'] = $_SERVER['HTTP_X_FORWARDED_FOR'][0];
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif ( isset($_SERVER['HTTP_X_REAL_IP']) )
		{
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
		}
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->useragent = isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT']:'';
		$this->referer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:'';
		$this->useragent = str_replace("\n",' ',$this->useragent);//a bit more secure?
		$this->referer = str_replace("\n",' ',$this->referer);//a bit more secure?
		$this->uri = str_replace("\n",' ',$_SERVER['REQUEST_URI']);//a bit more secure?
		if ($this->uri=='')
		{
			$this->uri='/';
		}
		//echo '<pre>'.var_export($_SERVER).'</pre>';
		//$this->useragent = '';//test
		//$this->useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SLCC1; .NET CLR 1.1.4325; .NET CLR 2.0.50727; .NET CLR 3.0.30729)';//test
		//$this->ip = '11.22.33.44';//test
		//$this->ip = '66.249.65.111';//test
	}
	
	function SetDefaults() // sets and saves the default settings
	{
		global $dataDir,$addonPathData;
		if (!file_exists($addonPathData))
			gpFiles::CheckDir($addonPathData);
		$this->config = array();
		$this->config['start_time']=time(); //counter start time
		$this->config['last_update']=$this->config['start_time']; //log last processed time
		$this->config['last_upsize']=strlen(protect); //log last update filesize
		$this->config['atv'] = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0); //0..13 agent total visits for all days.  (0th index is unused,padding)
		$this->config['atp'] = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0); //0..13 agent total pageviews for all pages.  (0th index is unused,padding)
		$this->config['clear_visits'] = false;
		$this->config['clear_referers'] = false;
		$this->config['clear_pages'] = false;
		$this->config['clear_agents'] = false;
		$this->config['clear_days'] = false;
		$this->config['clear_visitors'] = false;
		$this->config['test_old_records'] = false;
		$this->config['after_update'] = 'nothing';
		$this->config['max_visits']=30; //how many days to print on Visits Screen
		$this->config['ip_filter'] = $this->ip;
		$this->config['ip_filter_nocount']=true;
		$this->config['atf'] = array(1,1,1,1,1,1,1,1,1,1,1,1,1); //0..12 screen filter for ip addresses (offsets need to be shifted +1 to 1..13)
		$this->config['ip2dns']=true; //whether set or not to set description automatically for new ip-addresses to translated domain name.
		$this->config['lang']='en'; //selected language
		$this->config['fsize_limit']= 50*1024*1024; //maximum filesize of the datafile, default=50MB
		$this->config['max_mem_usage']=0; //maximum measured memory usage (only informative)
		$this->config['date_format']='d.M.Y'; //date format
		$this->config['time_format']='H:i:s'; //time format
		$this->config['time_zone'] = date_default_timezone_get();//default time zone
		$this->config['page_length']=20; //count of rows per page of ip-table
		$this->config['hide_admin_pages']=true; //hide admin pages from the list of visited pages
		$this->config['dont_count_logged_in']=true; //don't count logged in users.
		$this->config['uasparser_local_version']='N/A'; //local version of uasparser
		$this->config['bar_color']='0000FF';
		$this->config['bg_color']='BBFFEE';
		$this->config['text_color']='FF0000';
		$this->save_data($this->configFile,$this->config);
		//echo 'Default settings loaded.';
	}
	
	function LoadSettings()
	{
		if (file_exists($this->configFile))
		{
			$this->config = $this->load_data($this->configFile);
		}
		else
		{
			$this->SetDefaults();
		}
		date_default_timezone_set($this->config['time_zone']);// set time zone to user defined string
		$this->now = time();
		//$this->today_start = $this->now - ($this->now % 86400);//http://php.net/manual/en/function.mktime.php
		$this->today_start = strtotime('today');
		$this->today_end = $this->today_start + 86400;
		//echo '<pre>';print_r($this->config);echo '</pre>';
		//echo 'Settings loaded ';
	}
	
	function LoadLanguage()
	{
		global $addonPathCode;
		$langFile = $addonPathCode.'/languages/lang_'.$this->config['lang'].'.php';
		if (file_exists($langFile))
			include($langFile);
		else
		{
			echo $this->config['lang'].' language not found.';
			include($addonPathCode.'/languages/lang_en.php');
		}
	}
	
	function load_data($file)
	{
		$data=array();
		if (file_exists($file))
		{
			$s=@file_get_contents($file,false,NULL,strlen(protect));
			if ($s) { $data = @unserialize(trim($s)); }
			unset($s);
			if ($data===false) $data=array();
		}
		return $data;
	}
	
	function save_data($file,$data)
	{
		$s = @serialize($data);
		if ($s!==false)
		{
			@file_put_contents($file, protect.$s);
			//echo 'Data saved ';
		}
	}
	
	function getMemoryLimit()
	{
		$ml=ini_get('memory_limit');//string
		$nr=$ml+0; //+0 converts it to integer
		$unit=ltrim(str_replace((string)$nr,'',$ml));
		if ($unit=='M' || $unit=='m')
			return $nr*1024*1024;
		else if ($unit=='K' || $unit=='k')
			return $nr*1024;
		else if ($unit=='')
			return $nr;
	}
	
	function getMemoryUsage()
	{
		$mem_usage = $this->config['max_mem_usage'];
		$ml = $this->getMemoryLimit();
		if ($ml<0)
			return round($mem_usage/1048576,2).' MB / no memory limit<br/>';
		$r = '<b>'.round(100*$mem_usage/$ml,2).'%</b> - ';
		if ($ml < 1048576)
			$r .= round($mem_usage/1024).' KB / '.round($ml/1024).' KB';
		else
			$r .= round($mem_usage/1048576).' MB / '.round($ml/1048576).' MB';
		return $r; //this function returns string
	}
	
	function getDirectorySize($path)
	{
	  //http://www.go4expert.com/forums/showthread.php?t=290
	  $totalsize = 0;
	  $totalcount = 0;
	  $dircount = 0;
	  if ($handle = opendir ($path))
	  {
	    while (false !== ($file = readdir($handle)))
	    {
	      $nextpath = $path . '/' . $file;
	      if ($file != '.' && $file != '..' && !is_link ($nextpath))
	      {
	        if (is_dir ($nextpath))
	        {
	          $dircount++;
	          $result = $this->getDirectorySize($nextpath);
	          $totalsize += $result['size'];
	          $totalcount += $result['count'];
	          $dircount += $result['dircount'];
	        }
	        elseif (is_file ($nextpath))
	        {
	          $totalsize += filesize ($nextpath);
	          $totalcount++;
	        }
	      }
	    }
	  }
	  closedir ($handle);
	  $total['size'] = $totalsize;
	  $total['count'] = $totalcount;
	  $total['dircount'] = $dircount;
	  return $total;
	}
	
	function sizeFormat($size)
	{
	  if($size<1024)
	  {
	      return $size." B";
	  }
	  else if($size<(1024*1024))
	  {
	      $size=round($size/1024,1);
	      return $size." KB";
	  }
	  else if($size<(1024*1024*1024))
	  {
	      $size=round($size/(1024*1024),1);
	      return $size." MB";
	  }
	  else
	  {
	      $size=round($size/(1024*1024*1024),1);
	      return $size." GB";
	  }
	
	}
	
	function load_domains()
	{
		global $addonPathData;
		$domains=array();
		if (file_exists($addonPathData.'/domains.php'))
		{
			$s =@file_get_contents($addonPathData.'/domains.php',false,NULL,strlen(protect));
		}
		else
		{
			return $domains;
		}
		$d = explode(PHP_EOL,$s);
		$c = count($d);
		for($i=0; $i<$c; $i++)
		{
			$s=rtrim($d[$i]);
			$p=strpos($s,'|');
			if ($p==false)
				continue;
			$ip = substr($s,0,$p);
			$domain = substr($s,$p+1);
			$domains[$ip]=$domain;
		}
		//echo 'Data loaded ';
		return $domains;
	}

}

//echo "End of CounterCommon.php";

//$c=new CounterCommon; print_r($c->load_data($c->configFile)); unset($c);

