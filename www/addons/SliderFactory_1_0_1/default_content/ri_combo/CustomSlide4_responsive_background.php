<?php /* UTF-8 ÄÖÜäöü 
####################################################################################
Default Content for Typesetter CMS Addon Slider Factory - Custom Section Combo part
Author: J. Krausz
Date: 2016-04-28
Version: 1.0.1
####################################################################################
*/
defined('is_running') or die('Not an entry point...');

global $addonRelativeCode; 

$newSection = array (
  'type' => 'responsive_background',

  'content' => '<a class="responsive_image_wrapper" data-enable-fullscreen="false" data-use-colorbox="false" style="height:100%; padding-top:0px;"><div class="focuspoint" data-focus-x="0" data-focus-y="0" data-image-w="1280" data-image-h="560"><img style="top: -5.38642%; left: 0px; max-width: 100%;" alt="Image" src="' 
  . $addonRelativeCode . '/default_content/images/SliderFactory-SampleBg-04.png"></div><div class="responsive_image_caption_wrapper caption-show-never" style="background-color:rgba(0,0,0,0.75);"><div class="responsive_image_caption"></div></div></a>',
  'image_height_type'   => 'fixed',
  'image_height_value'  => '100',
  'image_height_units'  => '%',

  'attributes' => array (),
  'gp_label' => 'Responsive Background',
  'gp_color' => '#8D3EE8',
);