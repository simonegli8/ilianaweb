<?php defined('is_running') or die('Not an entry point...');

include_once('CounterCommon.php');

//echo "Start of CounterAdmin.php";

class CounterAdmin extends CounterCommon
{
	function __construct()
	{
		//echo "Creating CounterAdmin\n";
		parent::__construct();
		$this->Run();
	}

	function ProcessForm()
	{
		global $addonPathData,$addonPathCode;
		//echo '<pre>'; echo count($_POST); print_r($_POST); echo '</pre>';
		//echo '<pre>'; print_r($this->config); echo '</pre>';
		if (isset($_POST['save_visits_settings']))
		{
			if (isset($_POST['max_visits']))
				$this->config['max_visits'] = $_POST['max_visits']<0? 0:0+$_POST['max_visits'];
			if (isset($_POST['bar_color']))
				$this->config['bar_color'] = $_POST['bar_color'];
			if (isset($_POST['bg_color']))
				$this->config['bg_color'] = $_POST['bg_color'];
			if (isset($_POST['text_color']))
				$this->config['text_color'] = $_POST['text_color'];
			$this->save_data($this->configFile,$this->config);
			message($this->msg['save'].' - '.$this->msg['done'].'<br/>');
		}
		if (isset($_POST['remove_selected_pages']))
		{
			if (!file_exists($addonPathData.'/pages.php'))
			{
				return;
			}
			$this->pages = $this->load_data($addonPathData.'/pages.php');//page>stats
			foreach ($this->pages as $key => $value) // checking all pages
			{
				if (isset($_POST['delete'.$value[0]]))
				{
					unset($this->pages[$key]);
					message($value[0].' ');
				}
			}
			$this->save_data($addonPathData.'/pages.php',$this->pages);//save requested pages
			message($this->msg['remove'].' - '.$this->msg['done'].'<br/>');
		}
		if (isset($_POST['save_requested_pages_settings']))
		{
			$this->config['hide_admin_pages'] = isset($_POST['hide_admin_pages']) ? true : false;
			$this->save_data($this->configFile,$this->config);
			message($this->msg['save'].' - '.$this->msg['done'].'<br/>');
		}
		if (isset($_POST['save_table']))
		{
			if (isset($_POST['page_length']) && $_POST['page_length']!=$this->config['page_length'])
			{
				$this->config['page_length'] = 0+$_POST['page_length'];
				$this->save_data($this->configFile,$this->config);
			}
			message($this->msg['save'].' - '.$this->msg['done'].'<br/>');
		}
		if (isset($_POST['save_settings']))
		{
			//saving counter's settings
			if (isset($_POST['ip_filter']))
				$this->config['ip_filter'] = $_POST['ip_filter'];
			$this->config['ip_filter_nocount'] = isset($_POST['ip_filter_nocount']) ? true : false;
			if (isset($_POST['time_zone']))
			{
				$this->config['time_zone']=$_POST['time_zone'];
				date_default_timezone_set($this->config['time_zone']);
				$this->now = time();
			}
			if (isset($_POST['date_format']))
				$this->config['date_format']= ($_POST['date_format']=='')? 'd.M.Y' : $_POST['date_format'];
			if (isset($_POST['time_format']))
				$this->config['time_format']= ($_POST['time_format']=='')? 'H:i:s' : $_POST['time_format'];
			if (isset($_POST['language']))
			{
				$this->config['lang']=$_POST['language'];
				$this->LoadLanguage();
			}
			$this->config['menu_icon_set'] = 0+$_POST['menu_icon_set'];
			$this->config['order'] = isset($_POST['order']) ? true : false;
			$this->config['ip2dns'] = isset($_POST['ip2dns']) ? true : false;
			$this->config['dont_count_logged_in'] = isset($_POST['dont_count_logged_in']) ? true : false;
			if (isset($_POST['fsize_limit']))
			{
				if (0+$_POST['fsize_limit']<0) $_POST['fsize_limit']=0;// 0==disabled
				$this->config['fsize_limit'] = round(1048576*$_POST['fsize_limit']); //string to integer (MBytes to Bytes)
			}
			$this->save_data($this->configFile,$this->config);
			message($this->msg['save'].' - '.$this->msg['done'].'<br/>');
		}
		if (isset($_POST['save_screen_filter']))
		{
			$this->config['atf'] = array();
			for ($i=0;$i<13;$i++)
				$this->config['atf'][$i] = isset($_POST['atf'.($i+1)]);
			$this->save_data($this->configFile,$this->config);
			message($this->msg['save'].' - '.$this->msg['done'].'<br/>');
		}
		if (isset($_POST['save_times']))
		{
			if ($_POST['cstart']=='cd')
			{
				$this->config['start_time']=round($this->now-24*60*60*$_POST['counted_days']);
			}
			elseif ($_POST['cstart']=='st')
			{
				$this->config['start_time']=0+$_POST['start_time'];
			}
			if (isset($_POST['last_update']))
			{
				$this->config['last_update']=0+$_POST['last_update'];
			}
			$this->save_data($this->configFile,$this->config);
			message($this->msg['manually_set'].' - '.$this->msg['done'].'<br/>');
		}
		if (isset($_POST['purge_files']))
		{
			$todelete = explode(';',$_POST['todelete']);
			foreach ($todelete as $filename)
			{
				echo $filename;
				if ($filename=='uasdata.ini' || $filename=='') {
					continue;
				}
				if (file_exists($addonPathData.'/'.$filename) && is_file($addonPathData.'/'.$filename))
				{
					unlink($addonPathData.'/'.$filename);
					message($filename.',<br/>');
					if ($filename=='log.php') {
						$this->config['last_upsize']=strlen(protect);
						$this->save_data($this->configFile, $this->config);
					}
				}
			}
			//$this->recursive_expunge($addonPathData);
			message($this->msg['purge'].' - '.$this->msg['done'].'<br/>');
		}
	}

	function page_navigation(&$p,$displayed)
	{
		global $addonRelativeCode;
		if ($this->config['page_length']==0)
			return;
		$lastpage = floor($displayed/$this->config['page_length']);
		if (($displayed%$this->config['page_length'])==0)
			$lastpage--;
		if ($p>$lastpage)
			$p=$lastpage;
		if ($p<0)
			$p=0;
		if ($lastpage==0)
			return;//not show
		echo '<table cellspacing="2" cellpadding="4" style="float:right; font-weight:bold; height:50px; text-align:center">';
		echo '<tbody><tr>';

		echo '<td>';
		echo ($p>0) ? '<a href="'.common::GetUrl('Admin_Counter').'?show_ip_table=0">' : '';
		echo '<img src="'.$addonRelativeCode.'/img/go-first.png" alt="'.$this->msg['pagefirst'].'" /> ';
		echo ($p>0) ? '</a>':'';
		echo '</td>'.PHP_EOL;

		echo '<td>';
		echo ($p>0) ? '<a href="'.common::GetUrl('Admin_Counter').'?show_ip_table='.($p-1).'">' : '';
		echo '<img src="'.$addonRelativeCode.'/img/go-previous.png" alt="'.$this->msg['page-1'].'" /> ';
		echo ($p>0) ? '</a>':'';
		echo '</td>'.PHP_EOL;

		echo '<td>'.$this->msg['page'].'<br/>'.($p+1).' / '.($lastpage+1).'</td>'.PHP_EOL;

		echo '<td>';
		echo ($p<$lastpage) ? '<a href="'.common::GetUrl('Admin_Counter').'?show_ip_table='.($p+1).'">' : '';
		echo '<img src="'.$addonRelativeCode.'/img/go-next.png" alt="'.$this->msg['page+1'].'" /> ';
		echo ($p<$lastpage) ? '</a>':'';
		echo '</td>'.PHP_EOL;

		echo '<td>';
		echo ($p<$lastpage) ? '<a href="'.common::GetUrl('Admin_Counter').'?show_ip_table='.$lastpage.'">' : '';
		echo '<img src="'.$addonRelativeCode.'/img/go-last.png" alt="'.$this->msg['pagelast'].'" /> ';
		echo ($p<$lastpage) ? '</a>':'';
		echo '</td>'.PHP_EOL;

		echo '</tr></tbody></table>';
	}

	function ShowIPTable($p)
	{
		global $addonPathData, $page, $addonRelativeCode;
		$this->ShowMenu();
		$this->addresses = $this->load_data($addonPathData.'/ips.php');//ip>stats
		foreach ($this->addresses as $ip => $info)
		{
			if (!$this->config['atf'][$this->uati[$info[1]]-1])
				unset($this->addresses[$ip]);
		}
		$this->page_navigation($p,count($this->addresses));
		arsort($this->addresses);
		$domains = $this->load_domains(); //var_export($domains);
		$page->head .= '<style type="text/css"> #counter-table tbody tr:hover > td { background-color:#'.$this->config['bg_color'].'; color:#'.$this->config['text_color'].'; } #counter-table tbody td, #counter-table thead th { vertical-align:middle } </style>'.PHP_EOL;

		echo '<div style="border:2px solid #eee; float:left; margin-right:20px">';
		echo common::Link('Admin_Counter',$this->msg['filter'],'screen_filter',' name="gpabox"');
		echo '</div><div style="clear:both; float:none; visibility: hidden"></div>';

		echo '<table id="counter-table" style="width:100%; border-collapse: collapse; border-spacing: 10px; text-align:center; border: 4px solid #eee; margin-top:4px ">
			<colgroup>
				<col style="width:10%" />
				<col style="width:20%" />
				<col style="width:20%" />
				<col style="width:20%" />
				<col style="width:10%" />
				<col style="width:20%" />
			</colgroup>'.PHP_EOL;
		echo '<thead>'.PHP_EOL;
		echo '<tr style="background-color:#eef">'.PHP_EOL;
		echo '<th>#</th>'.PHP_EOL;
		echo '<th onclick="$(this).parent().parent().parent().find(\'tbody tr td:nth-child(2)\').each(function(){var t=this.getAttribute(\'title\');this.setAttribute(\'title\',this.innerHTML);this.innerHTML=t;});" style="cursor:pointer">'.$this->msg['last_visit'].'</th>';
		echo '<th onclick="$(this).parent().parent().parent().find(\'tbody tr td:nth-child(3)\').each(function(){var t=this.getAttribute(\'title\');this.setAttribute(\'title\',this.innerHTML);this.innerHTML=t;});" style="cursor:pointer">'.$this->msg['ip'].'</th>';
		echo '<th>'.$this->msg['agent_type'].'</th>'.PHP_EOL;
		echo '<th>'.$this->msg['pageviews'].'</th>'.PHP_EOL;
		echo '<th>'.$this->msg['details'].'</th>'.PHP_EOL;
		echo '</tr>'.PHP_EOL;
		echo '</thead>'.PHP_EOL;

		$i=0;
		echo '<tbody>'.PHP_EOL;

		foreach ($this->addresses as $ip => $info)
		{
			$i++;
			if ($this->config['page_length'])
			{
				if ($i<=$p*$this->config['page_length'])
					continue;
				if ($i>($p+1)*$this->config['page_length'])
					break;
			}
			$domain = isset($domains[$ip])?$domains[$ip]:$ip;
			echo '<tr><td><a href="'.external_address.$ip.'" target="_blank">'.$i.'</a></td>';
			echo '<td title="'.$info[0].'">'.date($this->config['date_format'].' '.$this->config['time_format'],$info[0]).'</td>'; //time
			echo '<td title="'.$domain.'">'.$ip.'</td>';
			$ai = $this->uati[$info[1]];
			echo '<td>'.$this->msg['at_filter'.$ai].'</td>';
			echo '<td>'.$info[2].'</td>';
			echo '<td>'.common::Link('Admin_Counter',$this->msg['details'],'ip='.$ip.'&p='.$p,' name="gpabox"').'</td>';
			echo '</tr>';
		}

		echo '</tbody></table>'.PHP_EOL;

		$this->page_navigation($p,count($this->addresses));
		echo '<div style="border:2px solid #eee; float:left; margin-right:20px;padding:1em">';
		echo common::Link('Admin_Counter',$this->msg['agents'].' , '.$this->msg['systems'],'agents');
		echo '</div><div style="clear:both; float:none; visibility: hidden"></div>';
		echo '<form action="'.common::GetUrl('Admin_Counter').'?show_ip_table=0" method="post">';
		echo '<div style="clear:both; float:none; visibility: hidden"></div>';
		echo $this->msg['page_length'].' <input type="text" name="page_length" size="5" maxlength="5" style="text-align:center" value="'.$this->config['page_length'].'" />.<br/><br/>'.PHP_EOL;
		echo '<input type="submit" name="save_table" value="'.$this->msg['save'].'" />'.PHP_EOL;
		echo '</form>';
	}

