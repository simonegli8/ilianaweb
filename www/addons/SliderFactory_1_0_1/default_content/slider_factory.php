<?php /* UTF-8 ÄÖÜäöü 
####################################################################################
Default Content for Typesetter CMS Addon Slider Factory - default controller section
Author: J. Krausz
Date: 2016-04-28
Version: 1.0.1
####################################################################################
*/
defined('is_running') or die('Not an entry point...');

global $addonPathData, $addonPathCode; 

// Load Defaults
if( file_exists($addonPathData . '/SliderOptions.php') ){
  include($addonPathData . '/SliderOptions.php');
}else{
  include($addonPathCode . "/defaults/SliderOptions.php");
}

$newSection = array(
  'content' =>  '<div class="sliderfactory-data"'
                . ' data-slide-selector="' . $sliderDefaults['slideSelector'] . '"'
                . ' data-transition-effect="' . $sliderDefaults['transitionEffect'] . '"'
                . ' data-random-start="' . $sliderDefaults['randomStart'] . '"'
                . ' data-shuffle="' . $sliderDefaults['shuffle'] . '"'
                . ' data-show-prev-next="' . $sliderDefaults['showPrevNext'] . '"'
                . ' data-prev-text="' . $sliderDefaults['prevText'] . '"'
                . ' data-next-text="' . $sliderDefaults['nextText'] . '"'
                . ' data-show-indicators="' . $sliderDefaults['showIndicators'] . '"'
                . ' data-pause-on-hover="' . $sliderDefaults['pauseOnHover'] . '"'
                . ' data-delay="' . $sliderDefaults['delay'] . '"'
                . ' data-speed="' . $sliderDefaults['speed'] . '"'
                . ' data-timeout="' . $sliderDefaults['timeout'] . '"'
                . ' data-easing="' . $sliderDefaults['easing'] . '"'
                . ' data-theme="' . $sliderDefaults['theme'] . '"'
                . ' data-height="' . $sliderDefaults['height'] . '"'
                . ' data-height-unit="' . $sliderDefaults['heightUnit'] . '"'
                . '></div>',

  'attributes' => array(
    'class' => 'additional-custom-class',
    'style' => 'visibility:visible;',
  ),
  'gp_label'  => 'Slider Factory (controller)',
  'gp_color'  => '#000000',
  'type'      => 'slider_factory',
);