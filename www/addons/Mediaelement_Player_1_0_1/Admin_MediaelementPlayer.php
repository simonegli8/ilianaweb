<?php // encoding UTF-8 ÄÖÜäöüß

defined('is_running') or die('Not an entry point...');

class Admin_MediaelementPlayer {

  var $config; // stores site-wide player settings
  var $config_file;
  var $defaultconfig;

  function Admin_MediaelementPlayer() {
    global $addonPathData;
    $this->config_file = $addonPathData.'/config.php';
    $this->Load_Config();
    if (isset($_POST['save'])) { $this->Save_Config(); }
    $this->Show_Config();
  }

  function Show_Config() {
    global $langmessage, $addonRelativeCode;
    echo '<h2>Mediaelement Player » Settings</h2>';
    echo '<form method="post" action="'.common::GetUrl('Admin_MediaelementPlayer').'">';
    echo '<table class="bordered " style="width:100%;">';
    echo '<tr><th>Option</th><th>Value</th><th>Option</th><th>Value</th></tr>';
    $even = false; $tdc = 0; $i = 0;
    foreach ($this->config as $key => $value) {
      if ($tdc == 0) {
        echo '<tr';
        if ($even) { echo ' class="even" '; }
        echo '>';
      }
      if ($key=="startVolume") {
        echo '<td><span>startVolume (value between 0 and 1)</span></td>';
        echo '<td><input name="'.$key.'" size="2" id="'.$key.'" type="text" value="'.$value.'" /></td>';
      } elseif ($key=="loop" || $key=="enableAutosize" || $key=="show_playpause" ||
            $key=="show_progress" || $key=="show_current" || $key=="show_duration" ||
            $key=="show_tracks" || $key=="show_volume" || $key=="allow_fullscreen" ||
            $key=="alwaysShowControls" || $key=="iPadUseNativeControls" ||
            $key=="iPhoneUseNativeControls" || $key=="AndroidUseNativeControls" ||
            $key=="enableKeyboard" || $key=="pauseOtherPlayers" || $key=="responsive") {
        echo '<td><span>'.str_replace('_',' ',$key).'</span></td>';
        echo '<td><input name="'.$key.'" id="'.$key.'" type="checkbox"';
        if ($value=='on') { echo ' checked="checked" '; }
        echo '/></td>';
      } elseif ($key=="skin") {
        echo '<td><span>'.str_replace('_',' ',$key).'</span></td>';
        echo '<td><select name="'.$key.'" id="'.$key.'">';
        echo '<option value="default"'.(($value=='' || $value=='default')?' selected="selected"':'').'>default</option>';
        echo '<option value="mejs-ted"'.(($value=='mejs-ted')?' selected="selected"':'').'>TED</option>';
        echo '<option value="mejs-wmp"'.(($value=='mejs-wmp')?' selected="selected"':'').'>WMP</option>';
        echo '</select></td>';
      } else {
        echo '<td><span>'.str_replace('_',' ',$key).'</span></td>';
        echo '<td><input name="'.$key.'" size="2" id="'.$key.'" type="text" value="'.$value.'" /></td>';
      }
      if ($tdc == 1) { echo '</tr>'; $tdc = -1; $even = $even ? false : true; }
      $tdc++; $i++;
    } // for each end
    echo '</table>';
    echo '<p style="float:right;">';
    echo 'See also <a href="http://mediaelementjs.com" target="_blank">mediaelementjs.com</a>';
    echo '</p>';
    echo '<p>';
    echo '<input type="submit" name="save" value="' . $langmessage['save'] . '" class="gpsubmit" /> ';
    echo '<input type="button" name="cmd" value="' . $langmessage['cancel'] . '" class="admin_box_close gpcancel" />';
    echo '</p>';
    echo '</form>';
    echo '<br/>';

    echo '<h3 style="border-top:1px solid #ccc; padding-top:0.6em;">Apache Configuration</h3>';
    echo '<p>If you have issues with playback make sure Apache serves your media files with the correct MIME Types. See ';
    echo '<a href="' . $addonRelativeCode . '/sample_htaccess_for_your_data_folder.txt" target="_blank">sample_htaccess_for_your_data_folder.txt</a>.';

    echo '<h3 style="border-top:1px solid #ccc; padding-top:0.6em;">gpEasy Configuration</h3>';
    echo '<p>gpEasy needs changes in line 11 in the file <em><strong>gpconfig.php</strong></em> in your gpEasy intallation '; 
    echo 'folder in order to allow the upload of all HTML5 media file types: <br/>';
    echo '<em>change</em>&nbsp;&nbsp;<span style="font-family:monospace;">$upload_extensions_allow = array();</span><br/>';
    echo '<em>to</em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family:monospace;">$upload_extensions_allow = array(\'webm\',\'m4v\',\'ogg\',\'oga\',\'ogv\');</span></p>';

    echo '<h3 style="border-top:1px solid #ccc; padding-top:0.6em;">A few words about HTML5 media playback</h3>';
    echo '<p>HTML5 &lt;video&gt; and &lt;audio&gt; is on in’s way but it is not yet mature. ';
    echo 'Mozilla has just decided to let Firefox render the proprietary mp4/h.264 format if the client OS provies a decoder. ';
    echo 'Therefore h.264 will likely become the long-aticipated (but patent-ridden) HTML5 video standard, that other major browsers already support. For the time being, to support ';
    echo 'all current browsers we still need to provide two different video formats (<strong>MP4</strong>/h.264 and <strong>WebM</strong>/VP8) and also two different ';
    echo 'formats for HTML5 audio (<strong>MP3</strong> and <strong>OGG</strong> Vorbis). To encode/re-encode all these formats and get some nice-playing web media ';
    echo 'you’ll need several tools like Audacity for OGG Vorbis and Miro Video Converter, Any Video Converter and/or Handbrake for MP4 and WebM. Recommended Link to start: ';
    echo '<a target="_blank" href="http://diveintohtml5.info/video.html">Dive Into HTML5 - Video</a>. ';
    echo 'Especially encoding/re-encoding of HTML5 video needs some practice, patience and frustration tolerance. ';
    echo 'Make sure to always remember this one: <strong>For proper streaming MP4/h.264 needs to have the meta-data placed at the beginning of the stream.</strong><br/>';
    echo 'Get informed and stay tuned – it can only get better with time ;-)';
    echo '</p>';
  } // fnc Show_Config end


