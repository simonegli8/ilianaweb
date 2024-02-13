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
  'type'    => 'text',

  'content' => '<div><h2><br /><span style="color:#ffcccc;">Welcome!</span></h2>
<p>Thank you for using Slider Factory! You just added the most sophisticated preset &ldquo;Slider Combo ResponsiveBG + Text Box&rdquo;. In this Combo, every slide is a wrapper section which contains 2 more sections each: A &ldquo;Responsive Background&rdquo; section and another wrapper called <em>Container</em>. The Text Box you are currently reading is a child of the Container wrapper. It&#39;s in fact a generic &ldquo;Text&rdquo; section, so you can edit it just like any other text.</p>
<p>This Text Box has the class <em>innerFx-fade</em> applied using the Options icon in the section list. <em>innerFx-fade</em> makes the box fade in once the slide appears and fade out upon exit.</p>
Ready? Well, then let&rsquo;s <a href="#next-slide">advance to&hellip;</a></div>',

  'attributes' => array ( 'class' => 'gpCol-6 innerFx-fade' ),
  'gp_label' => 'Text Box',
  'gp_color' => '#ED4B1E',
);