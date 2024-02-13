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

  'content' => '<div><h2>&hellip; Slide 2</h2>
<p>This Text Box not only uses the class <em>innerFx-fade</em> but also <em>innerBox-lighten</em>, which accounts for the white transparent background.</p>
<p>If you wonder how to use these cool <a class="prev-slide" href="#prev-slide">&laquo;previous slide</a> and <a href="#next-slide">next slide&raquo;</a> inline links &ndash; that&#39;s simple: They are generic links, made with CK&nbsp;Editor&#39;s <span style="white-space:nowrap;"><span style="background-image:url(\'' 
. $dirPrefix . '/include/thirdparty/ckeditor_34/skins/kama/icons.png\');background-position:0 -1152px; background-size:auto; width:16px; height:16px; float:none; display:inline-block; position:relative; top:1px;">&nbsp;</span>&nbsp;Link&nbsp;Tool</span>. Just use <em>#prev-slide</em> or <em>#next-slide</em> in the URL field and you&rsquo;re set.</p>
<p>Time for the <a href="#next-slide">next slide&raquo;</a></p>
</div>',

  'attributes' => array ( 'class' => 'gpCol-6 innerBox-lighten innerFx-fade' ),
  'gp_label' => 'Text Box',
  'gp_color' => '#C5E817',
);