  function Load_Config() {
    if (file_exists($this->config_file)) {
      include($this->config_file);
      $this->config = $config;
    } else {
      //default config
      $this->defaultconfig = array(
        'skin'                      =>   'default',         // default='', 'mejs-ted', 'mejs-wmp'
        'startVolume'               =>   '0.8',             // initial volume when the player starts
        'defaultVideoWidth'         =>   '512',             // if the <video width> is not specified, this is the default
        'defaultVideoHeight'        =>   '288',             // if the <video height> is not specified, this is the default
        'audioWidth'                =>   '100%',            // width of audio player
        'audioHeight'               =>   '30',              // height of audio player
        'loop'                      =>   'off',             // useful for <audio> player loops
        'enableAutosize'            =>   'on',              // enables Flash and Silverlight to resize to content size
        'show_playpause'            =>   'on',              
        'show_progress'             =>   'on',              
        'show_current'              =>   'off',             
        'show_duration'             =>   'on',              
        'show_tracks'               =>   'on',              
        'show_volume'               =>   'on',              
        'allow_fullscreen'          =>   'on',              
        'alwaysShowControls'        =>   'off',             // Hide controls when playing and mouse is not over the video
        'iPadUseNativeControls'     =>   'off',             // force iPad's native controls
        'iPhoneUseNativeControls'   =>   'off',             // force iPhone's native controls
        'AndroidUseNativeControls'  =>   'off',             // force Android's native controls
        'pauseOtherPlayers'         =>   'on',              // when this player starts, it will pause other players
        'enableKeyboard'            =>   'on',              // turns keyboard support on and off
        'responsive'                =>   'off'              // "on" scales player to max available size
      );
      $this->config = $this->defaultconfig;
    }
  } // fnc Load_Config end


  function Save_Config() {
    global $langmessage;
    $this->config['startVolume'] =              trim($_POST['startVolume']);
    $this->config['skin'] =                     trim($_POST['skin']);
    $this->config['defaultVideoWidth'] =        trim($_POST['defaultVideoWidth']);
    $this->config['defaultVideoHeight'] =       trim($_POST['defaultVideoHeight']);
    $this->config['audioWidth'] =               trim($_POST['audioWidth']);
    $this->config['audioHeight'] =              trim($_POST['audioHeight']);
    $this->config['loop'] =                     isset($_POST['loop']) ? trim($_POST['loop']) : 'off';
    $this->config['enableAutosize'] =           isset($_POST['enableAutosize']) ? trim($_POST['enableAutosize']) : 'off'; 
    $this->config['show_playpause'] =           isset($_POST['show_playpause']) ? trim($_POST['show_playpause']) : 'off';
    $this->config['show_progress'] =            isset($_POST['show_progress']) ? trim($_POST['show_progress']) : 'off';
    $this->config['show_current'] =             isset($_POST['show_current']) ? trim($_POST['show_current']) : 'off';
    $this->config['show_duration'] =            isset($_POST['show_duration']) ? trim($_POST['show_duration']) : 'off'; 
    $this->config['show_tracks'] =              isset($_POST['show_tracks']) ? trim($_POST['show_tracks']) : 'off'; 
    $this->config['show_volume'] =              isset($_POST['show_volume']) ? trim($_POST['show_volume']) : 'off'; 
    $this->config['allow_fullscreen'] =         isset($_POST['allow_fullscreen']) ? trim($_POST['allow_fullscreen']) : 'off'; 
    $this->config['alwaysShowControls'] =       isset($_POST['alwaysShowControls']) ? trim($_POST['alwaysShowControls']) : 'off'; 
    $this->config['iPadUseNativeControls'] =    isset($_POST['iPadUseNativeControls']) ? trim($_POST['iPadUseNativeControls']) : 'off';  
    $this->config['iPhoneUseNativeControls'] =  isset($_POST['iPhoneUseNativeControls']) ? trim($_POST['iPhoneUseNativeControls']) : 'off'; 
    $this->config['AndroidUseNativeControls'] = isset($_POST['AndroidUseNativeControls']) ? trim($_POST['AndroidUseNativeControls']) : 'off'; 
    $this->config['enableKeyboard'] =           isset($_POST['enableKeyboard']) ? trim($_POST['enableKeyboard']) : 'off'; 
    $this->config['pauseOtherPlayers'] =        isset($_POST['pauseOtherPlayers']) ? trim($_POST['pauseOtherPlayers']) : 'off'; 
    $this->config['responsive'] =               isset($_POST['responsive']) ? trim($_POST['responsive']) : 'off'; 

    if (gpFiles::SaveArray( $this->config_file , 'config' , $this->config )) { message($langmessage['SAVED']); }

  } // fnc Save_Config end

} // class end

?>