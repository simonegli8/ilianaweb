<?php defined('is_running') or die('Not an entry point...');

//echo "Start of CounterHook.php";

if (!class_exists('CounterCommon'))
{
	include_once('CounterCommon.php');
}

if (!class_exists('CounterHook')) {
////////////////////////////////////////////////////////

class CounterHook extends CounterCommon
{
	function __construct()
	{
		//print "Creating Gadget\n";
		parent::__construct();
		$this->Run();
	}
	
	function __destruct()
	{
		//print "Destroying Gadget\n";
	}
	
	function update_domains()
	{
		global $addonPathData;
		if (file_exists($addonPathData.'/domains.php'))
		{
			$s = file_get_contents($addonPathData.'/domains.php');
		}
		else
		{
			$s = protect;
		}
		if (strpos($s, $this->ip)===false)
		{
			$domain=gethostbyaddr($this->ip);
			$s.=$this->ip.'|'.$domain.PHP_EOL;
			file_put_contents($addonPathData.'/domains.php',$s);
			//echo 'Data saved ';
		}
	}
	
	function AddRecord()
	{
		if ($this->config['dont_count_logged_in'] && common::LoggedIn())
			return;
		if (strpos(strtolower($_SERVER['REQUEST_URI']),'/favicon.')!==false)
			return; //don't count
		if ($this->config['ip_filter_nocount'])
		{
			$filters=explode(',',$this->config['ip_filter']);
			foreach ($filters as $value)
			{
				$value=trim($value);
				if ($value!='' && strpos($this->ip, $value) !== false)
				{
					return; //matches, not record in log
				}
			}
		}
		if (!file_exists($this->dataFile))
		{
			file_put_contents($this->dataFile, protect);
		}
		if (filesize($this->dataFile) > $this->config['fsize_limit'])
			return;
		$str = @serialize(array( $this->ip, $this->now, $this->uri, $this->useragent, $this->referer ));
		if ($str && $fp = @fopen($this->dataFile,'a'))
		{
			flock($fp, LOCK_EX);
			fwrite($fp, $str.PHP_EOL);
			flock($fp, LOCK_UN);
			fclose($fp);
			//echo 'Visitor\'s request recorded';
			if ($this->config['ip2dns'])
			{
				$this->update_domains();
			}
		}
		//global $addonPathData; if (!$str) {file_put_contents($addonPathData.'/errors.txt', $this->ip.$_SERVER['REQUEST_URI'].$this->useragent.$this->referer);}
	}
	
	function test_log()
	{
		global $title,$addonPathData;
		$fp = fopen($addonPathData.'/test_log.txt','a');
		fputs($fp, time().' '.$title.'*'.PHP_EOL);
		fclose($fp);
	}

	function Run()
	{
		if (!defined('page_is_already_counted')) // if counter is added twice on the page
		{
			//$this->test_log();
			$this->LoadSettings();
			$this->AddRecord();
			define('page_is_already_counted',true);
		}
	}
	
}

////////////////////////////////////////////////////////
} //end of !class_exists(CounterHook)

//$c=new CounterHook;
//$c->Run();
//unset($c);
//echo "End of CounterHook.php";

