<?php /* UTF-8! ÄÖÜäöüß
######################################################################
PHP script for Typesetter CMS plugin Back2Top Button - Admin
Author: J. Krausz
Date: 2018-07-14
Version 1.0.2
######################################################################
*/

defined('is_running') or die('Not an entry point...');

class Admin_Back2Top {

  var $admin_link;
  var $b2t_config;
  var $b2t_config_file;
  var $b2t_css_file;

  public function __construct() {
    global $addonPathData, $addonRelativeCode, $page;
    $this->admin_link = common::GetUrl('Admin_Back2Top');
    \gp\tool::LoadComponents('bootstrap-css');
    $page->css_admin[] = $addonRelativeCode . '/Admin_Back2Top.css';
    $page->css_admin[] = $addonRelativeCode . '/bootstrap_colorpicker/css/bootstrap-colorpicker.min.css';
    $page->head_js[] =   $addonRelativeCode . '/bootstrap_colorpicker/js/bootstrap-colorpicker.min.js';
    $page->head_js[] =   $addonRelativeCode . '/Admin_Back2Top.js';
    $this->b2t_config_file = $addonPathData . '/b2t_config.php';
    $this->b2t_css_file = $addonPathData . '/b2t_custom.css';

    if ( isset($_POST['save']) ){ 
      msg($this->SaveConfig()); 
    }

    // LOAD DATA
    $this->LoadConfig();

    // OUTPUT
    $this->ShowConfig();
  }


