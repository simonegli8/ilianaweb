<?php /* UTF-8 ÄÖÜäöü 
####################################################################################
Default Content for Typesetter CMS Addon Slider Factory - Custom Section Combo part
Author: J. Krausz
Date: 2016-04-28
Version: 1.0.1
####################################################################################
*/
defined('is_running') or die('Not an entry point...');

global $addonRelativeCode, $dirPrefix; 

$newSection = array (
  'type' => 'slider_factory',

  'content' => '<div class="sliderfactory-data" data-slide-selector=".GPAREA:not(.filetype-slider_factory)" data-transition-effect="slideH" data-random-start="false" data-shuffle="false" data-show-prev-next="true" data-prev-text="prev" data-next-text="next" data-show-indicators="true" data-pause-on-hover="true" data-delay="0" data-speed="600" data-timeout="0" data-easing="swing" data-theme="gpSliderTheme-default" data-height="" data-height-unit="px"></div>',

  'gp_label' => 'Slider Factory (controller)',
  'gp_color' => '#000000',
);