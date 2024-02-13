<?php defined('is_running') or die('Not an entry point...');

/* ##################################################### */
/* ## functions for gpEasy Mediaelement Player Plugin ## */
/* ##               J. Krausz, 2015-02                ## */
/* ##################################################### */

function MEJS_getHead() {

  global $page, $addonRelativeCode;

  $config = Load_Config();

  $page->css_user[] = $addonRelativeCode . '/mediaelement/mediaelementplayer.min.css';
  $page->css_user[] = $addonRelativeCode . '/mediaelement/mejs-skins.css';
  $page->head_js[] =  $addonRelativeCode . '/mediaelement/mediaelement-and-player.min.js';

  $mejs_options = "\n";
  $mejs_options .= " startVolume : " . $config['startVolume'] . ",\n";
  if ($config['defaultVideoWidth'] != '') { 
    $mejs_options .= " defaultVideoWidth : '" . $config['defaultVideoWidth'] . "',\n";
  }
  if ($config['defaultVideoHeight'] != '') {
    $mejs_options .= " defaultVideoHeight : '" . $config['defaultVideoHeight'] . "',\n";
  }
  if ($config['audioWidth'] != '') {
    $mejs_options .= " audioWidth : '" . $config['audioWidth'] . "',\n";
  }
  if ($config['audioHeight'] != '') {
    $mejs_options .= " audioHeight : '" . $config['audioHeight'] . "',\n";
  }
  $mejs_options .= ($config['loop'] == 'on')                      ? " loop : true,\n"                     :   " loop : false,\n";
  $mejs_options .= ($config['enableAutosize'] == 'on')            ? " enableAutosize : true,\n"           :   " enableAutosize : false,\n";
  $mejs_options .= ($config['alwaysShowControls'] == 'on')        ? " alwaysShowControls : true,\n"       :   " alwaysShowControls : false,\n";
  $mejs_options .= ($config['iPadUseNativeControls'] == 'on')     ? " iPadUseNativeControls : true,\n"    :   " iPadUseNativeControls : false,\n";
  $mejs_options .= ($config['iPhoneUseNativeControls'] == 'on')   ? " iPhoneUseNativeControls : true,\n"  :   " iPhoneUseNativeControls : false,\n";
  $mejs_options .= ($config['AndroidUseNativeControls'] == 'on')  ? " AndroidUseNativeControls : true,\n" :   " AndroidUseNativeControls : false,\n";
  $mejs_options .= ($config['enableKeyboard'] == 'on')            ? " enableKeyboard : true,\n"           :   " enableKeyboard : false,\n";
  $mejs_options .= ($config['pauseOtherPlayers'] == 'on')         ? " pauseOtherPlayers : true,\n"        :   " pauseOtherPlayers : false,\n";

  $mejs_fa = array();
  if ($config['show_playpause'] == 'on')    { $mejs_fa[] = '"playpause"'; }
  if ($config['show_progress'] == 'on')     { $mejs_fa[] = '"progress"'; }
  if ($config['show_current'] == 'on')      { $mejs_fa[] = '"current"'; }
  if ($config['show_duration'] == 'on')     { $mejs_fa[] = '"duration"'; }
  if ($config['show_tracks'] == 'on')       { $mejs_fa[] = '"tracks"'; }
  if ($config['show_volume'] == 'on')       { $mejs_fa[] = '"volume"'; }
  if ($config['allow_fullscreen'] == 'on')  { $mejs_fa[] = '"fullscreen"'; }

  $mejs_features = '[' . implode("," , $mejs_fa) . ']';
  $mejs_options .= "features : " . $mejs_features . "\n";

  if ($config['skin'] != 'default' && $config['skin'] != "" ) {
    $page->jQueryCode .= '$("video,audio").addClass("'.$config['skin'].'");';
  }

  if ($config['responsive'] == "on") {
    $page->jQueryCode .= '$(".GPAREA").not(".gp_editing").find("video,audio").css( { "width" : "100%" , "height" : "100%" } ).attr( { "width" : "100%" , "height" : "100%" } );';
  }

  $page->jQueryCode .= '$("video,audio").mediaelementplayer({'.$mejs_options.'});';
}


function Load_Config() {
  global $addonPathData;
  $config_file = $addonPathData.'/config.php';
  if (file_exists($config_file)) {
    include($config_file);
    return $config;
  } else {
    //default config
    $defaultconfig = array(
      'skin'                      =>   'default',      // default='', 'mejs-ted', 'mejs-wmp'
      'startVolume'               =>   '0.8',         // initial volume when the player starts
      'defaultVideoWidth'         =>   '512',          // if the <video width> is not specified, this is the default
      'defaultVideoHeight'        =>   '288',          // if the <video height> is not specified, this is the default
      'audioWidth'                =>   '100%',         // width of audio player
      'audioHeight'               =>   '30',           // height of audio player
      'loop'                      =>   'off',          // useful for <audio> player loops
      'enableAutosize'            =>   'on',           // enables Flash and Silverlight to resize to content size
      'show_playpause'            =>   'on',           
      'show_progress'             =>   'on',           
      'show_current'              =>   'off',             
      'show_duration'             =>   'on',           
      'show_tracks'               =>   'on',           
      'show_volume'               =>   'on',           
      'allow_fullscreen'          =>   'on',           
      'alwaysShowControls'        =>   'off',          // Hide controls when playing and mouse is not over the video
      'iPadUseNativeControls'     =>   'off',          // force iPad's native controls
      'iPhoneUseNativeControls'   =>   'off',          // force iPhone's native controls
      'AndroidUseNativeControls'  =>   'off',          // force Android's native controls
      'enableKeyboard'            =>   'on',           // turns keyboard support on and off
      'pauseOtherPlayers'         =>   'on',           // when this player starts, it will pause other players
      'responsive'                =>   'off'           // "on" scales player to max available size
    );
    return $defaultconfig;
  }
}



function CKEditor_addVideoAudioPlugins($plugins) {
  global $addonRelativeCode;
  $plugins['video'] = $addonRelativeCode . '/CKEditor_plugins/video/';
  $plugins['audio'] = $addonRelativeCode . '/CKEditor_plugins/audio/';
  return $plugins;
}

?>