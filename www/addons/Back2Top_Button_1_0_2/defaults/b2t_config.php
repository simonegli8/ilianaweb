<?php
defined('is_running') or die('Not an entry point...');

$b2t_config = array (
  'content' =>              '1',                      // 1 = Icon Only, 2 = Text only, 3 = Text and Icon
  'text' =>                 'Back to Top',            // Button Text
  'icon' =>                 'b2t-arrow-0',            // CSS class
  'bgcolor' =>              'rgba(44,62,80,0.8)',     // valid CSS color string (default = Bootswatch Flatly navbar bg color)
  'bgcolor_hover' =>        'rgba(44,62,80,1)',       // valid CSS color string (default = Bootswatch Flatly navbar bg color)
  'borderRadius' =>         '4',                      // any px value
  'fontSize' =>             '100',                    // %
  'color' =>                'rgba(255,255,255,0.8)',  // valid CSS color string (default = white)
  'color_hover' =>          'rgba(255,255,255,1)',    // valid CSS color string (default = white)
  'position' =>             'right',                  // left, right, center
  'distance_side' =>        '30',                     // any number
  'distance_side_unit' =>   'px',                     // valid css unit (px, em, %...)
  'distance_bottom' =>      '30',                     // any number
  'distance_bottom_unit' => 'px',                     // valid css unit (px, em, %...)
  'transition_type' =>      '1',                      // 1 = slide, 2 = fade, 3 = both
  'transition_origin' =>    'right',                  // left, right, bottom
  'transition_speed' =>     '500',                    // miliseconds
  'scroll_trigger' =>       '600',                    // pixels page must be scrolled to show button
  'scroll_speed' =>         '600',                    // miliseconds
  'scroll_easing' =>        'swing',                  // swing, linear - valid jQuery.animate easing expression
);