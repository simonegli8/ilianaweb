/*
########################################################################
JS/jQuery script for gpEasy Slider Factory - Init
Author: J. Krausz
Date: 2016-04-28
Version: 1.0.1
########################################################################
*/

$(document).ready( function() {

  $(".filetype-slider_factory").each( function() {
    SliderFactory_init( $(this) );
  });

}); /* domready end */

/* #### NEW SectionAdded EVENT ### */

/* --- newly added Section ---- */
$(document).on("SectionAdded PreviewAdded", ".filetype-slider_factory", function(){
  console.log("New Slider Factory section added");
  SliderFactory_init( $(this) );
});

/* --- Section in newly created Section Combo --- */
$(document).on("SectionAdded PreviewAdded", ".filetype-wrapper_section", function(){
  var NewSfSection = $(this).find(".filetype-slider_factory");
  if ( NewSfSection.length > 0 ){
    console.log("New Slider Factory Section added inside Section Combo");
    SliderFactory_init( NewSfSection );
  }
});

/* --- Section dragged into or sorted inside existing Slider --- */
$(document).on("SectionSorted", ".GPAREA", function(){
  var isInSlider = 
    $(this).parent().is(".filetype-wrapper_section") 
    && $(this).siblings(".filetype-slider_factory").length > 0;
  if ( isInSlider ){
    console.log("Section dragged into or sorted inside Slider wrapper");
    SliderFactory_init( $(this).siblings(".filetype-slider_factory") );
  }
});

/* --- Section removed from existing Slider --- */
$(document).on("SectionRemoved PreviewRemoved", ".filetype-wrapper_section", function(){
  var isSlider = $(this).children(".filetype-slider_factory").length > 0;
  if ( isSlider ){
    console.log("Section removed from Slider wrapper");
    SliderFactory_init( $(this).children(".filetype-slider_factory") );
  }
});


function SliderFactory_init(sfSection){

  var slideWrapper = sfSection.closest(".filetype-wrapper_section");

  // warnings:
  if (slideWrapper.length < 1) {
    if (isadmin) {
      /* alert(
        "Warning! \n\nA 'Slider Factory' section is not inside a Section Wrapper."
        + "\nPlease use 'Manage Sections' to drag it into a wrapper "
        + "\nor - in case it's a remnant - delete it."
      ); */
      sfSection.addClass("sliderfacory-outOfWrapperWarning");
    }
    return; // skip this one
  }
  if (sfSection.next(".filetype-slider_factory").length > 0) {
    if (isadmin) {
      alert(
        "Warning! \n\nIt seems you have more than one 'Slider Factory' sections inside a Section Wrapper!"
        + "\nPlease remove them using 'Manage Sections'."
      );
    }
    return; // skip this one
  }

  // get slider options
  var sliderOptions = {};
  var sfData = sfSection.find(".sliderfactory-data");

  sliderOptions.slideSelector = 
    sfData.attr("data-slide-selector") 
    ? sfData.attr("data-slide-selector") 
    : ".GPAREA:not(.filetype-slider_factory)"; 

  if (sfData.attr("data-transition-effect")) {  sliderOptions.transitionEffect = sfData.attr("data-transition-effect"); }
  if (sfData.attr("data-random-start")) { sliderOptions.randomStart = sfData.attr("data-random-start") == "true" ? true : false; }
  if (sfData.attr("data-shuffle")) { sliderOptions.shuffle = sfData.attr("data-shuffle") == "true" ? true : false; }
  if (sfData.attr("data-show-prev-next")) { sliderOptions.showPrevNext = sfData.attr("data-show-prev-next") == "true" ? true : false; }
  if (sfData.attr("data-prev-text")) { sliderOptions.prevText = sfData.attr("data-prev-text"); }
  if (sfData.attr("data-next-text")) { sliderOptions.nextText = sfData.attr("data-next-text"); }
  if (sfData.attr("data-show-indicators")) { sliderOptions.showIndicators = sfData.attr("data-show-indicators") == "true" ? true : false; }
  if (sfData.attr("data-pause-on-hover")) { sliderOptions.pauseOnHover = sfData.attr("data-pause-on-hover") == "true" ? true : false; }
  if (sfData.attr("data-delay")) { sliderOptions.delay = sfData.attr("data-delay"); }
  if (sfData.attr("data-speed")) { sliderOptions.speed = sfData.attr("data-speed"); }
  if (sfData.attr("data-timeout")) { sliderOptions.timeout = sfData.attr("data-timeout"); }
  if (sfData.attr("data-easing")) { sliderOptions.easing = sfData.attr("data-easing"); }
  if (sfData.attr("data-theme")) { sliderOptions.theme = sfData.attr("data-theme"); } else { sliderOptions.theme = "gpSliderTheme-default"; }
  if (sfData.attr("data-height")) { sliderOptions.height = sfData.attr("data-height"); } 
  if (sfData.attr("data-height-unit")) { sliderOptions.heightUnit = sfData.attr("data-height-unit"); } 


  // before and after callback tests
  sliderOptions.before = function() {
    // console.log( "SliderFactory: 'before' callback fn executed @ slide " + $(this).data("slide-index") );
  }
  sliderOptions.after = function() {
    // console.log( "SliderFactory: 'after' callback fn executed @ slide " + $(this).data("slide-index") );
  }

  // init slider
  slideWrapper.gpSlider(sliderOptions);
  // trigger window.resize event for Responsive Image Sections
  $(window).resize();

} /* init slider --end */