/*
########################################################################
JS/jQuery script for gpEasy Slider Factory - Editor Component
Author: J. Krausz
Date: 2016-04-28
Version: 1.0.1
########################################################################
*/

function gp_init_inline_edit(area_id,section_object) { 

  // console.log("section_object = ", section_object);

  $gp.LoadStyle( SliderFactory.base + '/SliderFactory_edit.css' );
  gp_editing.editor_tools();
  edit_div = gp_editing.get_edit_area(area_id);
  slide_wrapper = edit_div.closest(".filetype-wrapper_section");
  sf_data = slide_wrapper.find(".sliderfactory-data");

  gp_editor = {

    save_path           : gp_editing.get_path(area_id),
    destroy             : function() {},
    checkDirty          : function() { return gp_editor.optionsChanged; },
    resetDirty          : function() {},
    updateElement       : function() {},
    gp_saveData         : function() {},

    optionsChanged      : false,
    sliderOptions       : {},
    getOptions          : function() {},
    setOptions          : function() {},
    reInitSlider        : function() {},
    showPrevNext        : function() {},
    showIndicators      : function() {},
    setTheme            : function() {},
    setText             : function() {},
    setHeight           : function() {},
    convertHeight       : function() {},
    
    resizableInit       : function() {},
    resizableCreate     : function() {},
    resizableResize     : function() {},
    updateTimer         : false

  } /* obj gp_editor --end */


  gp_editor.gp_saveData = function() {
    var save_clone = edit_div.clone();
    save_clone.find(".gpclear, .gpSlideIndicators").remove();
    var content = save_clone.html();
    save_clone = null;
    gp_editor.optionsChanged = false;
    return '&gpcontent=' + encodeURIComponent(content);
  }; /* fnc gp_editor.gp_saveData --end */



  gp_editor.reInitSlider = function() {
    slide_wrapper.gpSlider(gp_editor.sliderOptions);
  }; /* fnc gp_editor.reInitSlider --end */



  gp_editor.showPrevNext = function(show) {
    if ( show ){
      slide_wrapper.find(".gpPrevSlide, .gpNextSlide").fadeIn("fast");
    }else{
      slide_wrapper.find(".gpPrevSlide, .gpNextSlide").fadeOut("fast");
    }
  }; /* fnc gp_editor.showPrevNext --end */



  gp_editor.showIndicators = function(show) {
    if ( show ){
      slide_wrapper.find(".gpSlideIndicators").fadeIn("fast");
    }else{
      slide_wrapper.find(".gpSlideIndicators").fadeOut("fast");
    }
  }; /* fnc gp_editor.showIndicators --end */



  gp_editor.setTheme = function() {
    slide_wrapper
      .removeClass("gpSliderTheme-default gpSliderTheme-navbar gpSliderTheme-text gpSliderTheme-custom")
      .addClass(gp_editor.sliderOptions.theme);
  }; /* fnc gp_editor.setTheme --end */



  gp_editor.setText = function() {
    var prevText = gp_editor.sliderOptions.prevText;
    var nextText = gp_editor.sliderOptions.nextText;
    slide_wrapper.find(".gpPrevSlide").text(prevText).attr("title", prevText);
    slide_wrapper.find(".gpNextSlide").text(nextText).attr("title", nextText);
    $("#gpSliderEdit_prev").val(prevText);
    $("#gpSliderEdit_next").val(nextText);
  }; /* fnc gp_editor.setText --end */



  gp_editor.setHeight = function() {
    var newHeight = gp_editor.sliderOptions.height != "" ? gp_editor.sliderOptions.height : "auto";
    if (newHeight == "auto") {
      slide_wrapper.css("padding-bottom", 0).addClass("gpSlider_calculateHeight");
      // triger window resize to re-calc slider height
      $(window).trigger("resize");
    } else {
      slide_wrapper.removeClass("gpSlider_calculateHeight");
      if (gp_editor.sliderOptions.heightUnit == "px") {
        slide_wrapper.css("padding-bottom", 0).outerHeight(newHeight);
      } else {
        slide_wrapper.css({ "height" : 0, "padding-bottom" : newHeight + "%" });
      }
    }
  }; /* fnc gp_editor.setHeight --end */



  gp_editor.convertHeight = function(val) {
    if ( val == "" ){ return; }
    if ( gp_editor.sliderOptions.heightUnit == "%" ){
      var sliderWidth = slide_wrapper.outerWidth();
      if ( slide_wrapper.hasClass("makeFullWidth") ){
        // fix strange calculation when using 100vw
        sliderWidth -= sliderWidth - $("#gpx_content").innerWidth(); 
      }
      console.log("sliderWidth:" + sliderWidth + " | height:" + val);
      var newHeightVal = Math.round(val / sliderWidth * 1000) / 10;
    } else {
      var newHeightVal = slide_wrapper.outerHeight();
    }
    return newHeightVal;
  }; /* fnc gp_editor.convertHeight --end */



  gp_editor.getOptions = function() {
    // defaults:
    gp_editor.sliderOptions = {
      slideSelector : ".GPAREA:not(.filetype-slider_factory)",
      transitionEffect : "fadeout",
      randomStart : false,
      shuffle : false,
      showPrevNext : true,
      prevText : "prev",
      nextText : "next",
      showIndicators : true,
      pauseOnHover : true,
      delay : 0,
      speed : 600,
      timeout : 6000,
      easing : "swing",
      theme : "gpSliderTheme-default",
      height : "",
      heightUnit : "px"
    };
    if (sf_data.attr("data-transition-effect")) {  gp_editor.sliderOptions.transitionEffect = sf_data.attr("data-transition-effect"); }
    if (sf_data.attr("data-random-start")) { gp_editor.sliderOptions.randomStart = sf_data.attr("data-random-start") == "true" ? true : false; }
    if (sf_data.attr("data-shuffle")) { gp_editor.sliderOptions.shuffle = sf_data.attr("data-shuffle") == "true" ? true : false; }
    if (sf_data.attr("data-show-prev-next")) { gp_editor.sliderOptions.showPrevNext = sf_data.attr("data-show-prev-next") == "true" ? true : false; }
    if (sf_data.attr("data-prev-text")) { gp_editor.sliderOptions.prevText = sf_data.attr("data-prev-text"); }
    if (sf_data.attr("data-next-text")) { gp_editor.sliderOptions.nextText = sf_data.attr("data-next-text"); }
    if (sf_data.attr("data-show-indicators")) { gp_editor.sliderOptions.showIndicators = sf_data.attr("data-show-indicators") == "true" ? true : false; }
    if (sf_data.attr("data-pause-on-hover")) { gp_editor.sliderOptions.pauseOnHover = sf_data.attr("data-pause-on-hover") == "true" ? true : false; }
    if (sf_data.attr("data-delay")) { gp_editor.sliderOptions.delay = sf_data.attr("data-delay"); }
    if (sf_data.attr("data-speed")) { gp_editor.sliderOptions.speed = sf_data.attr("data-speed"); }
    if (sf_data.attr("data-timeout")) { gp_editor.sliderOptions.timeout = sf_data.attr("data-timeout"); }
    if (sf_data.attr("data-easing")) { gp_editor.sliderOptions.easing = sf_data.attr("data-easing"); }
    if (sf_data.attr("data-theme")) { gp_editor.sliderOptions.theme = sf_data.attr("data-theme"); }
    if (typeof(sf_data.attr("data-height")) != "undefined") { gp_editor.sliderOptions.height = sf_data.attr("data-height"); }
    if (sf_data.attr("data-height-unit")) { gp_editor.sliderOptions.heightUnit = sf_data.attr("data-height-unit"); }
  }; /* fnc gp_editor.getOptions --end */



  gp_editor.setOptions = function() {
    sf_data.attr("data-transition-effect", gp_editor.sliderOptions.transitionEffect);
    sf_data.attr("data-random-start", gp_editor.sliderOptions.randomStart);
    sf_data.attr("data-shuffle", gp_editor.sliderOptions.shuffle);
    sf_data.attr("data-show-prev-next", gp_editor.sliderOptions.showPrevNext);
    sf_data.attr("data-prev-text", gp_editor.sliderOptions.prevText);
    sf_data.attr("data-next-text", gp_editor.sliderOptions.nextText);
    sf_data.attr("data-show-indicators", gp_editor.sliderOptions.showIndicators);
    sf_data.attr("data-pause-on-hover", gp_editor.sliderOptions.pauseOnHover);
    sf_data.attr("data-delay", gp_editor.sliderOptions.delay);
    sf_data.attr("data-speed", gp_editor.sliderOptions.speed);
    sf_data.attr("data-timeout", gp_editor.sliderOptions.timeout);
    sf_data.attr("data-easing", gp_editor.sliderOptions.easing);
    sf_data.attr("data-theme", gp_editor.sliderOptions.theme);
    sf_data.attr("data-height", gp_editor.sliderOptions.height);
    sf_data.attr("data-height-unit", gp_editor.sliderOptions.heightUnit);
  }; /* fnc gp_editor.setOptions --end */



  /* --- INIT EDITOR --- */
  gp_editor.getOptions(); 
  slide_wrapper.resizable({
    start : function(event, ui) {
              var oh = ui.originalElement.outerHeight();
              ui.originalElement.css({ "padding-bottom": 0 }).outerHeight(oh);
              ui.originalSize.height = oh;
            },
    resize : function(event, ui) {
              var new_h = ui.size.height;
              gp_editor.sliderOptions.height = gp_editor.convertHeight(new_h); 
              $("#gpSliderEdit_height").val(gp_editor.sliderOptions.height);
            },
    stop   :  function(){
                gp_editor.setHeight();
                gp_editor.setOptions();
                gp_editor.updateTimer = false;
                gp_editor.optionsChanged = true;
              }
  });


  /* --- HEIGHT CHECK --- */
  if (slide_wrapper.outerHeight() < 36) {
    alert(
        "Your slider seems to have an inappropriate height! \n"
      + "\nThis might be due to a bad height value in the editor "
      + "\nor if your slider's Section Wrapper has a low height "
      + "\ndefined via CSS or inline style."
    );
  }

  /* === USER INTERFACE === */

  /* --- EDITOR AREA --- */
  $("#ckeditor_area .toolbar").append('<div class="gp_left">Slider Factory</div>');
  var option_area = $('<div id="gpSlider_options"/>').prependTo('#ckeditor_controls');

  /* PREV/NEXT NAV BUTTONS */
  $('<div class="half_width">' 
   + '<input type="button" id="gpSliderEdit_prev" class="ckeditor_control" />' 
   + '</div>')
  .appendTo(option_area)
  .find("input").val(gp_editor.sliderOptions.prevText)
  .on('click',function() {
    slide_wrapper.find(".gpPrevSlide").click();
  });
  $('<div class="half_width">' 
   + '<input type="button" id="gpSliderEdit_next" class="ckeditor_control" />' 
   + '</div>')
  .appendTo(option_area)
  .find("input").val(gp_editor.sliderOptions.nextText)
  .on('click',function() {
    slide_wrapper.find(".gpNextSlide").click();
  });


  /* PREV TEXT */
  $('<div class="half_width">'
   + '<input id="gpSliderEdit_prevText" type="text" />' 
   + '</div>')
    .appendTo(option_area)
    .find('input').val(gp_editor.sliderOptions.prevText)
    .on("keyup change paste", function() {
      gp_editor.sliderOptions.prevText = $(this).val();
      gp_editor.setOptions();
      gp_editor.setText();
      gp_editor.optionsChanged = true;
    });


  /* NEXT TEXT */
  $('<div class="half_width">'
   + '<input id="gpSliderEdit_nextText" type="text" />' 
   + '</div>')
    .appendTo(option_area)
    .find('input').val(gp_editor.sliderOptions.nextText)
    .on("keyup change paste", function() {
      gp_editor.sliderOptions.nextText = $(this).val();
      gp_editor.setOptions();
      gp_editor.setText();
      gp_editor.optionsChanged = true;
    });


  /* THEME */
  $('<div class="full_width">' 
   + '<label>Theme</label>' 
   + '<select style="width:100%;" id="gpSliderEdit_theme" class="ckeditor_input">' 
   + '<option value="gpSliderTheme-default">default</option>' 
   + '<option value="gpSliderTheme-navbar">navbar</option>' 
   + '<option value="gpSliderTheme-text">text</option>' 
   + '<option value="gpSliderTheme-custom">custom</option>' 
   + '</select>' 
   + '</div>')
  .appendTo(option_area)
  .find("select").val(gp_editor.sliderOptions.theme)
  .on("change", function() {
    gp_editor.sliderOptions.theme = $(this).val();
    gp_editor.setOptions();
    gp_editor.setTheme();
    gp_editor.optionsChanged = true;
  });


  /* SLIDER HEIGHT */
  var inputStep = gp_editor.sliderOptions.heightUnit == "px" ? 8 : 1;
  $('<div class="full_width">'
   + '<label>Slider Height</em></label><br/>'
   + '<input id="gpSliderEdit_height" placeholder="auto" type="number" step="' + inputStep + '" style="width:32%;"/>'
   + '<select style="width:32%; margin:0 0 4px 2%!important;" id="gpSliderEdit_heightUnit" class="ckeditor_input">' 
   + '<option value="px">px</option>' 
   + '<option value="%">% of width</option>' 
   + '</select>' 
   + '<input title="responsive automatic height" id="gpSliderEdit_setAutoHeight" type="button" class="ckeditor_control" '
   + 'style="height:20px; width:32%; padding:0!important; margin:0 0 4px 2%!important;" value="auto"/>' 
   + '</div>').appendTo(option_area);

  $("input#gpSliderEdit_height")
  .val(gp_editor.sliderOptions.height)
  .on("keyup change paste", function() {
    $(this).val(parseFloat($(this).val()));
    gp_editor.sliderOptions.height = $(this).val();
    gp_editor.setOptions();
    gp_editor.setHeight();
    gp_editor.optionsChanged = true;
  });

  $("select#gpSliderEdit_heightUnit")
  .val(gp_editor.sliderOptions.heightUnit)
  .on("change", function() {
    gp_editor.sliderOptions.heightUnit = $(this).val();
    var convertedHeight = gp_editor.convertHeight($("input#gpSliderEdit_height").val());
    var inputStep = $(this).val() == "px" ? 8 : 1;
    $("input#gpSliderEdit_height").val(convertedHeight).prop("step", inputStep);
    gp_editor.sliderOptions.height = convertedHeight;
    gp_editor.setOptions();
    gp_editor.setHeight();
    gp_editor.resizableInit();
    gp_editor.optionsChanged = true;
  });

  $("#gpSliderEdit_setAutoHeight")
  .on("click", function() {
    $("input#gpSliderEdit_height").val("");
    gp_editor.sliderOptions.height = "";
    $("select#gpSliderEdit_heightUnit").val("px");
    gp_editor.sliderOptions.heightUnit = "px";
    gp_editor.setOptions();
    gp_editor.setHeight();
    gp_editor.optionsChanged = true;
  });

  /* TRANSITION EFFECT */
  $('<div class="full_width">' 
   + '<label>Transition Effect</label>' 
   + '<select style="width:100%;" id="gpSliderEdit_transFx" class="ckeditor_input">' 
   + '<option value="slideH">slide horizontally</option>' 
   + '<option value="slideV">slide vertically</option>' 
   + '<option value="wipeH">wipe horizontally</option>' 
   + '<option value="wipeV">wipe vertically</option>' 
   + '<option value="crossfade">crossfade</option>' 
   + '<option value="zoomFade">zoom+fadeout</option>' 
   + '<option value="pulse">pulse</option>' 
   + '<option value="emerge">emerge</option>' 
   + '<option value="vanish">vanish</option>' 
   + '<option value="pushDown">push down</option>' 
   + '<option value="uncover">uncover</option>' 
   + '<option value="fadeOutIn">fadeout&rarr;fadein</option>' 
   + '</select>' 
   + '</div>')
  .appendTo(option_area)
  .find("select").val(gp_editor.sliderOptions.transitionEffect)
  .on("change", function() {
    gp_editor.sliderOptions.transitionEffect = $(this).val();
    gp_editor.setOptions();
    gp_editor.reInitSlider();
    gp_editor.optionsChanged = true;
  });


  /* EASING */
  $('<div class="full_width">' 
  + '<label>Transition Easing</label>' 
  + '<select style="width:100%;" id="gpSliderEdit_easing" class="ckeditor_input">' 
  + '<option value="swing">swing(default)</option>' 
  + '<option value="linear">linear</option>' 
  + '<option value="easeInQuad">easeInQuad</option>' 
  + '<option value="easeOutQuad">easeOutQuad</option>' 
  + '<option value="easeInOutQuad">easeInOutQuad</option>' 
  + '<option value="easeInCubic">easeInCubic</option>' 
  + '<option value="easeOutCubic">easeOutCubic</option>' 
  + '<option value="easeInOutCubic">easeInOutCubic</option>' 
  + '<option value="easeInQuart">easeInQuart</option>' 
  + '<option value="easeOutQuart">easeOutQuart</option>' 
  + '<option value="easeInOutQuart">easeInOutQuart</option>' 
  + '<option value="easeInQuint">easeInQuint</option>' 
  + '<option value="easeOutQuint">easeOutQuint</option>' 
  + '<option value="easeInOutQuint">easeInOutQuint</option>' 
  + '<option value="easeInSine">easeInSine</option>' 
  + '<option value="easeOutSine">easeOutSine</option>' 
  + '<option value="easeInOutSine">easeInOutSine</option>' 
  + '<option value="easeInExpo">easeInExpo</option>' 
  + '<option value="easeOutExpo">easeOutExpo</option>' 
  + '<option value="easeInOutExpo">easeInOutExpo</option>' 
  + '<option value="easeInCirc">easeInCirc</option>' 
  + '<option value="easeOutCirc">easeOutCirc</option>' 
  + '<option value="easeInOutCirc">easeInOutCirc</option>' 
  + '</select>' 
  + '</div>')
  .appendTo(option_area)
  .find("select").val(gp_editor.sliderOptions.easing)
  .on("change", function() {
      gp_editor.sliderOptions.easing = $(this).val();
      gp_editor.setOptions();
      gp_editor.reInitSlider();
      gp_editor.optionsChanged = true;
  });


  /* TRANSITION SPEED */
  $('<div class="full_width">'
   + '<label>Transition Speed <em>(in ms)</em></label>'
   + '<input id="gpSliderEdit_speed" type="number" min="0" step="100" />' 
   + '</div>')
    .appendTo(option_area)
    .find('input').val(gp_editor.sliderOptions.speed)
    .on("keyup change paste", function() {
      $(this).val(parseInt($(this).val()));
      gp_editor.sliderOptions.speed = $(this).val();
      gp_editor.setOptions();
      gp_editor.reInitSlider();
      gp_editor.optionsChanged = true;
    });


  /* INTERVAL / SLIDE TIMEOUT */
  $('<div class="full_width">'
  + '<label>Interval <em>(in ms)</em></label><br/>'
  + '<input id="gpSliderEdit_timeout" placeholder="0=no auto-advance" type="number" min="0" step="100" style="width:48%;"/>' 
  + '<input id="gpSliderEdit_zeroTimeout" type="button" class="ckeditor_control" '
  + 'style="height:20px; width:48%; padding:0!important; margin:0 0 4px 4%!important;" value="no autoplay"/>' 
  + '</div>').appendTo(option_area);
  $('#gpSliderEdit_timeout')
    .val(gp_editor.sliderOptions.timeout)
    .on("keyup change paste", function() {
      $(this).val(parseInt($(this).val()));
      if ( $(this).val() == 0 && !gp_editor.sliderOptions.showIndicators && !gp_editor.sliderOptions.showPrevNext ){
        if ( confirm(
              "You set the interval to zero, which means the slider will not auto-advance. \n"
              + "You should probably enable prev/next or indicator navigation. Enable them now?"
            ) ){
          $("#gpSliderEdit_showPrevNext").click();
          $("#gpSliderEdit_showIndicators").click();
        }
      }
      gp_editor.sliderOptions.timeout = $(this).val();
      gp_editor.setOptions();
      gp_editor.reInitSlider();
      gp_editor.optionsChanged = true;
    });
  $('#gpSliderEdit_zeroTimeout')
    .on("click", function() {
      $('#gpSliderEdit_timeout').val(0);
      if (!gp_editor.sliderOptions.showIndicators && !gp_editor.sliderOptions.showPrevNext){
        if ( confirm(
              "Setting the interval to zero which means the slider will not auto advance.\n"
              + "You should probably enable prev/next or indicator navigation. Enable them now?"
            ) ){
          $("#gpSliderEdit_showPrevNext").click();
          $("#gpSliderEdit_showIndicators").click();
        }
      }
      gp_editor.sliderOptions.timeout = 0;
      gp_editor.setOptions();
      gp_editor.reInitSlider();
      gp_editor.optionsChanged = true;
    });


  /* SHOW PREV/NEXT */
  $('<div class="full_width">' 
   + '<label style="width:100%;" class="gpcheckbox">' 
   + '<input id="gpSliderEdit_showPrevNext" class="gpcheck" type="checkbox"/>' 
   + ' show Prev/Next navigation</label>' 
   + '</div>')
  .appendTo(option_area)
  .find("input").prop("checked", gp_editor.sliderOptions.showPrevNext)
  .on("change", function() {
      gp_editor.sliderOptions.showPrevNext = $(this).prop("checked");
      gp_editor.showPrevNext($(this).prop("checked"));
      if (!$(this).prop("checked") && !gp_editor.sliderOptions.showIndicators && gp_editor.sliderOptions.timeout == 0){
        alert(
           "You have disabled both manual navigation methods and set the interval to zero, "
         + "\nwhich means the slider will not auto advance and cannot be navigated. "
         + "\nYou should probably enable prev/next or indicator navigation or set an interval timing!"
        );
      }
      gp_editor.setOptions();
      gp_editor.optionsChanged = true;
    });


  /* SHOW INDICATORS */
  $('<div class="full_width">' 
   + '<label style="width:100%;" class="gpcheckbox">' 
   + '<input id="gpSliderEdit_showIndicators" class="gpcheck" type="checkbox"/>' 
   + ' show indicators/pagination</label>' 
   + '</div>')
  .appendTo(option_area)
  .find("input").prop("checked", gp_editor.sliderOptions.showIndicators)
  .on("change", function() {
      gp_editor.sliderOptions.showIndicators = $(this).prop("checked");
      gp_editor.showIndicators($(this).prop("checked"));
      if ( !$(this).prop("checked") && !gp_editor.sliderOptions.showPrevNext && gp_editor.sliderOptions.timeout == 0){
        alert(
           "You have disabled both manual navigation methods and set the interval to zero, "
         + "\nwhich means the slider will not auto advance and cannot be navigated. "
         + "\nYou should probably enable prev/next or indicator navigation or set an interval timing!"
        );
      }
      gp_editor.setOptions();
      gp_editor.optionsChanged = true;
    });

  /* RAMDOM START SLIDE */
  $('<div class="full_width">' 
   + '<label style="width:100%;" class="gpcheckbox" title="you must be logged-off to see this effect">' 
   + '<input id="gpSliderEdit_randomStart" class="gpcheck" type="checkbox"/>' 
   + ' start with random slide</label>' 
   + '</div>')
  .appendTo(option_area)
  .find("input").prop("checked", gp_editor.sliderOptions.randomStart)
  .on("change", function() {
      gp_editor.sliderOptions.randomStart = $(this).prop("checked");
      gp_editor.setOptions();
      gp_editor.optionsChanged = true;
  });

  /* SHUFFLE */
  $('<div class="full_width">' 
   + '<label style="width:100%;" class="gpcheckbox" title="you must be logged-off to see this effect">' 
   + '<input id="gpSliderEdit_shuffle" class="gpcheck" type="checkbox"/>' 
   + ' shuffle slides on load</label>' 
   + '</div>')
  .appendTo(option_area)
  .find("input").prop("checked", gp_editor.sliderOptions.shuffle)
  .on("change", function() {
      gp_editor.sliderOptions.shuffle = $(this).prop("checked");
      gp_editor.setOptions();
      gp_editor.optionsChanged = true;
  });

  /* PAUSE ON HOVER */
  $('<div class="full_width">' 
   + '<label style="width:100%;" class="gpcheckbox">' 
   + '<input id="gpSliderEdit_pauseOnHover" class="gpcheck" type="checkbox"/>' 
   + ' pause slider on hover</label>' 
   + '</div>')
  .appendTo(option_area)
  .find("input").prop("checked", gp_editor.sliderOptions.pauseOnHover)
  .on("change", function() {
      gp_editor.sliderOptions.pauseOnHover = $(this).prop("checked");
      gp_editor.setOptions();
      gp_editor.reInitSlider();
      gp_editor.optionsChanged = true;
  });


  loaded();

} /* main fnc gp_init_inline_edit --end */
