<?php /* 
######################################################################
Main PHP script for Typesetter CMS Addon Slider Factory
Author: J. Krausz
Date: 2016-04-28
Version: 1.0.1
######################################################################
*/

defined('is_running') or die('Not an entry point...');

class SliderFactory {

  static function GetHead() {
    global $page, $addonRelativeCode;

    $page->css_user[] = $addonRelativeCode.'/webfonts/slidercontrols.min.css';
    $page->css_user[] = $addonRelativeCode.'/SliderFactory.css';
    $page->css_user[] = $addonRelativeCode.'/SliderFactory_themes.css';
    $page->css_user[] = $addonRelativeCode.'/SliderFactory_custom.css';
    $page->css_user[] = $addonRelativeCode.'/SliderFactory_innerBox.css';
    $page->css_user[] = $addonRelativeCode.'/SliderFactory_innerFx.css';

    $page->head_js[] =  $addonRelativeCode.'/thirdparty/velocity.min.js';
    // $page->head_js[] =  $addonRelativeCode.'/dev/SliderFactory.dev.js';
    // $page->head_js[] =  $addonRelativeCode.'/dev/SliderFactory_init.js';
    $page->head_js[] =  $addonRelativeCode.'/SliderFactory.min.js';

    if (common::LoggedIn()) {
      common::LoadComponents("resizable");
    }
  }



  static function SectionTypes($section_types){
    $section_types['slider_factory'] = array();
    $section_types['slider_factory']['label'] = 'Slider Factory';
    return $section_types;
  }



  static function NewSections($links){
    global $addonRelativeCode, $addonFolderName, $config;
    $addonIconPath = $addonRelativeCode . '/icons';

    // replace section icon
    foreach ($links as $key => $section_type_arr) {
      if ( $section_type_arr[0] == 'slider_factory' ) {
        $links[$key] = array('slider_factory', $addonIconPath . '/section.png');
        break;
      }
    }

    // simple 4 x text section combo
    $links[] = array(
      array( 
        'text.gpSlide',   // sectionType.className
        'text.gpSlide',   // sectionType.className
        'text.gpSlide',   // sectionType.className
        'text.gpSlide',   // sectionType.className
        'slider_factory', // sectionType.className
      ), 
      $addonIconPath . '/section-combo-4-x-txt.png', // icon for the Section Combo, 88x50px
      'gpSlideWrapper', // SectionWrapper className
    );

    // multi-nested wrapper section combo
    $links[] = array( // new section entry

      0 => array( // level 0 items array
 
        0 =>  array( // .gpSlide (level 0 item no. 1)
          0 => array( // level 1 items array
            0 => array( // .container
              0 =>  array( // level 2 items array
                'text.gpCol-6 innerBox-lighten innerFx-fade innerFx-toast', // section-type.class names
              ),
              'container', // wrapper class name(s)
            ),
          ),
          'gpSlide',
        ),

        1 =>  array( // .gpSlide (level 0 item no. 2)
          0 => array( // level 1 items array
            0 => array( // .container
              0 =>  array( // level 2 items array
                'text.gpCol-6 innerBox-lighten innerFx-fade innerFx-toast', // section-type.class names
              ),
              'container', // wrapper class name(s)
            ),
          ),
          'gpSlide',
        ),

        2 =>  array( // .gpSlide (level 0 item no. 3)
          0 => array( // level 1 items array
            0 => array( // .container
              0 =>  array( // level 2 items array
                'text.gpCol-6 innerBox-lighten innerFx-fade innerFx-toast', // section-type.class names
              ),
              'container', // wrapper class name(s)
            ),
          ),
          'gpSlide',
        ),

        3 =>  array( // .gpSlide (level 0 item no. 4)
          0 => array( // level 1 items array
            0 => array( // .container
              0 =>  array( // level 2 items array
                'text.gpCol-6 innerBox-lighten innerFx-fade innerFx-toast', // section-type.class names
              ),
              'container', // wrapper class name(s)
            ),
          ),
          'gpSlide',
        ),

        4 => 'slider_factory', // Slider Factory controller (level 0 item no. 4)
 
      ), // end of level 0 items array
      $addonIconPath . '/section-combo-4-x-wrp.png', // section combo icon
      'gpSlideWrapper makeFullWidth' // wrapper class name(s)
    ); // end of new section entry
    // end of multi-nested wrapper section combo


    // Responsive Image Slider - depends on Responsive Image Plugins
    $ResponsiveImageInstalled = false;
    foreach ($config['addons'] as $addon_key => $addon_info) {
      if ( $addon_info['name'] == 'Responsive Image' && version_compare( $addon_info['version'], '1.5b3') >= 0 ){
        $ResponsiveImageInstalled = "true";
        break;
      }
    }
    if ( $ResponsiveImageInstalled ){

      $links[] = array( // new section entry

        0 => array( // level 0 items array
   
          0 =>  array( // .gpSlide (level 0 item no. 1)
            0 => array( // level 1 items array
              0 => 'CustomSlide1_responsive_background', // level 1 item
              1 => array( // .container
                0 =>  array( // level 2 items array
                  'CustomSlide1_text', // section-type.class names
                ),
                'container', // wrapper class name(s)
              ),
            ),
            'gpSlide',
          ),

          1 =>  array( // .gpSlide (level 0 item no. 2)
            0 => array( // level 1 items array
              0 => 'CustomSlide2_responsive_background', 
              1 => array( // .container
                0 =>  array( // level 2 items array
                  'CustomSlide2_text', // section-type.class names
                ),
                'container', // wrapper class name(s)
              ),
            ),
            'gpSlide',
          ),

          2 =>  array( // .gpSlide (level 0 item no. 3)
            0 => array( // level 1 items array
              0 => 'CustomSlide3_responsive_background', 
              1 => array( // .container
                0 =>  array( // level 2 items array
                  'CustomSlide3_text', // section-type.class names
                ),
                'container', // wrapper class name(s)
              ),
            ),
            'gpSlide',
          ),

          3 =>  array( // .gpSlide (level 0 item no. 4)
            0 => array( // level 1 items array
              0 => 'CustomSlide4_responsive_background', 
              1 => array( // .container
                0 =>  array( // level 2 items array
                  'CustomSlide4_text', // section-type.class names
                ),
                'container', // wrapper class name(s)
              ),
            ),
            'gpSlide',
          ),

          4 => 'CustomSlider_slider_factory', // Slider Factory controller (level 0 item no. 5)
   
        ), // end of level 0 items array
        $addonIconPath . '/custom-section-combo-4-x-respoBg.png', // section combo icon
        'gpSlideWrapper makeFullWidth', // wrapper class name(s)
      ); // end of new section entry

    } // end of if $ResponsiveImageInstalled

    return $links;
  }




