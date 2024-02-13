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

  'content' => '<div><h2><span style="color:#4B0082;">And finally &hellip; Slide 4</span></h2>
<p>In addition to <em>innerBox-lighten </em>this Text Box utilizes the classes <em>innerFx-toast</em> and <em>innerFx-fade</em>, all in combination.</p>
<p>Hmm, anything missing? Ah, yes, the slider parameters ;-) You may already have noticed the overlay down &dArr; at the bottom of the slider, saying &ldquo;edit slider&rdquo;. Editing this area will get you to all the slider options. Just try it. No worries, the overlay will not be shown to normal visitors.</p>
<p>Adding/removing slides: This is done in the Sections List in &ldquo;Page&rdquo; view. To add a slide the most easy way is to copy an existing one using the &ldquo;Copy&rdquo; icon next to <em>Slide 1 (wrapper)</em> but you can also build your very own slides. Slider Factory can turn every section into a slide.<br />Just try it yourself!</p></div>',

  'attributes' => array ( 'class' => 'gpCol-6 innerBox-lighten innerFx-fade innerFx-toast' ),
  'gp_label' => 'Text Box',
  'gp_color' => '#8D3EE8',
);