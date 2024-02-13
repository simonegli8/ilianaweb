<?php
/*
######################################################################
PHP class for Typesetter CMS plugin Back2Top Button - Admin
Author: J. Krausz
Date: 2018-07-14
Version 1.0.2
######################################################################
*/

defined('is_running') or die('Not an entry point...');

class Back2Top{

  static $b2t_config;

  static function Gadget() {
    global $page, $addonRelativeCode, $addonRelativeData, $addonPathData;

    self::LoadConfig();
 
    $b2t_css_file = file_exists($addonPathData.'/b2t_custom.css') 
      ? $addonRelativeData.'/b2t_custom.css' 
      : $addonRelativeCode.'/defaults/b2t_custom.css';

    $page->css_user[] =   $addonRelativeCode.'/Back2Top.css';
    $page->css_user[] =   $b2t_css_file;
    $page->head_js[] =    $addonRelativeCode.'/Back2Top.js';
    // $page->head_script .=  "\n" . 'var bt2_config = ' . json_encode(self::$b2t_config) . ';' . "\n";
    $page->head_script .=  "\n\nvar bt2_config = {" 
      . "\n  scroll_trigger : "  . self::$b2t_config['scroll_trigger']  . ",\n"
      . "\n  scroll_speed : "    . self::$b2t_config['scroll_speed']    . ",\n"
      . "\n  scroll_easing : '"  . self::$b2t_config['scroll_easing']   . "'\n"
      . "};\n\n";

    $text = self::$b2t_config['text'];
    $icon = self::$b2t_config['icon'];
    echo '<div class="gp_Back2Top"' . (self::$b2t_config['content'] < 2 ? ' title="' . $text . '"' : '') . '>';
    if( self::$b2t_config['content'] == 1 || self::$b2t_config['content'] == 3 ){
      echo '<span class="b2t-arrow ' . $icon . '"></span>';
    }
    if( self::$b2t_config['content'] > 1 ){
      echo '<span class="b2t-text">' . $text . '</span>';
    }
    echo '</div>';
  }


  static function LoadConfig() {
    global $addonPathCode, $addonPathData;
    $b2t_config_file = $addonPathData . '/b2t_config.php';
    if( file_exists($b2t_config_file) ){
      include($b2t_config_file);
    }else{
      include($addonPathCode . "/defaults/b2t_config.php");
    }
    self::$b2t_config = $b2t_config;
  }

}