  private function ShowConfig() {
    global $langmessage; 
    echo '<h2 class="hqmargin">Back2Top Button &raquo; Settings</h2>';

    // FORM
    echo '<form id="b2t_config_form" action="' . $this->admin_link . '" method="post">';
    echo '<table class="bordered" style="width:100%;">';
    echo '<tr><th>Option</th><th>Value</th></tr>';

    $b2tContent = $this->b2t_config['content'];
    echo '<tr>';
    echo '<td>Button Content <span class="smalltext">may be an arrow icon, your custom text or both</span></td>';
    echo '<td>';
    echo '<select class="gpselect" name="b2t_config[content]">';
    echo '<option value="1"' . $this->checkActive('select', $b2tContent, '1') . '>Icon only</option>';
    echo '<option value="2"' . $this->checkActive('select', $b2tContent, '2') . '>Text only</option>';
    echo '<option value="3"' . $this->checkActive('select', $b2tContent, '3') . '>Icon and Text</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    $b2tIcon = $this->b2t_config['icon'];
    echo '<tr>';
    echo '<td>Button Icon <span class="smalltext">the icons are rendered using a custom webfont</span></td>';
    echo '<td>';
    /*
    echo '<select class="gpselect" name="b2t_config[icon]" style="font-family:back2top!important; font-size:1.6em; text-align:center;">';
    echo '<option value="b2t-arrow-0"'  . $this->checkActive('select', $b2tIcon, 'b2t-arrow-0') .  '>&#xe803;</option>';
    echo '<option value="b2t-arrow-1"'  . $this->checkActive('select', $b2tIcon, 'b2t-arrow-1') .  '>&#xe801;</option>';
    echo '<option value="b2t-arrow-2"'  . $this->checkActive('select', $b2tIcon, 'b2t-arrow-2') .  '>&#xe808;</option>';
    echo '<option value="b2t-arrow-3"'  . $this->checkActive('select', $b2tIcon, 'b2t-arrow-3') .  '>&#xe809;</option>';
    echo '<option value="b2t-arrow-4"'  . $this->checkActive('select', $b2tIcon, 'b2t-arrow-4') .  '>&#xe800;</option>';
    echo '<option value="b2t-arrow-5"'  . $this->checkActive('select', $b2tIcon, 'b2t-arrow-5') .  '>&#xe806;</option>';
    echo '<option value="b2t-arrow-6"'  . $this->checkActive('select', $b2tIcon, 'b2t-arrow-6') .  '>&#xe80a;</option>';
    echo '<option value="b2t-arrow-7"'  . $this->checkActive('select', $b2tIcon, 'b2t-arrow-7') .  '>&#xe80c;</option>';
    echo '<option value="b2t-arrow-8"'  . $this->checkActive('select', $b2tIcon, 'b2t-arrow-8') .  '>&#xe80f;</option>';
    echo '<option value="b2t-arrow-9"'  . $this->checkActive('select', $b2tIcon, 'b2t-arrow-9') .  '>&#xe80d;</option>';
    echo '<option value="b2t-arrow-10"' . $this->checkActive('select', $b2tIcon, 'b2t-arrow-10') . '>&#xe802;</option>';
    echo '<option value="b2t-arrow-11"' . $this->checkActive('select', $b2tIcon, 'b2t-arrow-11') . '>&#xe80e;</option>';
    echo '<option value="b2t-arrow-12"' . $this->checkActive('select', $b2tIcon, 'b2t-arrow-12') . '>&#xe80b;</option>';
    echo '<option value="b2t-arrow-13"' . $this->checkActive('select', $b2tIcon, 'b2t-arrow-13') . '>&#xe810;</option>';
    echo '<option value="b2t-arrow-14"' . $this->checkActive('select', $b2tIcon, 'b2t-arrow-14') . '>&#xe807;</option>';
    echo '<option value="b2t-arrow-15"' . $this->checkActive('select', $b2tIcon, 'b2t-arrow-15') . '>&#xe804;</option>';
    echo '<option value="b2t-arrow-16"' . $this->checkActive('select', $b2tIcon, 'b2t-arrow-16') . '>&#xe805;</option>';
    echo '</select>';
    */
    echo '<div class="b2t_arrow_select">';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_0" value="b2t-arrow-0"'   . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-0')  . '/><label for="b2t_icon_0">&#xe803;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_1" value="b2t-arrow-1"'   . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-1')  . '/><label for="b2t_icon_1">&#xe801;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_2" value="b2t-arrow-2"'   . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-2')  . '/><label for="b2t_icon_2">&#xe808;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_3" value="b2t-arrow-3"'   . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-3')  . '/><label for="b2t_icon_3">&#xe809;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_4" value="b2t-arrow-4"'   . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-4')  . '/><label for="b2t_icon_4">&#xe800;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_5" value="b2t-arrow-5"'   . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-5')  . '/><label for="b2t_icon_5">&#xe806;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_6" value="b2t-arrow-6"'   . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-6')  . '/><label for="b2t_icon_6">&#xe80a;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_7" value="b2t-arrow-7"'   . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-7')  . '/><label for="b2t_icon_7">&#xe80c;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_8" value="b2t-arrow-8"'   . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-8')  . '/><label for="b2t_icon_8">&#xe80f;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_9" value="b2t-arrow-9"'   . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-9')  . '/><label for="b2t_icon_9">&#xe80d;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_10" value="b2t-arrow-10"' . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-10') . '/><label for="b2t_icon_10">&#xe802;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_11" value="b2t-arrow-11"' . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-11') . '/><label for="b2t_icon_11">&#xe80e;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_12" value="b2t-arrow-12"' . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-12') . '/><label for="b2t_icon_12">&#xe80b;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_13" value="b2t-arrow-13"' . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-13') . '/><label for="b2t_icon_13">&#xe810;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_14" value="b2t-arrow-14"' . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-14') . '/><label for="b2t_icon_14">&#xe807;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_15" value="b2t-arrow-15"' . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-15') . '/><label for="b2t_icon_15">&#xe804;</label> ';
    echo '<input type="radio" name="b2t_config[icon]" id="b2t_icon_16" value="b2t-arrow-16"' . $this->checkActive('radio', $b2tIcon, 'b2t-arrow-16') . '/><label for="b2t_icon_16">&#xe805;</label> ';
    echo '</div>';

    echo '</td>';
    echo '</tr>';

    $b2tText = $this->b2t_config['text'];
    echo '<tr>';
    echo '<td>Button Text <span class="smalltext">used for the title tooltip when only the icon is shown</span></td>';
    echo '<td>';
    echo '<input class="gpinput" type="text" name="b2t_config[text]" value="' . $b2tText . '" />';
    echo '</td>';
    echo '</tr>';

    $b2tColor = $this->b2t_config['color'];
    echo '<tr>';
    echo '<td>Color <span class="smalltext">will affect both text and icon - supports transparency (rgba)</span></td>';
    echo '<td>';
    echo '<input class="gpinput colorpicker" type="text" name="b2t_config[color]" value="' . $b2tColor . '" />';
    echo '</td>';
    echo '</tr>';

