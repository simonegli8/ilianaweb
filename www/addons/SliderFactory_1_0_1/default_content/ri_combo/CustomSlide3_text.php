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
  'type'    => 'text',

  'content' => '<div><h2><span style="color:#FFFFFF;">Here comes Slide No. 3</span></h2>
<p><span style="color:#FFFFFF;">This Text Box only has the (effect) class<em> innerBox-darken</em> instead of <em>-lighten</em>. <em>innerBox-darken</em> will not automatically make the text color white, so you will have to use CK Editor&rsquo;s <span style="white-space:nowrap;"><span style="background-image:url(\'' 
. $dirPrefix . '/include/thirdparty/ckeditor_34/skins/kama/icons.png\');background-position:0 -408px; background-size:auto; width:16px; height:16px; float:none; display:inline-block; position:relative; top:1px;">&nbsp;</span> Text Color tool</span>.</span></p>
<p><span style="color:#FFFFFF;">What about the background image? That&rsquo;s a &ldquo;Responsive Background&rdquo; section which comes with the &ldquo;Responsive Image&rdquo; plugin. You can edit it by clicking somewhere into the background. Currently this will not always work. In case just switch to the editor&rsquo;s &ldquo;Page&rdquo; view and click the Edit icon (the pencil). I hope to fix this soon. </span><a href="#next-slide"><span style="color:#ccffcc;">Next slide&raquo;</span></a></p></div>',

  'attributes' => array ( 'class' => 'gpCol-6 innerBox-darken', ),
  'gp_label' => 'Text Box',
  'gp_color' => '#1192D6',
);