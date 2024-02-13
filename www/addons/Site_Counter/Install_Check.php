<?php 
defined('is_running') or die('Not an entry point...');

function GetAddonKey($addon_id)
{
	global $config;
	if(empty($config['addons']))
		return false;
	foreach($config['addons'] as $addon_key => $addon_info){
		if( isset($addon_info['id']) && $addon_info['id'] == $addon_id )
			return $addon_key;
	}
	return false;
}

function Install_Check()
{
	global $config, $dataDir, $gpversion, $langmessage;
	$ok = version_compare(phpversion(),"5.0.0",'>=');
	echo '<p>'.$langmessage['PHP_Version'].' : '.phpversion();
	echo $ok ? ' &gt;=' : ' &lt;';
	echo ' 5.0.0 ';
	echo $ok ? $langmessage['Passed']:$langmessage['Failed'];
	echo ' </p>';
	//echo '<pre>'; var_export($config); echo '</pre>';
	$key=GetAddonKey(54);
	if ($key) //if plugin already installed
	{
		$ver = isset($config['addons'][$key]['version'])? 0+$config['addons'][$key]['version']:0;
		if ($ver<4)
		{
			echo '<p>This package is not compatible with the Counter '.$ver.'. Please uninstall it first for a fresh installation. </p>';
			$ok = false;
		}
	}
	return $ok;
}