    $b2tColorHover = $this->b2t_config['color_hover'];
    echo '<tr>';
    echo '<td>Hover Color <span class="smalltext">the color when the mouse pointer hovers the button</span></td>';
    echo '<td>';
    echo '<input class="gpinput colorpicker" type="text" name="b2t_config[color_hover]" value="' . $b2tColorHover . '" />';
    echo '</td>';
    echo '</tr>';

    $b2tBgColor = $this->b2t_config['bgcolor'];
    echo '<tr>';
    echo '<td>Background Color <span class="smalltext">want it completely transparent? &rarr; rgba(0,0,0,0)</span></td>';
    echo '<td>';
    echo '<input class="gpinput colorpicker" type="text" name="b2t_config[bgcolor]" value="' . $b2tBgColor . '" />';
    echo '</td>';
    echo '</tr>';

    $b2tBgColorHover = $this->b2t_config['bgcolor_hover'];
    echo '<tr>';
    echo '<td>Hover Background Color<span class="smalltext">the background color when the mouse pointer hovers the button</span></td>';
    echo '<td>';
    echo '<input class="gpinput colorpicker" type="text" name="b2t_config[bgcolor_hover]" value="' . $b2tBgColorHover . '" />';
    echo '</td>';
    echo '</tr>';


    $b2tBorderRadius = $this->b2t_config['borderRadius'];
    echo '<tr>';
    echo '<td>Border Radius <span class="smalltext">with icon only and a high value here you will get a disc</span></td>';
    echo '<td>';
    echo '<input class="gpinput" type="number" min="0" step="1" name="b2t_config[borderRadius]" value="' . $b2tBorderRadius . '" /> px';
    echo '</td>';
    echo '</tr>';

    $b2tFontSize = $this->b2t_config['fontSize'];
    echo '<tr>';
    echo '<td>Font Size <span class="smalltext">% of base font size, affects the whole button</span></td>';
    echo '<td>';
    echo '<input class="gpinput" type="number" min="0" step="10" name="b2t_config[fontSize]" value="' . $b2tFontSize . '" /> %';
    echo '</td>';
    echo '</tr>';