	function agents()
	{
		global $addonPathData,$addonPathCode,$addonRelativeCode,$page;
		$this->ShowMenu();
		$page->head .= '<style type="text/css"> td,th {text-align:left;} th:nth-child(1),td:nth-child(1),td:nth-child(2) {text-align:right;} </style>'.PHP_EOL;
		$page->head_js[] = $addonRelativeCode.'/sort.js';
		$agents = $this->load_data($addonPathData.'/agents.php');//ip>stats
		//echo '<pre>';var_export($agents);echo '</pre>'; return;

		echo '<h2>'.$this->msg['agents'].'</h2>';

		foreach ($this->uati as $an => $ai) //agent name=>agent index
		{
			$i = isset($agents[$ai])? count($agents[$ai]['ua']):0;
			echo '<p onclick="$(this).next(\'div\').toggle()" style="cursor:pointer"><b>'.$an.'</b> ('.$i.')</p>';
			echo '<div style="display:none">';
			if (!$i)
			{
				echo '</div>';
				continue;
			}
			echo '<table style="width:100%"><tr><td style="width:50%">';

			//a -agents
			$aa=$agents[$ai]['ua']; //agents
			$a=current($aa);
			echo '<table id="uaa'.$ai.'" cellpadding="2" style="width:100%;border-collapse:collapse;">';
			echo '<col style="text-align:right"/><col style="text-align:left"/><col style="text-align:left"/>';
			echo '<thead><tr>';
			echo '<th onclick="sorter.sort(\'uaa'.$ai.'\',0,2);" style="cursor:pointer">&#8721;</th>';//sum
			echo '<th></th>';//icon
			echo '<th onclick="sorter.sort(\'uaa'.$ai.'\',2,1);" style="cursor:pointer;text-align:left">'.$this->msg['agent'].'</th>';
			echo '</tr></thead><tbody>';
			while ($a !== false)
			{
				echo '<tr><td>'.$a[0].'</td>';
				echo '<td><img src="http://user-agent-string.info/pub/img/ua/'.$a[1].'" alt=""/></td>';
				echo '<td>'.key($aa).'</td>';
				echo '</tr>';
				$a=next($aa);
			}
			echo '</tbody></table>';

			echo '</td><td style="width:50%">';

			//b -families
			$bb=$agents[$ai]['uaf']; //agent families
			$b=current($bb);
			echo '<table id="uab'.$ai.'" cellpadding="2" style="width:100%">';
			echo '<thead><tr>';
			echo '<th onclick="sorter.sort(\'uab'.$ai.'\',0,2);" style="cursor:pointer">&#8721;</th>';//sum
			echo '<th></th>';//icon
			echo '<th onclick="sorter.sort(\'uab'.$ai.'\',2,1);" style="cursor:pointer;text-align:left">'.$this->msg['ua_family'].'</th>';
			echo '</tr></thead><tbody>';
			while ($b !== false)
			{
				echo '<tr><td>'.$b[0].'</td>';
				echo '<td><img src="http://user-agent-string.info/pub/img/ua/'.$b[1].'" alt=""/></td>';
				echo '<td>'.key($bb).'</td>';
				echo '</tr>';
				$b=next($bb);
			}
			echo '</tbody></table>';


			echo '</td></tr></table>';
			echo '</div>';
		}



		echo '<br/><br/><h2>'.$this->msg['systems'].'</h2>';

		foreach ($this->uati as $an => $ai) //agent name=>agent index
		{
			$i = isset($agents[$ai])? count($agents[$ai]['os']):0;
			echo '<p onclick="$(this).next(\'div\').toggle()" style="cursor:pointer"><b>'.$an.'</b> ('.$i.')</p>';
			echo '<div style="display:none">';
			if (!$i)
			{
				echo '</div>';
				continue;
			}
			echo '<table style="width:100%"><tr><td style="width:50%">';

			//a -operating systems
			$aa=$agents[$ai]['os']; //operating systems
			$a=current($aa);
			echo '<table id="osa'.$ai.'" cellpadding="2" style="width:100%;border-collapse:collapse;">';
			echo '<col style="text-align:right"/><col style="text-align:left"/><col style="text-align:left"/>';
			echo '<thead><tr>';
			echo '<th onclick="sorter.sort(\'osa'.$ai.'\',0,2);" style="cursor:pointer">&#8721;</th>';//sum
			echo '<th></th>';//icon
			echo '<th onclick="sorter.sort(\'osa'.$ai.'\',2,1);" style="cursor:pointer;text-align:left">'.$this->msg['system'].'</th>';
			echo '</tr></thead><tbody>';
			while ($a !== false)
			{
				echo '<tr><td>'.$a[0].'</td>';
				echo '<td><img src="http://user-agent-string.info/pub/img/os/'.$a[1].'" alt=""/></td>';
				echo '<td>'.key($aa).'</td>';
				echo '</tr>';
				$a=next($aa);
			}
			echo '</tbody></table>';

			echo '</td><td style="width:50%">';

			//b -families
			$bb=$agents[$ai]['osf']; //operating systems families
			$b=current($bb);
			echo '<table id="osb'.$ai.'" cellpadding="2" style="width:100%">';
			echo '<thead><tr>';
			echo '<th onclick="sorter.sort(\'osb'.$ai.'\',0,2);" style="cursor:pointer">&#8721;</th>';//sum
			echo '<th></th>';//icon
			echo '<th onclick="sorter.sort(\'osb'.$ai.'\',2,1);" style="cursor:pointer;text-align:left">'.$this->msg['os_family'].'</th>';
			echo '</tr></thead><tbody>';
			while ($b !== false)
			{
				echo '<tr><td>'.$b[0].'</td>';
				echo '<td><img src="http://user-agent-string.info/pub/img/os/'.$b[1].'" alt=""/></td>';
				echo '<td>'.key($bb).'</td>';
				echo '</tr>';
				$b=next($bb);
			}
			echo '</tbody></table>';


			echo '</td></tr></table>';
			echo '</div>';
		}
	}

	function ShowIP()
	{
		global $addonPathData,$addonPathCode,$page;
		//echo $_SERVER['QUERY_STRING'];
		parse_str($_SERVER['QUERY_STRING']);

		echo '<div style="font-size:16px;font-weight:bold;">'.$ip.' - '.$this->msg['details'].'</div><br/>';

		$fp = fopen($this->dataFile,'r');
		flock($fp, LOCK_SH);
		fgets($fp);//this is protection string
		$p = array();
		while (!feof($fp))
		{
			$s = fgets($fp);
			if (strpos($s,$ip)===false)
				continue;
			$r = @unserialize(trim($s));
			if ($r===false || $r[0]!=$ip)
				continue;
			$p[] = $r;
			//echo '<pre>';var_export($r);echo '</pre>';
		}
		flock($fp, LOCK_UN);
		fclose($fp);
		if (!count($p))
		{
			echo $this->msg['no_details'];
			return;
		}
		include($addonPathCode.'/UASparser.php');
		$parser = new UASparser();
		$ps=null;
		echo '<table style="width:100%;border-collapse: collapse;border-spacing:10px;">';
		foreach ($p as $r)
		{
			$label = $this->Get_Label($r[2]);
			echo '<tr>';
			echo '<td style="word-wrap:break-word;">'.date($this->config['date_format'].' '.$this->config['time_format'],$r[1]).'</td>';
			echo '<td style="word-wrap:break-word;"><a href="'.htmlspecialchars($r[2]).'" title="'.htmlspecialchars(rawurldecode($r[2])).'">'.$label[0].($label[1]==''? '':'?'.htmlspecialchars($label[1])).'</a></td>';
			if ($ps!=$r[3])
			{
				$ret = $parser->Parse($r[3]);
				$ps=$r[3];
			}
			echo '<td><img src="http://user-agent-string.info/pub/img/ua/'.$ret['ua_icon'].'" alt="" title="'.$ret['ua_name'].'"/></td>';
			echo '<td><img src="http://user-agent-string.info/pub/img/os/'.$ret['os_icon'].'" alt="" title="'.$ret['os_name'].'"/></td>';
			echo '<td style="word-wrap:break-word;">'.htmlentities(rawurldecode($r[4]),ENT_COMPAT,'UTF-8').'</td></tr>';
		}
		unset($parser);
		echo '</table>';
	}