  static function DefaultContent($default_content, $type){
    global $addonPathCode;

    switch( $type ){

      // default slider factory controller section
      case 'slider_factory' : 
        include($addonPathCode . '/default_content/slider_factory.php');
        return $newSection;

      // custom slider factory sample combo with responsive backgrounds
      case 'CustomSlider_slider_factory' : 
        include($addonPathCode . '/default_content/ri_combo/CustomSlider_slider_factory.php');
        return $newSection;

      // slide 1
      case 'CustomSlide1_responsive_background' : 
        include($addonPathCode . '/default_content/ri_combo/CustomSlide1_responsive_background.php');
        return $newSection;
      case 'CustomSlide1_text' : 
        include($addonPathCode . '/default_content/ri_combo/CustomSlide1_text.php');
        return $newSection;

      // slide 2
      case 'CustomSlide2_responsive_background' : 
        include($addonPathCode . '/default_content/ri_combo/CustomSlide2_responsive_background.php');
        return $newSection;
      case 'CustomSlide2_text' : 
        include($addonPathCode . '/default_content/ri_combo/CustomSlide2_text.php');
        return $newSection;

      // slide 3
      case 'CustomSlide3_responsive_background' : 
        include($addonPathCode . '/default_content/ri_combo/CustomSlide3_responsive_background.php');
        return $newSection;
      case 'CustomSlide3_text' : 
        include($addonPathCode . '/default_content/ri_combo/CustomSlide3_text.php');
        return $newSection;

      // slide 4
      case 'CustomSlide4_responsive_background' : 
        include($addonPathCode . '/default_content/ri_combo/CustomSlide4_responsive_background.php');
        return $newSection;
      case 'CustomSlide4_text' : 
        include($addonPathCode . '/default_content/ri_combo/CustomSlide4_text.php');
        return $newSection;

    }

    return $default_content;
  }




  static function SaveSection($return, $section, $type){
    global $page;
    if( $type == 'slider_factory' ){
      $content =& $_POST['gpcontent'];
      $page->file_sections[$section]['content'] = $content;
      return true;
    }
    return $return;
  }




  static function InlineEdit_Scripts($scripts, $type){
    global $addonRelativeCode, $addonFolderName, $addonCodeFolder;
    if( $type == 'slider_factory' ) {
      $scripts[] = '/include/js/inline_edit/inline_editing.js';
      $addonBasePath = (strpos($addonRelativeCode,'/addons/') !== false) ? '/addons/' . $addonFolderName : '/data/_addoncode/' . $addonFolderName;
      echo 'var SliderFactory = { base : "' . $addonBasePath . '" }; ';
      $scripts[] = $addonCodeFolder . '/SliderFactory_edit.js'; 
    }
    return $scripts;
  }


} // class end 