    $b2tPosition = $this->b2t_config['position'];
    echo '<tr>';
    echo '<td>Position <span class="smalltext">where the button will appear</span></td>';
    echo '<td>';
    echo '<select class="gpselect" name="b2t_config[position]">';
    echo '<option value="right"'  . $this->checkActive('select', $b2tPosition, 'right') .  '>right</option>';
    echo '<option value="center"' . $this->checkActive('select', $b2tPosition, 'center') . '>center</option>';
    echo '<option value="left"'   . $this->checkActive('select', $b2tPosition, 'left') .   '>left</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    $b2tDistanceSide = $this->b2t_config['distance_side'];
    $b2tDistanceSideUnit = $this->b2t_config['distance_side_unit'];
    echo '<tr>';
    echo '<td>Distance from Side <span class="smalltext">lateral viewport edge offset - used for left or right position</span></td>';
    echo '<td>';
    echo '<input class="gpinput" type="number" name="b2t_config[distance_side]" value="' . $b2tDistanceSide . '" /> ';
    echo '<select class="gpselect" name="b2t_config[distance_side_unit]">';
    echo '<option value="px"' . $this->checkActive('select', $b2tDistanceSideUnit, 'px') . '>px</option>';
    echo '<option value="em"' . $this->checkActive('select', $b2tDistanceSideUnit, 'em') . '>em</option>';
    echo '<option value="%"'  . $this->checkActive('select', $b2tDistanceSideUnit, '%') .  '>%</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    $b2tDistanceBottom = $this->b2t_config['distance_bottom'];
    $b2tDistanceBottomUnit = $this->b2t_config['distance_bottom_unit'];
    echo '<tr>';
    echo '<td>Distance from Bottom <span class="smalltext">offset from the viewport&rsquo;s lower edge</span></td>';
    echo '<td>';
    echo '<input class="gpinput" type="number" name="b2t_config[distance_bottom]" value="' . $b2tDistanceBottom . '" /> ';
    echo '<select class="gpselect" name="b2t_config[distance_bottom_unit]">';
    echo '<option value="px"' . $this->checkActive('select', $b2tDistanceBottomUnit, 'px') . '>px</option>';
    echo '<option value="em"' . $this->checkActive('select', $b2tDistanceBottomUnit, 'em') . '>em</option>';
    echo '<option value="%"'  . $this->checkActive('select', $b2tDistanceBottomUnit, '%') .  '>%</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    $b2tTransitionType = $this->b2t_config['transition_type'];
    echo '<tr>';
    echo '<td>Button Transition Type <span class="smalltext">slide and fade can be used together</span></td>';
    echo '<td>';
    echo '<select class="gpselect" name="b2t_config[transition_type]">';
    echo '<option value="1"' . $this->checkActive('select', $b2tTransitionType, '1') . '>slide</option>';
    echo '<option value="2"' . $this->checkActive('select', $b2tTransitionType, '2') . '>fade</option>';
    echo '<option value="3"' . $this->checkActive('select', $b2tTransitionType, '3') . '>both</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    $b2tTransitionOrigin = $this->b2t_config['transition_origin'];
    echo '<tr>';
    echo '<td>Button slides-in from&hellip; <span class="smalltext">origin of the invisible button. Used for slide transition</span></td>';
    echo '<td>';
    echo '<select class="gpselect" name="b2t_config[transition_origin]">';
    echo '<option value="right"' .  $this->checkActive('select', $b2tTransitionOrigin, 'right') .  '>right</option>';
    echo '<option value="bottom"' . $this->checkActive('select', $b2tTransitionOrigin, 'bottom') . '>bottom</option>';
    echo '<option value="left"' .   $this->checkActive('select', $b2tTransitionOrigin, 'left') .   '>left</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    $b2tTransitionSpeed = $this->b2t_config['transition_speed'];
    echo '<tr>';
    echo '<td>Button Transition Speed <span class="smalltext">duration of the show/hide and hover effects in milliseconds</span></td>';
    echo '<td>';
    echo '<input class="gpinput" type="number" min="0" step="100" name="b2t_config[transition_speed]" value="' . $b2tTransitionSpeed . '" /> ms ';
    echo '</td>';
    echo '</tr>';

    $b2tScrollTrigger = $this->b2t_config['scroll_trigger'];
    echo '<tr>';
    echo '<td>Page Scroll Trigger <span class="smalltext">number of pixels the page must be scrolled down to show the button</span></td>';
    echo '<td>';
    echo '<input class="gpinput" type="number" min="0" step="50" name="b2t_config[scroll_trigger]" value="' . $b2tScrollTrigger . '" /> px ';
    echo '</td>';
    echo '</tr>';

    $b2tScrollSpeed = $this->b2t_config['scroll_speed'];
    echo '<tr>';
    echo '<td>Page Scroll Speed <span class="smalltext">duration of the scroll-up effect when the button is clicked</span></td>';
    echo '<td>';
    echo '<input class="gpinput" type="number" min="0" step="100" name="b2t_config[scroll_speed]" value="' . $b2tScrollSpeed . '" /> ms ';
    echo '</td>';
    echo '</tr>';

    $b2tScrollEasing = $this->b2t_config['scroll_easing'];
    echo '<tr>';
    echo '<td>Page Scroll Easing <span class="smalltext">swing = accelerate&rarr;decelerate, linear = uniform scrolling speed</span></td>';
    echo '<td>';
    echo '<select class="gpselect" name="b2t_config[scroll_easing]">';
    echo '<option value="swing"'  . $this->checkActive('select', $b2tScrollEasing, 'swing')  . '>swing</option>';
    echo '<option value="linear"' . $this->checkActive('select', $b2tScrollEasing, 'linear') . '>linear</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    echo '</table>';


    // SAVE / CANCEL BUTTONS
    echo '<br/>';
    echo '<input type="submit" name="save" value="' . $langmessage['save'] . '" class="gpsubmit" /> ';
    echo '<input type="button" onClick="location.href=\'' . $this->admin_link . '\'" ';
    echo 'name="cmd" value="' . $langmessage['cancel'] . '" class="gpcancel" />';

    echo '</form>';