	function screen_filter()
	{
		global $addonRelativeCode,$addonPathData;
		echo '<script type="text/javascript" src="'.$addonRelativeCode.'/checkboxes.js"></script>';
		echo '<div style="margin:1em;">';
		$addresses = $this->load_data($addonPathData.'/ips.php');//ip>stats
		$x=array();
		if (count($addresses))
		{
			foreach ($addresses as $value)
				if (isset($x[$value[1]]))
					$x[$value[1]]++;
				else
					$x[$value[1]]=1;
			//var_export($x);
		}
		echo '<form action="'.common::GetUrl('Admin_Counter').'?show_ip_table" method="post">';
		echo '<span style="font-size:16px;font-weight:bold">'.$this->msg['at_filter'].'</span><br/>'.$this->msg['at_filter_screen'].'<br/>';
		echo '<table border="1" style="width:100%">';
		echo '<tr>';
		echo '<td><input type="checkbox" name="atf1" '. ($this->config['atf'][0]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter1'].(isset($x[cat_filter1])? ' ('.$x[cat_filter1].')':'').'</td>'.PHP_EOL;
		echo '<td><input type="checkbox" name="atf2" '. ($this->config['atf'][1]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter2'].(isset($x[cat_filter2])? ' ('.$x[cat_filter2].')':'').'</td>'.PHP_EOL;
		echo '<td><input type="checkbox" name="atf3" '. ($this->config['atf'][2]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter3'].(isset($x[cat_filter3])? ' ('.$x[cat_filter3].')':'').'</td>'.PHP_EOL;
		echo '</tr>';
		echo '<tr>';
		echo '<td><input type="checkbox" name="atf4" '. ($this->config['atf'][3]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter4'].(isset($x[cat_filter4])? ' ('.$x[cat_filter4].')':'').'</td>'.PHP_EOL;
		echo '<td><input type="checkbox" name="atf5" '. ($this->config['atf'][4]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter5'].(isset($x[cat_filter5])? ' ('.$x[cat_filter5].')':'').'</td>'.PHP_EOL;
		echo '<td><input type="checkbox" name="atf6" '. ($this->config['atf'][5]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter6'].(isset($x[cat_filter6])? ' ('.$x[cat_filter6].')':'').'</td>'.PHP_EOL;
		echo '</tr>';
		echo '<tr>';
		echo '<td><input type="checkbox" name="atf7" '. ($this->config['atf'][6]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter7'].(isset($x[cat_filter7])? ' ('.$x[cat_filter7].')':'').'</td>'.PHP_EOL;
		echo '<td><input type="checkbox" name="atf8" '. ($this->config['atf'][7]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter8'].(isset($x[cat_filter8])? ' ('.$x[cat_filter8].')':'').'</td>'.PHP_EOL;
		echo '<td><input type="checkbox" name="atf9" '. ($this->config['atf'][8]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter9'].(isset($x[cat_filter9])? ' ('.$x[cat_filter9].')':'').'</td>'.PHP_EOL;
		echo '</tr>';
		echo '<tr>';
		echo '<td><input type="checkbox" name="atf10" '. ($this->config['atf'][9]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter10'].(isset($x[cat_filter10])? ' ('.$x[cat_filter10].')':'').'</td>'.PHP_EOL;
		echo '<td><input type="checkbox" name="atf11" '. ($this->config['atf'][10]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter11'].(isset($x[cat_filter11])? ' ('.$x[cat_filter11].')':'').'</td>'.PHP_EOL;
		echo '<td><input type="checkbox" name="atf12" '. ($this->config['atf'][11]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter12'].(isset($x[cat_filter12])? ' ('.$x[cat_filter12].')':'').'</td>'.PHP_EOL;
		echo '</tr>';
		echo '<tr>';
		echo '<td><input type="checkbox" name="atf13" '. ($this->config['atf'][12]? 'checked="checked"' : '') .'/> '.$this->msg['at_filter13'].(isset($x[cat_filter13])? ' ('.$x[cat_filter13].')':'').'</td>'.PHP_EOL;
		echo '<td colspan="2" align="right"><label for="cb_checkallt">'.$this->msg['cb_checkall'].'</label><input type="checkbox" id="cb_checkallt" name="cb_checkallt" onclick="checkUncheckAllt(this);"/></td>'.PHP_EOL;
		echo '</tr>';
		echo '</table><br/>';
		echo '<input type="submit" name="save_screen_filter" value="'.$this->msg['save'].'" />';
		echo '</form>';
		echo '</div>';
	}

	function Select_Languages()
	{
		global $addonPathCode, $languages, $langmessage;

		$avail=array();
		if ($handle = opendir($addonPathCode.'/languages'))
		{
			while (false !== ($file = readdir($handle)))
				if (strpos($file, 'lang_')!==false)
					$avail[] = substr($file,5,-4);
			closedir($handle);
		}
		//uksort($avail,"strnatcasecmp");
		//print_r($avail);
		echo '<select name="language">';
		echo '<optgroup label="'.$langmessage['language'].'">';
		foreach ($avail as $lang)
		{
			if (!strlen($lang))
				continue;
			$lang1 = isset($languages[$lang])?$languages[$lang]:$lang;
			echo '<option value="'.$lang.'"'. ($this->config['lang']==$lang ? 'selected="selected"':'') .'> '.$lang.' - '.$lang1.' </option>';
		}
		echo '</optgroup>';
		echo '</select>';
	}

	function settings()
	{
		global $addonRelativeCode,$addonPathData,$config,$addonFolderName,$langmessage;

		$this->ShowMenu();
		echo '<div style="margin:0.5em;">';
		echo '<form action="'.common::GetUrl('Admin_Counter').'?settings" method="post">'.PHP_EOL;
		echo '<table border="1" cellpadding="8" cellspacing="2">';
		echo '<tr>';
		echo '<td>';
		echo '<div style="font-size:16px;font-weight:bold">'.$langmessage['Settings'].'</div><br/>'.PHP_EOL;
		echo $this->msg['tz_server'].' '.$this->tz_server.'.<br/>'.PHP_EOL;
		echo $this->msg['tz_counter'].' <input type="text" name="time_zone" maxlength="20" size="20" value="'.$this->config['time_zone'].'" />. '.PHP_EOL;
		echo $this->msg['tz_counter_time'].': '.date($this->config['time_format'],$this->now).'.<br /> '.$this->msg['tz_link'].'<br/>'.PHP_EOL;

		echo '<label for="date_format">'.$this->msg['date_format'].'</label> <input type="text" id="date_format" name="date_format" size="20" maxlength="20" value="'.$this->config['date_format'].'" /> ,<br /> '.PHP_EOL;
		echo '<label for="time_format">'.$this->msg['time_format'].'</label> <input type="text" id="time_format" name="time_format" size="20" maxlength="20" value="'.$this->config['time_format'].'" />. <br /> '.PHP_EOL.$this->msg['info_format'].'<br/>'.PHP_EOL;

		echo '<p>'.$langmessage['language'].': ';
		$this->Select_Languages();
		echo '</p>'.PHP_EOL;

		echo '<p>'.$this->msg['menu_icon_set'].': ';
		echo '<select name="menu_icon_set">';
		$menu_icon_set = isset($this->config['menu_icon_set'])? $this->config['menu_icon_set'] : 0;
		echo '<option value="0"'. ($menu_icon_set==0 ? 'selected="selected"':'') .'> FeNiWeb (V4) </option>';
		echo '<option value="1"'. ($menu_icon_set==1 ? 'selected="selected"':'') .'> Classic (V3) </option>';
		echo '</select>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>';
		echo '<b>'.$this->msg['ip_filter'].'</b><br/><br/>';
		echo $this->msg['ip_filter_info'].'<br/>';
		echo '<input id="ip_filter" type="text" value="'.$this->config['ip_filter'].'" name="ip_filter" size="80" maxlength="2048" style="width:99%; background-color:#f7f7f7;" /> <br/>';
		echo '<input type="checkbox" id="ip_filter_nocount" name="ip_filter_nocount"'. ($this->config['ip_filter_nocount']==true ? 'checked="checked"' : '') .' /><label for="ip_filter_nocount">'.$this->msg['ip_filter_nocount'].'</label><br/>';
		echo $this->msg['your_ip'].' :'.$this->ip;
		echo '&nbsp;<input type="button" value="'.$this->msg['add_your_ip']."\" onclick=\"document.getElementById('ip_filter').value += (document.getElementById('ip_filter').value==''?'".$this->ip."':', ".$this->ip."');\"".'/>';

		echo '<p><input type="checkbox" name="ip2dns"'. ($this->config['ip2dns']==true ? 'checked="checked"' : '') .' />'.$this->msg['ip2dns'].'</p>'.PHP_EOL;
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>';
		echo '<span style="font-size:16px;font-weight:bold">'.$this->msg['others'].'</span><br/><br/>'.PHP_EOL;

		echo $this->msg['plugin_used_memory'].':&nbsp;&nbsp;'.$this->getMemoryUsage().PHP_EOL;
		echo '<br/><br/>';
		$size = file_exists($this->dataFile)? round(filesize($this->dataFile)/1048576,2):0;
		echo $this->dataFile.' : '.$size.' MB<br/>';
		echo $this->msg['fsize_limit'].': <input type="text" name="fsize_limit" maxlength="7" size="4" style="text-align:right" value="'.round($this->config['fsize_limit']/1048576,2).'" /> MB.<br/><br/>';

		echo '&#9658; '.common::Link('Admin_Counter',$this->msg['purge'],'purge').'<br /><br />'.PHP_EOL;
		echo '&#9658; '.common::Link('Admin_Counter',$this->msg['parser_update'],'parser_update',' name="gpabox"').'<br/><br/>'.PHP_EOL;
		echo '<p><input type="checkbox" id="dont_count_logged_in" name="dont_count_logged_in" '. ($this->config['dont_count_logged_in'] ? 'checked="checked"' : '') .'/><label for="dont_count_logged_in">'.$this->msg['dont_count_logged_in'].'</label></p>'.PHP_EOL;
		echo '</td>';

		echo '</tr>';
		echo '</table>';

		echo '<p><input type="submit" name="save_settings" value="'.$this->msg['save'].'" /></p>'.PHP_EOL;
		echo '</form>'.PHP_EOL;
		echo '</div>';
	}

	function recursive_expunge($path)
	{
		if ($handle = opendir ($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				$nextpath = $path . '/' . $file;
				if ($file != '.' && $file != '..' && !is_link ($nextpath))
				{
					if (is_dir ($nextpath))
					{
						$this->recursive_expunge($nextpath);
						rmdir($nextpath); /*remove directory*/
					}
					elseif (is_file ($nextpath))
					{
						unlink($nextpath); /*remove file*/
					}
				}
			}
		}
		closedir ($handle);
	}

	function what_type($filename)
	{
		global $addonRelativeData;
		switch ($filename) {
			case 'agents.php': return common::Link('Admin_Counter', 'User Agents','agents');
			case 'log.php': return common::Link('Admin_Counter', $this->msg['new_data']);
			case 'config.php': return $this->msg['config_file'];
			case 'visits.php': return common::Link('Admin_Counter', $this->msg['visits'],'visits');
			case 'domains.php': return $this->msg['resolved_ips'];
			case 'ips.php': return common::Link('Admin_Counter', $this->msg['visitors'], 'show_ip_table=0');
			case 'pages.php': return common::Link('Admin_Counter', $this->msg['pages'], 'requested_pages');
			case 'referers.php': return common::Link('Admin_Counter', $this->msg['referers'], 'referers');
			case 'uasdata.ini': return $this->msg['parser_file'];
			default: return '';
		}
	}

	function purge()
	{
		global $addonPathData, $addonRelativeCode, $page, $langmessage, $title;
		$page->head .= '<style type="text/css"> #purge_table { border-collapse:collapse; } #purge_table tr:hover > td { background-color:#'.$this->config['bg_color'].'; } </style>'.PHP_EOL;
		$page->head_js[] = $addonRelativeCode.'/checkboxes.js';
		$page->head_js[] = $addonRelativeCode.'/sort.js';
		$this->pages = $this->load_data($addonPathData.'/pages.php');
		$this->ShowMenu();
		echo '<div style="margin:1em;">';
		echo '<div style="font-size:16px;font-weight:bold">'.$this->msg['purge'].'</div><br/>'.PHP_EOL;

		echo $addonPathData.'<br/><br/>'.PHP_EOL;
		echo '<form action="'.common::GetUrl('Admin_Counter').'?purge" method="post" onsubmit="return collect_files(this)">';
		echo '<table id="purge_table" style="width:100%">'.PHP_EOL;
		echo '<thead><tr>';
		echo '<th onclick="sorter.sort(\'purge_table\',0,2);" style="cursor:pointer;text-align:left">#</th>';
		echo '<th onclick="sorter.sort(\'purge_table\',1,1);" style="cursor:pointer;text-align:left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$langmessage['file_name'].'</th>';
		echo '<th onclick="sorter.sort(\'purge_table\',2,1);" style="cursor:pointer;text-align:left">'.$langmessage['description'].'</th>';
		echo '<th style="text-align:left"></th>'; //downloads
		echo '<th onclick="sorter.sort(\'purge_table\',4,2);" style="cursor:pointer;text-align:right">'.$langmessage['File Size'].' (B)</th></tr></thead>';
		$this->pages = $this->load_data($addonPathData.'/pages.php');//page>stats
		$allfiles=array();
		if ($handle = opendir($addonPathData))
		{
			while (false !== ($file = readdir($handle)))
				if (($file!=".") && ($file!=".."))
					$allfiles[] .= $file;
			closedir($handle);
		}
		uksort($allfiles,"strnatcasecmp");
		//print_r($allfiles);
		if (count($allfiles))
		{
			$i=0;
			echo '<tbody>';
			foreach($allfiles as $file)
			{
				$s = $file=='uasdata.ini' ?'disabled="disabled" ':'';
				echo '<tr><td>'.($i+1).'</td>'.PHP_EOL;
				echo '<td><input type="checkbox" id="cb'.$i.'" name="cb'.$i.'" '.$s.'/><label id="lb'.$i.'" for="cb'.$i.'">'.$file.'</label></td>';
				echo '<td>'.$this->what_type($file).'</td>'.PHP_EOL;
				echo '<td>'.common::Link($title,'&#9660;','download='.htmlspecialchars($file),'title="'.$langmessage['Download'].'"').'</td>'.PHP_EOL;
				echo '<td style="text-align:right">'.filesize($addonPathData.'/'.$file).'</td></tr>'.PHP_EOL;
				$i++;
			}
			echo '</tbody>';
		}
		echo '</table><br/>'.PHP_EOL;
		echo '<input type="hidden" value="" name="todelete" />';
		$x= $this->getDirectorySize($addonPathData);
		echo '<div style="float:right">&#8721; '.$this->sizeFormat($x['size']).'</div>';
		echo '<input type="checkbox" name="cb_checkall" id="cb_checkall" onclick="checkUncheckAll(this);"/><label for="cb_checkall">'.$this->msg['cb_checkall'].'</label>';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="purge_files" value="'.$this->msg['purge'].'" />'.PHP_EOL;
		echo '</form>'.PHP_EOL;
		echo '</div>';
	}

	function manually_set()
	{
		global $addonPathData;
		echo '<div style="margin:1em;">';
		echo '<div><span style="font-size:16px;font-weight:bold">'.$this->msg['manually_set'].'</div>'.PHP_EOL;
		echo '<form action="'.common::GetUrl('Admin_Counter').'" method="post">'.PHP_EOL;
		echo '<table style="margin-top:20px">';
		echo '<tr><td><input type="radio" name="cstart" value="cd" id="cd" /></td>';
		echo '<td><label for="cd">'.$this->msg['counted_days'].'</label></td>';
		$counted_days = round(($this->now-$this->config['start_time']+1)/86400,2);
		echo '<td><input name="counted_days" value="'.$counted_days.'" onclick="this.form.cstart[0].checked=\'true\'" /></td></tr>'.PHP_EOL;
		echo '<tr><td><input type="radio" name="cstart" value="st" id="st" checked="checked" /></td>';
		echo '<td><label for="st">'.$this->msg['start_time'].'</label></td>';
		echo '<td><input name="start_time" value="'.$this->config['start_time'].'" onclick="this.form.cstart[1].checked=\'true\'" /> ~ '.date($this->config['date_format'].' '.$this->config['time_format'],$this->config['start_time']).'</td></tr>'.PHP_EOL;
		echo '<tr><td></td><td>'.$this->msg['last_update'].'</td><td><input name="last_update" value="'.$this->config['last_update'].'"/> ~ '.date($this->config['date_format'].' '.$this->config['time_format'],$this->config['last_update']).'</td></tr>'.PHP_EOL;
		echo '<tr><td colspan="3"><input type="submit" name="save_times" value="'.$this->msg['save'].'" /></td></tr>'.PHP_EOL;
		echo '</table></form>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
	}

	function referers()
	{
		global $addonPathData,$langmessage,$page, $addonRelativeCode;
		$page->head_js[] = $addonRelativeCode.'/sort.js';
		$this->ShowMenu();
		echo '<div style="margin:1em;">';
		$referers = $this->load_data($addonPathData.'/referers.php');//index>time+ref
		$qq=array();//queries counter for google,bing,icq etc
		$rr=array();////queries with time
		if (count($referers)){//ifcount
		foreach ($referers as $i=>$d)
		{
			$r=rawurldecode(str_replace('+',' ',$d['ref']));
			if ($r!='')//not empty referer
			{
				$q='-';
				//try to find a query
				$s=explode('?',$r,2);
				if (isset($s[1]))
				{
					$s=explode('&',$s[1]);
					foreach ($s as $p)
					{
						if (strlen($p)>2 && $p[0]=='q' && $p[1]=='=')
						{
							$q=substr($p,2);
						}
					}
				}
				//query counter
				if (isset($qq[$q]))
				{
					$qq[$q]++;
				}
				else
				{
					$qq[$q]=1;
				}
				//query times
				$rr[]=array('q'=>$q,'t'=>$d['time']);//query and time
			}
		}
		//arsort($qq);
		//var_export($gr);
		//var_export($or);
		//var_export($qq);
		echo '<div style="float:right;text-align:right">';//menu
		echo '<a href="'.common::GetUrl('Admin_Counter').'?referers">'.$this->msg['queries'].'</a> / ';
		echo '<a href="'.common::GetUrl('Admin_Counter').'?referers=expand">'.$langmessage['... Expand All'].'</a> / ';
		echo '<a href="'.common::GetUrl('Admin_Counter').'?referers=referers">'.$this->msg['referers'].'</a>';
		echo '</div>';
		echo '<span style="font-size:16px;font-weight:bold;">';
		if ($_GET['referers']=='') //queries (default)
		{
			echo $this->msg['queries'].'</span><br/><br/>'.PHP_EOL;
			echo '<table id="collapsed_queries" style="width:100%"><thead><tr>';
			echo '<th onclick="sorter.sort(\'collapsed_queries\',0,2);" style="cursor:pointer;text-align:left;width:10%">&#8721;</th>';
			echo '<th onclick="sorter.sort(\'collapsed_queries\',1,1);" style="cursor:pointer;text-align:left">'.$this->msg['query'].'</th></tr></thead><tbody>';
			foreach ($qq as $q=>$c)
			{
				echo '<tr><td>'.$c.'</td><td>'.htmlentities($q,ENT_COMPAT,'UTF-8').'</td></tr>'.PHP_EOL;
			}
			echo '</tbody></table>';
		}
		elseif ($_GET['referers']=='expand') //expand all queries
		{
			echo $this->msg['queries'].' '.$langmessage['... Expand All'].'</span><br/><br/>'.PHP_EOL;
			echo '<table id="expanded_queries" style="width:100%"><thead>'.PHP_EOL;
			echo '<tr>'.PHP_EOL;
			echo '<th onclick="sorter.sort(\'expanded_queries\',0,2);" style="cursor:pointer;width:10%;text-align:left">#</th>'.PHP_EOL;
			echo '<th onclick="switchtime(\'expanded_queries\')" style="cursor:pointer;width:40%;text-align:left">'.htmlspecialchars($this->msg['time']).'</th>'.PHP_EOL;
			echo '<th onclick="sorter.sort(\'expanded_queries\',2,1);" style="cursor:pointer;width:50%;text-align:left">'.htmlspecialchars($this->msg['query']).'</th>'.PHP_EOL;
			echo '</tr></thead><tbody>'.PHP_EOL;
			foreach ($rr as $i=>$d)
			{
				echo '<tr>';
				echo '<td>'.($i+1).'</td>';
				echo '<td class="time" title="'.$d['t'].'">'.date($this->config['date_format'].' '.$this->config['time_format'],$d['t']).'</td>'; //date
				echo '<td>'.htmlentities($d['q'],ENT_COMPAT,'UTF-8').'</td>';
				echo '</tr>'.PHP_EOL;
			}
			if (count($rr)==0)
				echo '<tr><td colspan="4"></td></tr>';
			echo '</tbody>';
			echo '</table>';
		}
		elseif ($_GET['referers']=='referers') //show full referers
		{
			echo $this->msg['referers'].'</span><br/><br/>'.PHP_EOL;
			echo '<table id="full_referers" style="width:100%;border:2px solid #aaa;border-spacing:5px">';
			echo '<thead><tr>';
			echo '<th onclick="sorter.sort(\'full_referers\',0,2);" style="cursor:pointer;text-align:left;min-width:1em">#</th>';
			echo '<th onclick="switchtime(\'full_referers\')" style="cursor:pointer;text-align:left;min-width:12em">'.htmlspecialchars($this->msg['time']).'</th>';
			echo '<th onclick="sorter.sort(\'full_referers\',2,1);" style="cursor:pointer;text-align:left;">'.htmlspecialchars($this->msg['referrer']).'</th></tr></thead><tbody>';
			foreach ($referers as $i=>$d)
			{
				//if ($d['ref']=='') continue;
				echo '<tr><td>'.($i+1).'</td>';
				echo '<td class="time" title="'.$d['time'].'">'.date($this->config['date_format'].' '.$this->config['time_format'],$d['time']).'</td>';
				echo '<td>'.htmlentities(rawurldecode($d['ref']),ENT_COMPAT,'UTF-8').'</td></tr>'.PHP_EOL;
			}
			echo '</tbody></table>';
		}
		echo '<script type="text/javascript"> function switchtime(table) { $(\'#\'+table+\' .time\').each(function(i){ var a=this.getAttribute(\'title\'); this.setAttribute(\'title\',this.innerHTML); this.innerHTML=a; }); } </script>';

		}//ifcount
		echo '</div>';
	}

	function parser_update($action)
	{
		global $addonPathData;
		echo '<div style="margin:1em;">';
		echo '<div style="font-size:16px;font-weight:bold">'.$this->msg['parser_update'].'</div><br/>'.PHP_EOL;
		echo $this->msg['parser_update_local'].' '.$this->config['uasparser_local_version'].'<br/><br/>'.PHP_EOL;
		if ($action=='')
		{
			echo '&#9658; '.common::Link('Admin_Counter',$this->msg['parser_update_check'],'parser_update=check_version',' name="gpabox"').'<br/><br/>'.PHP_EOL;
			echo common::Link('Admin_Counter',$this->msg['back'],'settings').'<br/>';
			return;
		}
		//if (!ini_get('allow_url_fopen'))
		//	echo 'Error: function file_get_contents not allowed URL open.'; return;
		if ($action=='check_version')
		{
			//$ctx = stream_context_create(array('http' => array('timeout' => 5)));
			//$ver = @file_get_contents($this->VerUrl, 0, $ctx);
			includeFile('tool/RemoteGet.php');
			$ver = gpRemoteGet::Get_Successful($this->VerUrl);
			if ($ver==false)
			{
				echo 'Connection error occured. Try it again later.';
			}
			elseif ($ver==$this->config['uasparser_local_version'])
			{
				echo $this->msg['parser_update_not_needed'].'<br/><br/>';
				echo $this->msg['parser_update_website'].' <a href="'.$this->InfoUrl.'" title="" target="_blank">'.$this->InfoUrl.'</a><br/><br/>';
				echo common::Link('Admin_Counter',$this->msg['back'],'settings').'<br/>';
			}
			else
			{
				echo $this->msg['parser_update_available'].': '.$ver.'<br/><br/>'.$this->msg['parser_update_confirm'].' ';
				echo common::Link('Admin_Counter',$this->msg['yes'],'parser_update=update_now',' name="gpabox"');
				echo '&nbsp; &nbsp; &#9668; &#9658; &nbsp; &nbsp;';
				echo common::Link('Admin_Counter',$this->msg['no'],'parser_update',' name="gpabox"').'<br/>'.PHP_EOL;
			}
		}
		if ($action=='update_now')
		{
			//$ctx = stream_context_create(array('http' => array('timeout' => 5)));
			//$ini = @file_get_contents($this->IniUrl, 0, $ctx))
			includeFile('tool/RemoteGet.php');
			$ini = gpRemoteGet::Get_Successful( $this->IniUrl );
			if ($ini)
			{
				$md5hash = gpRemoteGet::Get_Successful( $this->md5Url );
				if(md5($ini) == $md5hash)
				{
					@file_put_contents($addonPathData.'/uasdata.ini', $ini);
					$ver = gpRemoteGet::Get_Successful( $this->VerUrl );
					$this->config['uasparser_local_version']=$ver;
					$this->save_data($this->configFile,$this->config);
					echo $this->msg['parser_update_succesfull'].'<br/><br/>';
					echo $this->msg['parser_update_local'].' '.$ver.'<br/><br/>';
					echo $this->msg['parser_update_website'].' <a href="'.$this->InfoUrl.'" title="" target="_blank">'.$this->InfoUrl.'</a><br/><br/>';
					echo common::Link('Admin_Counter',$this->msg['back']).PHP_EOL;
				}
				else
				{
					echo $this->msg['parser_update_hash_error'].'<br/>'.$this->msg['parser_update_website'].' <a href="'.$this->InfoUrl.'" title="" target="_blank">'.$this->InfoUrl.'</a><br/>';
				}
			}
		}
		echo '</div>';
	}

	function ShowVisitsTable() //displays table
	{
		global $page,$addonRelativeCode;
		$page->head .= '<style type="text/css"> #ds_table { border-collapse:collapse; } .hover,.hover1 {background-color:#'.$this->config['bg_color'].';color:#'.$this->config['text_color'].'} div.bar {background-color:#'.$this->config['bar_color'].'} </style>'.PHP_EOL;
		$page->head_js[] = $addonRelativeCode.'/sort.js';
		$page->head_js[] = $addonRelativeCode.'/visits.js';
		echo '<table id="ds_table" cellpadding="2" style="width:100%; border:1px solid #000; text-align:right">'.PHP_EOL;
		echo '<colgroup><col/><col/><col/><col/><col/><col/><col/>';
		echo '<col/><col/><col/><col/><col/><col/><col/></colgroup>';
		echo '<thead style="background-color:#eff;"><tr style="height:3.5em">'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',0,2);SetClicks()" style="cursor:pointer;width:15%" title="">&nbsp;</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',1,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter1'].'">1</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',2,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter2'].'">2</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',3,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter3'].'">3</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',4,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter4'].'">4</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',5,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter5'].'">5</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',6,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter6'].'">6</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',7,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter7'].'">7</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',8,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter8'].'">8</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',9,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter9'].'">9</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',10,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter10'].'">10</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',11,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter11'].'">11</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',12,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter12'].'">12</th>'.PHP_EOL;
		echo '<th onclick="sorter.sort(\'ds_table\',13,2);SetClicks()" style="cursor:pointer" title="'.$this->msg['at_filter13'].'">13</th>'.PHP_EOL;
		echo '</tr></thead><tbody>'.PHP_EOL;
		$this->days = array_reverse($this->days,true); //reverse
		//var_export($this->days);
		$db = ceil(($this->config['last_update'] - $this->config['start_time'])/86400); //days back
		if ($db>$this->config['max_visits'])
			$db=$this->config['max_visits'];
		$bound = $this->today_end-$db*86400;
		$sum = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0); //for agents
		$max = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0); //for agents
		$bb='';//second bars body
		foreach ($this->days as $et=>$day)
		{
			if ($et<$bound)
				continue; //until time of first displayable record
			$s='<tr><td><span style="display:none">'.$et.'</span>'.date($this->config['date_format'],$et-36000).'</td>'.PHP_EOL;
			echo $s;
			$bb.=$s.'<td colspan="13"><div class="bar">&nbsp;</div></td></tr>'.PHP_EOL;
			$sum[0]++;
			for ($i=1;$i<=13;$i++) //print and sum different agents
			{
				echo '<td>'.$day[$i].'</td>'.PHP_EOL;
				$sum[$i] += $day[$i];
				if ($max[$i]<$day[$i])
					$max[$i]=$day[$i];
			}
			echo '</tr>'.PHP_EOL;
		}
		echo '</tbody><tbody style="display:none">'.$bb.'</tbody>'; //second body for graphs
		echo '<tbody><tr><td>Total</td>'; //third body for summary stats
		$maxtotal=0;
		for ($i=1;$i<=13;$i++) {
			echo '<td>'.$sum[$i].'</td>';
			if ($maxtotal<$max[$i]) $maxtotal=$max[$i];
		}
		echo '</tr><tr><td>'.$this->msg['average'].'</td>';
		$sum0= $sum[0]?$sum[0]:1;//only prevent division0
		for ($i=1;$i<=13;$i++)
			echo '<td>'.round($sum[$i]/$sum0,1).'</td>';
		echo '</tr></tbody><tbody><tr><td>&nbsp;</td>';
		if ($maxtotal==0) $maxtotal=1;
		for ($i=1;$i<=13;$i++)
		{
			$shade=100+round(100*($maxtotal-$sum[$i])/$maxtotal);
			echo '<td onclick="ShowBars('.$i.','.$max[$i].')" style="background-color:rgb('.$shade.','.$shade.','.$shade.');cursor:pointer"></td>';
		}
		echo '</tr></tbody></table>'.PHP_EOL;
		echo '<div>'.$this->msg['days'].': '.$sum[0].'</div>';
	}

	function ShowVisits()
	{
		global $addonPathData,$langmessage,$page,$addonFolderName;

		$page->head_js[] = '/data/_addoncode/'.$addonFolderName.'/jscolor/jscolor.js';
		$this->ShowMenu();
		echo '<div style="margin:1em">';

		echo '<div style="font-size:16px;font-weight:bold">'.$this->msg['visits'].'</div><br/>'.PHP_EOL;

		if (file_exists($addonPathData.'/visits.php'))
		{
			$this->days = $this->load_data($addonPathData.'/visits.php');
			//var_export($this->days);
			if (count($this->days))
			{
				$this->ShowVisitsTable();
			}
			else
			{
				echo $this->msg['no_visits'].'<br/>';
			}
		}
		else
		{
			echo $this->msg['no_visits'].'<br/>';
		}
		echo '<form name="ds_setup" action="'.common::GetUrl('Admin_Counter').'?visits" method="post" style="margin-top:40px">'.PHP_EOL;
		echo '<table style="border: 1px solid #eee; border-collapse:collapse">';
		echo '<tr>';
		echo '<td><label for="max_visits">'.$this->msg['visits_count'].'</label></td>';
		echo '<td><input type="text" name="max_visits" id="max_visits" value="'.$this->config['max_visits'].'" size="7" maxlength="7" /></td>'.PHP_EOL;
		echo '</tr>';
		echo '<tr>';
		echo '<td colspan="2" style="cursor:pointer" onclick="$(this).parent().parent().find(\'tr\').each(function(i){ if (i==2 || i==3 || i==4) $(this).toggle(); });">'.$langmessage['more_options'].'</td>';
		echo '</tr>';
		echo '<tr style="display:none">';
		echo '<td><label for="bar_color">'.$this->msg['bar_color'].'</label></td>';
		echo '<td><input type="text" id="bar_color" name="bar_color" class="color" value="'.$this->config['bar_color'].'" /></td>';
		echo '</tr>'.PHP_EOL;
		echo '<tr style="display:none">';
		echo '<td><label for="bg_color">'.$this->msg['bg_color'].'</label></td>';
		echo '<td><input type="text" id="bg_color" name="bg_color" class="color" value="'.$this->config['bg_color'].'" /></td>';
		echo '</tr>'.PHP_EOL;
		echo '<tr style="display:none">';
		echo '<td><label for="text_color">'.$this->msg['text_color'].'</label></td>';
		echo '<td><input type="text" id="text_color" name="text_color" class="color" value="'.$this->config['text_color'].'" /></td>';
		echo '</tr>'.PHP_EOL;
		echo '<tr>';
		echo '<td>&nbsp;<input type="submit" name="save_visits_settings" value="'.$this->msg['save'].'" /></td>'.PHP_EOL;
		echo '</tr>';
		echo '</table>';
		echo '</form>'.PHP_EOL;
		echo '</div>';
	}

	function Get_Label($req)
	{
		global $config;
		//echo $req.'<br/>';
		//code taken from common::WhichPage
		$title1=common::CleanRequest($req);
		$params='';
		$pos=strpos($title1,'?');
		if ($pos!==false)
		{
			$params=substr($title1,$pos+1);
			$title1=substr($title1,0,$pos);
		}
		$title1 = gp_edit::CleanTitle($title1);
		if(empty($title1))
			$title1=$config['homepath'];
		$label=common::GetLabel($title1);
		return array(0=>$label,1=>$params);
	}

	function ShowMenu() // top menu
	{
		global $page, $addonRelativeCode, $langmessage;
		if (!isset($this->config['menu_icon_set']) || $this->config['menu_icon_set']==0) {
			$img = array('nonewdata.png', 'newdata.png', 'visits.png', 'pages.png', 'referers.png', 'visitors.png', 'config.png'  ); // feniweb set
		} else {
			$img = array('nonewdata1.png', 'newdata1.png', 'visits1.png', 'pages1.png', 'referers1.png', 'visitors1.png', 'config1.png'  ); //classic set
		}
		$new_data_yesno = (file_exists($this->dataFile) && (filemtime($this->dataFile)>$this->config['last_update']))? 1:0;
		$page->head .= '<style type="text/css"> #counter-menu-table td { padding:1em; background-color: #fff; } #counter-menu-table td:hover { background-color: #eee; } </style>'.PHP_EOL;
		echo '<table id="counter-menu-table" cellspacing="0" cellpadding="8" style="float:right; font-weight:bold; width:100%">';
		echo '<tr>';
		echo '<td style="border-right:1px solid #eee; text-align:center; vertical-align:middle"> <a href="'.common::GetUrl('Admin_Counter').'"> <img src="'.$addonRelativeCode.'/img/'.$img[$new_data_yesno].'" alt="" /> <br/> '.$this->msg['new_data'].' </a> </td>'.PHP_EOL;
		echo '<td style="border-right:1px solid #eee; text-align:center"> <a href="'.common::GetUrl('Admin_Counter').'?visits"> <img src="'.$addonRelativeCode.'/img/'.$img[2].'" alt="" /> <br/> '.$this->msg['visits'].' </a> </td>'.PHP_EOL;
		echo '<td style="border-right:1px solid #eee; text-align:center"> <a href="'.common::GetUrl('Admin_Counter').'?requested_pages"> <img src="'.$addonRelativeCode.'/img/'.$img[3].'" alt="" /> <br/> '.$this->msg['pages'].' </a> </td>'.PHP_EOL;
		echo '<td style="border-right:1px solid #eee; text-align:center"> <a href="'.common::GetUrl('Admin_Counter').'?referers"> <img src="'.$addonRelativeCode.'/img/'.$img[4].'" alt="" /> <br/> '.$this->msg['referers'].' </a> </td>'.PHP_EOL;
		echo '<td style="border-right:1px solid #eee; text-align:center"> <a href="'.common::GetUrl('Admin_Counter').'?show_ip_table=0"> <img src="'.$addonRelativeCode.'/img/'.$img[5].'" alt="" /> <br/> '.$this->msg['visitors'].' </a> </td>'.PHP_EOL;
		echo '<td style="border-right:1px solid #eee; text-align:center"> <a href="'.common::GetUrl('Admin_Counter').'?settings"> <img src="'.$addonRelativeCode.'/img/'.$img[6].'" alt="" /> <br/> '.$langmessage['Settings'].' </a> </td>'.PHP_EOL;
		echo '</tr></table>';//--end of top-right menu
		echo '<div style="clear:both; float:none; padding:0.5em 1em; text-align:right">'.$this->msg['last_update'].' : '.date($this->config['date_format'].' '.$this->config['time_format'],$this->config['last_update']).'</div>';
	}

	function ShowRequestedPages()
	{
		global $addonPathData,$page, $gp_titles,$gp_index,$config,$addonRelativeCode;
		$this->pages = $this->load_data($addonPathData.'/pages.php');//page>stats
		$page->head .= '<style type="text/css"> #rp_table { border-collapse:collapse; } .hover,.hover1 {background-color:#'.$this->config['bg_color'].';color:#'.$this->config['text_color'].'} div.bar {background-color:#'.$this->config['bar_color'].'} </style>'.PHP_EOL;
		$page->head_js[] = $addonRelativeCode.'/checkboxes.js';
		$page->head_js[] = $addonRelativeCode.'/sort.js';
		$page->head_js[] = $addonRelativeCode.'/pages.js';
		$this->ShowMenu();
		//var_export($this->config['atp']);
		echo '<div style="margin:1em">';
		echo '<div id="user_agent_name" style="float:right;font-weight:bold"></div>';
		echo '<div style="font-size:16px;font-weight:bold">'.$this->msg['pages'].'</div><br/>'.PHP_EOL;
		echo '<a onclick="SwapDayTal(this)" title="'.$this->msg['today_pageviews'].'">'.$this->msg['total_pageviews'].'</a><br/>';
		echo '<form action="'.common::GetUrl('Admin_Counter').'?requested_pages" method="post">'.PHP_EOL;
		echo '<table id="rp_table" cellpadding="2" style="width:100%; border:1px solid #ccc; text-align:center">'.PHP_EOL;
		echo '<col/><col width="30%"/><col/><col span="13"/>';
		echo '<thead><tr>'.PHP_EOL;
		echo '<th style="cursor:pointer" onclick="sorter.sort(\'rp_table\',0,2);SetClicks()" title="">#</th>'.PHP_EOL;
		echo '<th style="cursor:pointer" onclick="sorter.sort(\'rp_table\',1,1);SetClicks()" colspan="2" title="">'.$this->msg['page'].'</th>'.PHP_EOL;
		$ac=13; //count($this->uati)
		$adp = array(0,0,0,0,0,0,0,0,0,0,0,0,0); //agent today pageviews for all pages (sums)
		for ($i=0;$i<$ac;$i++)
			echo '<th style="cursor:pointer" onclick="sorter.sort(\'rp_table\','.(3+$i).',2);SetClicks()" title="'.$this->msg['at_filter'.(1+$i)].'">'.(1+$i).'</th>'.PHP_EOL;
		echo '<th style="text-align:center;">X</th>'.PHP_EOL;
		echo '</tr></thead><tbody>'.PHP_EOL;
		if (count($this->pages))
		{
			$i=0;
			foreach ($this->pages as $key => $value)
			{
				if (($this->config['hide_admin_pages']==false) || (strpos($key,'/Admin')===false))
				{
					$i++;
					echo '<tr>';
					echo '<td>'.$value[0].'</td>';
					echo '<td style="text-align:left;padding-left:1em;word-wrap:break-word;max-width:100px;" title="'.htmlentities($key,ENT_COMPAT,'UTF-8').'">';
					$label=$this->Get_Label($key);
					echo htmlentities($label[0],ENT_COMPAT,'UTF-8');
					if ($label[1]!='')
						echo '<i>?'.htmlentities($label[1],ENT_COMPAT,'UTF-8').'</i>';//additional parameters
					echo '</td>';
					echo '<td><a href="'.htmlspecialchars($key).'" title="'.htmlspecialchars(rawurldecode($key)).'">&#187;</a></td>';
					for ($j=0; $j<$ac; $j++)
					{
						echo '<td title="'.$value[14+$j].'">'.$value[1+$j].'</td>';
						$adp[$j] += $value[14+$j];
					}
					echo '<td style="text-align:center;"><input type="checkbox" name="delete'.$value[0].'" /></td></tr>'.PHP_EOL;
				}
			}
		}
		echo '</tbody><tbody><tr><td></td><td></td><td>&#8721;</td>';
		for ($i=0; $i<$ac; $i++)
		{
			echo '<td title="'.$adp[$i].'">'.$this->config['atp'][$i+1].'</td>';
		}
		echo '<td></td></tr>';
		echo '</tbody><tbody><tr><td colspan="16" style="text-align:right"><input type="submit" name="remove_selected_pages" value="'.$this->msg['remove'].'" onclick="javascript:return confirm(\''.$this->msg['confirm'].'\')" /></td>';
		echo '<td><input type="checkbox" id="cb_checkall" name="cb_checkall" onclick="checkUncheckAll(this);"/></td></tr>'.PHP_EOL;
		echo '</tbody></table>'.PHP_EOL;
		echo '</form>'.PHP_EOL;
		echo '<div style="margin-top:20px"><form action="'.common::GetUrl('Admin_Counter').'?requested_pages" method="post">'.PHP_EOL;
		echo '<fieldset>';
		echo '<label for="hide_admin_pages">'.$this->msg['hide_admin_pages'].'</label><input type="checkbox" id="hide_admin_pages" name="hide_admin_pages" '. ($this->config['hide_admin_pages']==true?'checked="checked"' : '') .'/><br/>'.PHP_EOL;
		echo '&nbsp;<input type="submit" name="save_requested_pages_settings" value="'.$this->msg['save'].'" />'.PHP_EOL;
		echo '</fieldset>';
		echo '</form></div>'.PHP_EOL;
		$page->head .= '<style type="text/css"> #rp_table tr { background:#fff; } #rp_table tr:hover { background:#eee; } </style>';
		echo '</div>';
	}


	function new_data($update)
	{
		global $addonPathData,$addonPathCode,$langmessage,$page,$addonRelativeCode;
		$page->head_js[] = $addonRelativeCode.'/sort.js';
		$this->ShowMenu();
		echo '<div style="margin:1em;">';
		echo '<span style="font-size:16px;font-weight:bold">'.$this->msg['new_data'].'</span><br/><br/>'.PHP_EOL;
		if ($update && file_exists($this->dataFile))
		{
			//var_export($_GET);return;
			$this->config['clear_visits'] = isset($_GET['clear_visits']) ? true : false;
			$this->config['clear_pages'] = isset($_GET['clear_pages']) ? true : false;
			$this->config['clear_referers'] = isset($_GET['clear_referers']) ? true : false;
			$this->config['clear_agents'] = isset($_GET['clear_agents']) ? true : false;
			$this->config['clear_visitors'] = isset($_GET['clear_visitors']) ? true : false;
			$this->config['after_update'] = $_GET['after_update']; //'clear' or 'rename' or do 'nothing'
			$this->config['test_old_records'] = isset($_GET['test_old_records']) ? true : false;
			include_once($addonPathCode.'/UASparser.php');
			$parser = new UASparser();
			//d1-d6=array(key>content)
			$d1 = $this->load_data($addonPathData.'/ips.php'); //ip > stats(TYPE,LASTIME...)
			$d2 = $this->load_data($addonPathData.'/pages.php'); //page > 0pgindex, 1-26stats(TP1...TP13, DP1...DP13resetable)
			$d3 = $this->load_data($addonPathData.'/visits.php'); //et(dayendtime) > stats(DV1..DV13)
			$d4 = $this->load_data($addonPathData.'/agents.php'); //at1..at13> a,af,o,of> {name=>count} ;only for beginning visits
			$d5 = $this->load_data($addonPathData.'/referers.php'); //index>time+ref
			$d6 = $this->load_data($addonPathData.'/domains.php'); //ip>dns =here used to test for some fake ua strings of search.msn.com's and some googlebot's
			$atp = $this->config['atp']; //agent total pageviews - like d2 columns summed for all pages (0..13, 0th=unused index)
			$atv = $this->config['atv']; //agent total visits - like d3 columns summed for all days (0..13, 0th=unused index)
			if ($this->config['clear_visitors']) { $d1=array(); }
			if ($this->config['clear_pages']) { $d2=array(); $atp = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0); }
			if ($this->config['clear_visits']) { $d3=array(); $atv = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0); }
			if ($this->config['clear_agents']) { $d4=array(); }
			if ($this->config['clear_referers']) { $d5=array(); }
			if (count($d2) && $this->config['last_update']<$this->today_start) //if new day began
			{
				foreach ($d2 as $p=>$stats)
				{
					for ($i=0;$i<13;$i++)
						$d2[$p][14+$i]=0;  // zeroize today stats for each page for all 13 agents
				}
			}
			$fp = fopen($this->dataFile,'rb');
			flock($fp, LOCK_SH);
			$first=fgets($fp);//protection string
			if (!$this->config['test_old_records'])
				fseek($fp,$this->config['last_upsize'],SEEK_SET);
			//while (!feof($fp)){ $first=fgets($fp); echo ftell($fp).htmlspecialchars($first).'<br/>'; } return;

			$e = $this->today_end - $this->config['start_time']; //cca elapsed seconds
			$d = ceil($e/86400)-1; //cca elapsed days
			$et = $this->today_end - 86400*$d; //endtime of the first counted day
			if (!isset($d3[$et]))
			{
				$j=13;//count($this->uati)
				for ($i=1; $i<=$j;$i++)
					$d3[$et][$i]=0; //zeroize visits for the day ending at $et
			}
			$ir=count($d5);//index for referers
			$rt=$rp=$rc=0; // Total, Processed and Corrupted records inside the log file
			//set_time_limit(0);
			while (!feof($fp))
			{
				$s=rtrim(fgets($fp));
				if ($s=='') continue;
				$rt++;
				$r = @unserialize($s); //record[] 0=ip,1=time,2=title,3=agent,4=referer.
				if ($r===false) //if unserialize failed
				{
					echo '<div>'.$this->msg['corrupted_record'].' ('.$rt.') '.htmlspecialchars($s).'</div>';
					$rc++;
					continue; //skip corrupted record
				}
				if ($r[1]<=$this->config['last_update'])
					continue;
				$rp++;
				while ($et<$r[1])
				{
					$et+=86400; //seeking $et (finish of day) 24hours ahead
					if (!isset($d3[$et]))
					{
						$j=13; //count($this->uati)
						for ($i=1; $i<=$j;$i++)
							$d3[$et][$i]=0; //zeroize visits for the day ending at $et
					}
				}
				$sd = $et-86400; //start of examined day
				//d1 - update the list of ip addresses
				if (!isset($d1[$r[0]]))//new visitor in ip table
				{
					// gets information about user agent
					$ret = $parser->Parse($r[3]); // get information about user agent
					if (isset($d6[$r[0]]) && (strpos($d6[$r[0]], 'search.msn.com')!==false || strpos($d6[$r[0]], 'googlebot.com')!==false))
						{ $ret['typ'] = cat_filter1; } //"Robot"//
					//echo $ret['ua_name'].$r[3].'<br/>';
					$d1[$r[0]][0] = $r[1];//time of last requested page
					$d1[$r[0]][1] = $ret['typ'];//ua type
					$d1[$r[0]][2] = 1;//total pvs
					//variables 3-9 are used only for faster processing!
					$d1[$r[0]][3] = $r[3]; //temp uas
					$d1[$r[0]][4] = $ret['ua_family']; //temp
					$d1[$r[0]][5] = $ret['ua_name']; //temp
					$d1[$r[0]][6] = $ret['ua_icon']; //temp
					$d1[$r[0]][7] = $ret['os_family']; //temp
					$d1[$r[0]][8] = $ret['os_name']; //temp
					$d1[$r[0]][9] = $ret['os_icon']; //temp
					$atv[$this->uati[$ret['typ']]]++; //add total visit for agent type (counted on index 1..13)
					$d3[$et][$this->uati[$ret['typ']]]++; //add today visit for agent type (counted on index 1..13)
					//$d4 =3d array of useragents: at1..at13> ua,uaf,os,osf> label={count,icon}
					$x =& $d4[$this->uati[$ret['typ']]];
					if (!isset($x['ua'][$ret['ua_name']]))
					{
						$x['ua'][$ret['ua_name']]= array(0=>1, 1=>$ret['ua_icon']);
					}
					else
					{
						$x['ua'][$ret['ua_name']][0]++; //add visit
					}
					if (!isset($x['uaf'][$ret['ua_family']]))
					{
						$x['uaf'][$ret['ua_family']]= array(0=>1, 1=>$ret['ua_icon']); //0=visits,1=icon
					}
					else
					{
						$x['uaf'][$ret['ua_family']][0]++; //add visit
					}
					if (!isset($x['os'][$ret['os_name']]))
					{
						$x['os'][$ret['os_name']]= array(0=>1, 1=>$ret['os_icon']);
					}
					else
					{
						$x['os'][$ret['os_name']][0]++; //add visit
					}
					if (!isset($x['osf'][$ret['os_family']]))
					{
						$x['osf'][$ret['os_family']]= array(0=>1, 1=>$ret['os_icon']); //0=visits,1=icon
					}
					else
					{
						$x['osf'][$ret['os_family']][0]++; //add visit
					}
					if ($r[4]!='')
					{
						$d5[$ir]['time'] = $r[1];
						$d5[$ir]['ref'] = $r[4];
						$ir++;
					}
				}
				else //update existing visitor's record
				{
					if ($r[1] - $d1[$r[0]][0] > timeonline )//if new visit (for a day ending at $et)
					{
						$d3[$et][$this->uati[$d1[$r[0]][1]]]++; //add today visit for agent type (counted on index 1..13)
						$atv[$this->uati[$d1[$r[0]][1]]]++; //add total visit for agent type (counted on index 1..13)
						if ($r[4]!='')
						{
							$d5[$ir]['time'] = $r[1];
							$d5[$ir]['ref'] = $r[4];
							$ir++;
						}
					}
					if ($r[1] > $this->today_start)//if is today
					{
						if ( ($r[1] - $d1[$r[0]][0]) > timeonline) //if new visit for today
						{
							//$d1[$r[0]][2]++;//add today visit
						}
						//$d1[$r[0]][3]++;//today pvs
					}
					if (!isset($d1[$r[0]][3]) || $d1[$r[0]][3] != $r[3]) //if user agent string has changed
					{
						// gets information about user agent
						$ret = $parser->Parse($r[3]);
						if (isset($d6[$r[0]]) && (strpos($d6[$r[0]], 'search.msn.com')!==false) && (strpos($d6[$r[0]], 'googlebot.com')!==false))
							{ $ret['typ'] = cat_filter1; } //"Robot"//
						$d1[$r[0]][1] = $ret['typ'];
						//variables 3-9 are used only for faster processing!
						$d1[$r[0]][3] = $r[3]; //temp uas
						$d1[$r[0]][4] = $ret['ua_family']; //temp
						$d1[$r[0]][5] = $ret['ua_name']; //temp
						$d1[$r[0]][6] = $ret['ua_icon']; //temp
						$d1[$r[0]][7] = $ret['os_family']; //temp
						$d1[$r[0]][8] = $ret['os_name']; //temp
						$d1[$r[0]][9] = $ret['os_icon']; //temp
					}
					if ($r[1] - $d1[$r[0]][0] > timeonline ) // if new visit
					{
						//if ($ret['ua_name']!=$d1[$r[0]][5])//if switched to another agent

						//$d4 =3d array of useragents: at1..at13> ua,uaf,os,osf> label={count,icon}
						$x =& $d4[$this->uati[$d1[$r[0]][1]]];//agent type i
						if (!isset($x['ua'][$d1[$r[0]][5]]))
						{
							$x['ua'][$d1[$r[0]][5]]= array(0=>1, 1=>$d1[$r[0]][6]);
						}
						else
						{
							$x['ua'][$d1[$r[0]][5]][0]++; //add visit
						}
						if (!isset($x['uaf'][$d1[$r[0]][4]]))
						{
							$x['uaf'][$d1[$r[0]][4]]= array(0=>1, 1=>$d1[$r[0]][6]); //0=visits,1=icon
						}
						else
						{
							$x['uaf'][$d1[$r[0]][4]][0]++; //add visit
						}
						if (!isset($x['os'][$d1[$r[0]][8]]))
						{
							$x['os'][$d1[$r[0]][8]]= array(0=>1, 1=>$d1[$r[0]][9]);
						}
						else
						{
							$x['os'][$d1[$r[0]][8]][0]++; //add visit
						}
						if (!isset($x['osf'][$d1[$r[0]][7]]))
						{
							$x['osf'][$d1[$r[0]][7]]= array(0=>1, 1=>$d1[$r[0]][9]); //0=visits,1=icon
						}
						else
						{
							$x['osf'][$d1[$r[0]][7]][0]++; //add visit
						}
					}
					$d1[$r[0]][0] = $r[1]; //now can update time of last requested page
					$d1[$r[0]][2]++; //total pvs
				}

				//d2 - update pages
				if (isset($d2[$r[2]]))//request uri
				{
					$j=13; //count($this->uati)
					$i=$this->uati[$d1[$r[0]][1]];
					$d2[$r[2]][$i]++; // increase total requests
					if ($r[1] > $this->today_start)
						$d2[$r[2]][$i+$j]++; // increase also today request (on indexes 14..26)
				}
				else
				{
					$new_index=0;
					if (count($d2))
					{
						foreach ($d2 as $p => $info)
						{
							if ($new_index<$info[0]) //searchin for maximum
								$new_index=$info[0];
						}
						$new_index+=1;
					}
					$d2[$r[2]]=array();
					$d2[$r[2]][0]=$new_index;
					$j=13; //count($this->uati)
					for ($i=1; $i<=$j;$i++)
						$d2[$r[2]][$i]=$d2[$r[2]][$i+$j]=0;//set all to zero
					$i=$this->uati[$d1[$r[0]][1]];//1..13
					$d2[$r[2]][$i]=1; // total pageviews=1
					if ($r[1] > $this->today_start)
						$d2[$r[2]][$i+$j]=1; // also add first today pageview
				}
				$atp[$this->uati[$d1[$r[0]][1]]]++;
				//d3 - update visits statistics
				//d4 - update agents (done above)
				//d5 - update referers (done above)
				//echo '<br/>';var_export($r);
			}
			flock($fp,LOCK_UN);
			fclose($fp);
			$this->config['max_mem_usage'] = function_exists('memory_get_usage') ? memory_get_usage(true):0;
			unset($parser);

			foreach ($d1 as $ip=>$info)
			{ //remove redundant data
				unset($d1[$ip][3]);
				unset($d1[$ip][4]);
				unset($d1[$ip][5]);
				unset($d1[$ip][6]);
				unset($d1[$ip][7]);
				unset($d1[$ip][8]);
				unset($d1[$ip][9]);
			}
			$this->save_data($addonPathData.'/ips.php',$d1);//ip>stats
			$this->save_data($addonPathData.'/pages.php',$d2);//page>stats
			$this->save_data($addonPathData.'/visits.php',$d3);//dayendtime>stats
			$this->save_data($addonPathData.'/agents.php',$d4);//index>time+ua+os ,only one per visit
			$this->save_data($addonPathData.'/referers.php',$d5);//index>time+ref
			//set_time_limit(30);
			$this->config['atp']=$atp;
			$this->config['atv']=$atv;
			$this->config['last_update']=$this->now;
			$this->config['last_upsize']=filesize($this->dataFile); //=ftell($fp);
			if ($this->config['after_update']=='rename')
			{
				if (rename($this->dataFile, $addonPathData.'/log-'.$this->now.'.php'))
				{
					echo $this->msg['rename_log'].' - '.$this->msg['done'].' [log-'.$this->now.'.php]<br/>';
					$this->config['last_upsize']=strlen(protect);
				}
			}
			elseif ($this->config['after_update']=='clear')
			{
				if (unlink($this->dataFile)) // delete
				{
					echo $this->msg['clear_log'].' - '.$this->msg['done'].'<br/>';
					$this->config['last_upsize']=strlen(protect);
				}
			}
			$this->save_data($this->configFile, $this->config);
			echo sprintf($this->msg['processed'],$rp,$rt).'<br/><br/>';
		}
		if (file_exists($this->dataFile) && filemtime($this->dataFile)>$this->config['last_update'])
		{
			$fsize= $this->sizeFormat(filesize($this->dataFile));
			$growth= filesize($this->dataFile) - $this->config['last_upsize'];
			$growth= $growth>0? $this->sizeFormat($growth) : '';
			echo common::Link('Admin_Counter',$this->msg['new_data_available'],'new_data','id="start_processing" onclick="new_data_params()"').' ('.$growth.')';
		}
		elseif (!$update)
		{
			echo $this->msg['no_new_data']; //no records
		}
		echo '</div>';

		//settings
		echo '<div style="margin-top:3em; font-style:italic">';
		echo '<span onclick="$(this).next(\'form\').toggle()" style="cursor:pointer">'.$langmessage['Settings'].'</span>';
		echo '<form id="new_data_params" action="'.common::GetUrl('Admin_Counter').'" method="post" style="display:none;padding:1em 0">';
		echo '<i>'.$this->msg['before_update'].' ...</i>'.PHP_EOL;
		echo '<br/><input type="checkbox" name="clear_visits" '.($this->config['clear_visits']?'checked="checked"':'').' /> '.$this->msg['clear_visits'];
		echo '<br/><input type="checkbox" name="clear_pages" '.($this->config['clear_pages']?'checked="checked"':'').' /> '.$this->msg['clear_pages'];
		echo '<br/><input type="checkbox" name="clear_referers" '.($this->config['clear_referers']?'checked="checked"':'').' /> '.$this->msg['clear_referers'];
		echo '<br/><input type="checkbox" name="clear_visitors" '.($this->config['clear_visitors']?'checked="checked"':'').' /> '.$this->msg['clear_visitors'];
		echo '<br/><input type="checkbox" name="clear_agents" '.($this->config['clear_agents']?'checked="checked"':'').' /> '.$this->msg['clear_agents'];
		echo '<br/><input type="checkbox" name="test_old_records" '.($this->config['test_old_records']?'checked="checked"':'').' /> '.$this->msg['test_old_records'];
		echo '<br/><br/>';
		echo '<i>'.$this->msg['after_update'].' ...</i>'.PHP_EOL;
		echo '<br/><input type="radio" name="after_update"'.($this->config['after_update']=='nothing'?' checked="checked"':'').' value="nothing" id="do_nothing" /> <label for="do_nothing">'.$this->msg['do_nothing'].'</label>'.PHP_EOL;
		echo '<br/><input type="radio" name="after_update"'.($this->config['after_update']=='rename'?' checked="checked"':'').' value="rename" id="rename_log" /> <label for="rename_log">'.$this->msg['rename_log'].'</label>'.PHP_EOL;
		echo '<br/><input type="radio" name="after_update"'.($this->config['after_update']=='clear'?' checked="checked"':'').' value="clear" id="clear_log" /> <label for="clear_log">'.$this->msg['clear_log'].'</label>'.PHP_EOL;
		echo '<br/><br/>&#9658; '.common::Link('Admin_Counter',$this->msg['manually_set'],'manually_set','name="gpabox"').PHP_EOL;
		echo '</form></div>'.PHP_EOL;

		if (!file_exists($addonPathData.'/uasdata.ini'))
			echo '<p style="font-weight:bold">'.$this->msg['parser_update_available'].' - '.common::Link('Admin_Counter',$this->msg['parser_update'],'parser_update=update_now',' name="gpabox"').'</p>';
	}

	function download($file='')
	{
		global $addonPathData;
		$path = $addonPathData.'/'.$file;
		if ($file=='' || !file_exists($path))
			return;
		$fp = fopen($path,'rb');
		if ($fp === false)
			return;
		//set_time_limit(0);
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
			$file = preg_replace('/\./', '%2e', $file, substr_count($file, '.') - 1);
		while (@ob_end_clean());
		ob_start('ob_gzhandler'); //thanks to http://us2.php.net/manual/en/function.ob-end-clean.php#71092
		header("Content-Type: application/octet-stream");
		header("Content-Length: " .(string)(filesize($path)) );
		header('Content-Disposition: attachment; filename="'.$file.'"');
		header("Content-Transfer-Encoding: binary\n");
		while( (!feof($fp)) && (connection_status()==0) )
		{
			$text = fread($fp, 8192);
			print($text);
			ob_flush();
			flush();
			//sleep(1);
		}
		fclose($fp);
		exit(0);
	}

	function Run()
	{
		$this->LoadSettings();
		$this->LoadLanguage();
		$this->ProcessForm();
		parse_str($_SERVER['QUERY_STRING']);
		if (isset($download))
		{
			$this->download($download);
			$this->ShowMenu();
		}
		elseif (isset($manually_set))
			$this->manually_set();
		else if (isset($parser_update))
			$this->parser_update($parser_update);
		else if (isset($agents))
			$this->agents();
		else if (isset($purge))
			$this->purge();
		else if (isset($settings))
			$this->settings();
		else if (isset($screen_filter))
			$this->screen_filter();
		else if (isset($requested_pages))
			$this->ShowRequestedPages();
		else if (isset($referers))
			$this->referers();
		else if (isset($ip))
			$this->ShowIP();
		else if (isset($show_ip_table))
			{
				if (isset($show_ip_table))
					$this->ShowIPTable($show_ip_table);
				else
					$this->ShowIPTable(0);
			}
		elseif (isset($visits))
		{
			$this->ShowVisits();
		}
		else
		{
			$this->new_data(isset($new_data));
		}
	}

}

//echo "End of CounterAdmin.php";