    echo '<div style="margin-top:2em; border:1px solid #ccc; background:#fafafa; border-radius:3px; padding:12px;">';
    echo '<h4>Tipps:</h4>';
    echo '<ul>';
    echo '<li>Open your website in a separate tab or window and reresh it (F5, Ctrl+R) after saving here to quickly see the effect of your settings.</li>';
    echo '<li>Using Firefox or Chrome? Install the free <a target="_blank" href="http://www.colorzilla.com">ColorZilla addon</a> ';
    echo 'to directly pick colors from your or any other website. (I&rsquo;m not affiliated with ColorZilla)</li>';
    echo '</ul>';
    echo '</div>';

    // FORM end

  } /* ShowConfig fnc end */


  private function LoadConfig() {
    global $addonPathCode;

    if (file_exists($this->b2t_config_file)) {
      include($this->b2t_config_file);
    } else {
      include($addonPathCode . "/defaults/b2t_config.php");
    }
    $this->b2t_config = $b2t_config;

  } /* LoadConfig fnc end */


  private function SaveConfig() {
    global $langmessage;

    foreach ($_POST['b2t_config'] as $key => $value) {
      $this->b2t_config[$key] = trim($value); 
    }

    $success = gpFiles::SaveArray($this->b2t_config_file, 'b2t_config', $this->b2t_config);
    $success = $this->saveCSS() ? $success : false;

    if ( $success ){ 
      msg( $langmessage['SAVED'] );
    } else {
      msg( $langmessage['OOPS'] );
    }

  } /* SaveConfig fnc end */



  private function checkActive($type,$str,$match){
    $return = $type == 'select' ? ' selected="selected" ' : ' checked="checked" ';
    if ($str == $match) {
      return $return;
    }
    return '';
  }



  private function saveCSS(){
    global $addonPathData;

    $color = trim($this->b2t_config['color']);
    $color_hover = trim($this->b2t_config['color_hover']);
    $bgcolor = trim($this->b2t_config['bgcolor']);
    $bgcolor_hover = trim($this->b2t_config['bgcolor_hover']);
    $borderRadius = intval($this->b2t_config['borderRadius']);
    $fontSize = intval($this->b2t_config['fontSize']);
    $sideDist = intval($this->b2t_config['distance_side']) . trim($this->b2t_config['distance_side_unit']);
    $bottomDist = intval($this->b2t_config['distance_bottom']) . trim($this->b2t_config['distance_bottom_unit']);
    $transitionDuration = intval($this->b2t_config["transition_speed"]);

    switch ($this->b2t_config['position']) {
      case 'left':
        $toLeft = $sideDist;
        $toRight = 'auto';
        $toTranslateX = '0';
        $toTranslateY = '0';
        break;
      case 'center':
        $toLeft = '50%';
        $toRight = 'auto';
        $toTranslateX = '-50%';
        $toTranslateY = '0';
        break;
      case 'right':
      default:
        $toLeft = 'auto';
        $toRight = $sideDist;
        $toTranslateX = '0';
        $toTranslateY = '0';
        break;
    }

    switch ($this->b2t_config['transition_origin']) {
      case 'left':
        if ( $this->b2t_config['position'] == "right"){
          $fromLeft = 'auto';
          $fromRight = 'calc(100%)';
        }else{
          $fromLeft = '0';
          $fromRight = 'auto';
        }
        $fromBottom = $bottomDist;
        $toBottom = $bottomDist;
        $fromTranslateX = '-102%';
        $fromTranslateY = $toTranslateY;
        break;
      case 'bottom':
        $fromLeft = $toLeft;
        $fromRight = $toRight;
        $fromBottom = '0';
        $toBottom = $bottomDist;
        $fromTranslateX = $toTranslateX;
        $fromTranslateY = '102%';
        break;
      case 'right':
      default:
        if ( $this->b2t_config['position'] == "left" ||  $this->b2t_config['position'] == "center" ){
          $fromLeft = 'calc(100%)';
          $fromRight = 'auto';
        }else{
          $fromLeft = 'auto';
          $fromRight = '0';
        }
        $fromBottom = $bottomDist;
        $toBottom = $bottomDist;
        $fromTranslateX = '102%';
        $fromTranslateY = $toTranslateY;
        break;
    }

    switch ($this->b2t_config['transition_type']) {
      case '1': // slide
        $fromOpacity = '1';
        $toOpacity = '1';
      break;
      case '2': // fade
        $fromLeft = $toLeft;
        $fromRight = $toRight;
        $fromBottom = $toBottom;
        $fromTranslateX = $toTranslateX;
        $fromTranslateY = $toTranslateY;
        $fromOpacity = '0';
        $toOpacity = '1';
        break;
      case '3': // both
      default:
        $fromOpacity = '0';
        $toOpacity = '1';
        break;
    }

    $css =  ".gp_Back2Top {\n";

    if ( $this->rgba2rgb($bgcolor) ){
      $css .= "background:" . $this->rgba2rgb($bgcolor) . ";\n";
    }
    $css .= "background:" . $bgcolor . ";\n";
    if ( $this->rgba2rgb($color) ){
      $css .= "color:" . $this->rgba2rgb($color) . ";\n";
    }
    $css .= "color:" . $color . ";\n";

    $css .= "border-radius:" . $borderRadius . "px;\n";
    $css .= "font-size:" . $fontSize . "%;\n";

    $css .= "left:" . $fromLeft . ";\n";
    $css .= "right:" . $fromRight . ";\n";
    $css .= "bottom:" . $fromBottom . ";\n";

    $css .= "-webkit-opacity:" . $fromOpacity . ";\n";
    $css .= "-moz-opacity:" . $fromOpacity . ";\n";
    $css .= "opacity:" . $fromOpacity . ";\n";

    $css .= "-webkit-transform:translate(" . $fromTranslateX . "," . $fromTranslateY . ");\n";
    $css .= "-moz-transform:translate(" . $fromTranslateX . "," . $fromTranslateY . ");\n";
    $css .= "-ms-transform:translate(" . $fromTranslateX . "," . $fromTranslateY . ");\n";
    $css .= "transform:translate(" . $fromTranslateX . "," . $fromTranslateY . ");\n";

    $css .= "-webkit-transition:all " . $transitionDuration . "ms;\n";
    $css .= "-moz-transition:all " . $transitionDuration . "ms;\n";
    $css .= "-ms-transition:all " . $transitionDuration . "ms;\n";
    $css .= "-o-transition:all " . $transitionDuration . "ms;\n";
    $css .= "transition:all " . $transitionDuration . "ms;\n";

    $css .= "}\n\n";


    $css .= ".gp_Back2Top.b2t-show {\n";

    $css .= "left:" . $toLeft . ";\n";
    $css .= "right:" . $toRight . ";\n";
    $css .= "bottom:" . $toBottom . ";\n";

    $css .= "-webkit-transform:translate(" . $toTranslateX . "," . $toTranslateY . ");\n";
    $css .= "-moz-transform:translate(" . $toTranslateX . "," . $toTranslateY . ");\n";
    $css .= "-ms-transform:translate(" . $toTranslateX . "," . $toTranslateY . ");\n";
    $css .= "transform:translate(" . $toTranslateX . "," . $toTranslateY . ");\n";

    $css .= "-webkit-opacity:" . $toOpacity . ";\n";
    $css .= "-moz-opacity:" . $toOpacity . ";\n";
    $css .= "opacity:" . $toOpacity . ";\n";

    $css .= "}\n\n";


    $css .= ".gp_Back2Top.b2t-show:hover {\n";

    if ( $this->rgba2rgb($bgcolor_hover) ){
      $css .= "background:" . $this->rgba2rgb($bgcolor_hover) . ";\n";
    }
    $css .= "background:" . $bgcolor_hover . ";\n";
    if ( $this->rgba2rgb($color_hover) ){
      $css .= "color:" . $this->rgba2rgb($color_hover) . ";\n";
    }
    $css .= "color:" . $color_hover . ";\n";

    $css .= "}\n";

    if ( intval($this->b2t_config["content"]) == 3 ){
      $css .= "\n.gp_Back2Top .b2t-text { padding-left:0.5em; }\n";
    }

    return \gp\tool\Files::Save($this->b2t_css_file, $css);

  } /* SaveCSS fnc end */



  private function rgba2rgb($colStr) {
    if ( strtolower(substr($colStr, 0, 4)) != "rgba" ){
      return false;
    }
    $rgb = 'rgb' . trim( substr($colStr, 4, strrpos($colStr,',')-4 ) ) . ')';
    return $rgb;
  }

}
