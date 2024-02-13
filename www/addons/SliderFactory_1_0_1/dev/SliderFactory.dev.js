/*
########################################################################
JS/jQuery script for gpEasy Slider Factory - Main Slider Module
Author: J. Krausz
Date: 2016-04-28
Version: 1.0.1
########################################################################
*/

/*
 * Inspired and largely based on Malsup's jQuery Cycle Light - http://malsup.com/jquery/cycle/lite/
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */

;(function($) {
  "use strict";

  $.fn.gpSlider = function(options) {

    return this.each( function() {

      options = options || {};
      var opts = $.extend({}, $.fn.gpSlider.defaults, options || {});

      // sanitize timeout
      opts.timeout = (!opts.timeout || opts.timeout == "") ? 0 : opts.timeout;

      // clear timeout if present
      if ( this.sliderTimeout ){
        clearTimeout(this.sliderTimeout);
      }

      this.sliderTimeout = 0;
      this.sliderPaused = 0;
      var slideWrapper = $(this);

      // find slides
      var $slides = {};
      // detect Simple Blog Gadget
      if ( slideWrapper.find(".simple_blog_title").length > 0 ){
        var blog_gadget_area = slideWrapper.find(".simple_blog_title").closest(".GPAREA");
        blog_gadget_area.children("h3").hide();
        blog_gadget_area.children("a").hide();
        blog_gadget_area.children(".simple_blog_title").each( function(){
          var simple_blog_slide =  $('<div class="simple_blog_slide"/>').prependTo(blog_gadget_area);
          var simple_blog_info =   $(this).siblings(".simple_blog_info").first();
          var simple_blog_abbrev = $(this).siblings(".simple_blog_abbrev").first();
          simple_blog_slide.append($(this));
          simple_blog_slide.append(simple_blog_info);
          simple_blog_slide.append(simple_blog_abbrev);
        });
        opts.slideSelector = ".simple_blog_slide";
        $slides = slideWrapper.find(opts.slideSelector);
      } else {
        $slides = slideWrapper.children(opts.slideSelector);
      }
      $slides.each( function(i) {
        var $this = $(this);
        $this.addClass("gpSlide gpSlide-noTransitions");
        setTimeout( function() {
          $this.removeClass("gpSlide-noTransitions");
        }, 250);
        $this.data("slide-index", i);
      });

      // set wrapper theme and height
      slideWrapper.addClass("gpSlideWrapper " + opts.theme);
      if ( slideWrapper.hasClass("gpSlider_calculateHeight") ){
        $(window).on("load", function() {
          gpSlider_setHeight(slideWrapper, $slides, opts.height, opts.heightUnit);
        });
      }else{
        gpSlider_setHeight(slideWrapper, $slides, opts.height, opts.heightUnit);
      }

      // re-calc height on window resize
      $(window).on("resize", function() {
        if ( slideWrapper.hasClass("gpSlider_calculateHeight") ){
          gpSlider_setHeight(slideWrapper, $slides, opts.height, opts.heightUnit);
        }
      });


      // Shuffle Slides
      if ( opts.shuffle && !isadmin ){
        while ( $slides.length ){
          $slides.parent()
            .append($slides.splice(Math.floor(Math.random() * $slides.length), 1)[0]);
        }
       // $slides = slideWrapper.children(opts.slideSelector);
       $slides = slideWrapper.find(".gpSlide");
      }

      var slideElements = $slides.get();

      // early exit if too few slides present
      if ( slideElements.length < 2 ){
        // console.log("gpSlider: At least 2 slides needed - we have " + slideElements.length);
        return;
      }

      opts.before = opts.before ? [opts.before] : [];
      opts.after =  opts.after  ? [opts.after]  : [];
      opts.after.unshift( function(){ opts.busy = 0; } );
      opts.timeout = parseInt(opts.timeout);
      opts.speed = parseInt(opts.speed);


      // Start Slide
      var startSlide = opts.randomStart && !isadmin ? Math.round(Math.random() * (slideElements.length-1)) : 0; 
      // console.log("startSlide:" + startSlide);

      // reset styles
      $slides.css({
        position    : "absolute", 
        top         : 0,
        left        : 0,
        opacity     : 1,
        "z-index"   : "auto",
        "-webkit-transform" : "scale(1,1) translate(0,0) rotateX(0) rotateY(0) rotateZ(0) rotate3d(0,0,0,0)",
        "-moz-transform" : "scale(1,1) translate(0,0) rotateX(0) rotateY(0) rotateZ(0) rotate3d(0,0,0,0)",
        "-ms-transform" : "scale(1,1) translate(0,0) rotateX(0) rotateY(0) rotateZ(0) rotate3d(0,0,0,0)",
        "-o-transform" : "scale(1,1) translate(0,0) rotateX(0) rotateY(0) rotateZ(0) rotate3d(0,0,0,0)",
        "transform" : "scale(1,1) translate(0,0) rotateX(0) rotateY(0) rotateZ(0) rotate3d(0,0,0,0)",
      })
      .show();

      // Pause on Hover
      if ( opts.pauseOnHover ){
        slideWrapper.on("mouseenter admin:mouseenter", function() { 
          // console.log("Slider Paused");
          $(this).get(0).sliderPaused = 1;
        });
        slideWrapper.on("mouseleave", function() { 
          // console.log("Slider Unpaused");
          $(this).get(0).sliderPaused = 0;
        });
      }

      // Pagination/Indicators
      slideWrapper.find(".gpSlideIndicators").remove(); // for re-init
      if ( opts.showIndicators || isadmin ){
        var indicators = $('<ul class="gpSlideIndicators" />');
        $slides.each( function(i) {
          $('<li data-gpSlider_gotoSlide="' + i + '"><a role="button">' + (i+1) + '</a></li>')
          .on("click", function() {
            opts.nextSlide = parseInt($(this).attr("data-gpSlider_gotoSlide"));
            var fwd = opts.nextSlide > opts.currSlide;
            gpSlider_gotoSlide(slideElements, opts, true, fwd, false);
          })
          .appendTo(indicators);
        });
        indicators.prependTo($(this).find(".filetype-slider_factory"));
        // hide if logged-in but elements don't show regulary
        if ( !opts.showIndicators && isadmin ){
          indicators.hide();
        }
      }

      // Prev/Next Navigation
      slideWrapper.find(".gpPrevSlide, .gpNextSlide").remove(); // for re-init
      if ( opts.showPrevNext || isadmin ){
        slideWrapper
          .prepend('<a title="' + slideWrapper.find(".sliderfactory-data").attr("data-next-text") 
          + '" class="gpNextSlide" role="button">' 
          + slideWrapper.find(".sliderfactory-data").attr("data-next-text")
          + '</a>')
          .prepend('<a title="' + slideWrapper.find(".sliderfactory-data").attr("data-prev-text") 
          + '" class="gpPrevSlide" role="button">' 
          + slideWrapper.find(".sliderfactory-data").attr("data-prev-text")
          + '</a>');
        slideWrapper.find(".gpNextSlide, .next-slide, a[href='#next-slide']")
          .bind("click.slide", function(e) {
          e.preventDefault();
          return gpSlider_advance(slideElements, opts, 1);
        });
        slideWrapper.find(".gpPrevSlide, .prev-slide, a[href='#prev-slide']")
          .bind("click.slide", function(e) { 
          e.preventDefault();
          return gpSlider_advance(slideElements, opts, -1);
        });
        // hide if logged-in but elements don't show regulary
        if ( !opts.showPrevNext && isadmin ){
          slideWrapper.find(".gpPrevSlide, .gpNextSlide").hide();
        }
      }

      // touchswipe
      slideWrapper.unbind("swipeleft").bind("swipeleft", function() {
        return gpSlider_advance(slideElements, opts, 1);
      });
      slideWrapper.unbind("swiperight").bind("swiperight", function() {
        return gpSlider_advance(slideElements, opts, -1);
      });

      var txFn = $.fn.gpSlider.transitions[opts.transitionEffect];
      if ( txFn ){
        txFn(slideWrapper, $slides, opts); 
      }

      if ( opts.timeout != 0 && opts.timeout - opts.speed < 150 ){
        ///console.log("opts.timeout=" + opts.timeout + "opts.speed=" + opts.speed);
        opts.timeout = opts.speed + 150;
        // console.log("new opts.timeout=" + opts.timeout);
      }



      opts.slideCount = slideElements.length;
      opts.currSlide = startSlide-1 < 0 ? opts.slideCount-1 : startSlide-1;
      opts.nextSlide = startSlide;

      // Init
      gpSlider_gotoSlide(slideElements, opts, true, true, true);
      $(slideElements[opts.currSlide]).css({ "opacity" : 1, "z-index" : opts.slideCount }).show();

    });

  }; /* fn.gpSlider end */


  function gpSlider_setHeight(slideWrapper, slides, height, heightUnit) {
    // alert("setHeight");
    var newHeight = 0;
    var recalc_height = slideWrapper.hasClass("gpSlider_calculateHeight");
    if ( !height || height == "" || recalc_height ){
      var cssHeight = 0;
      if (recalc_height){
        slideWrapper.css("height", "auto");
      } else {
        // probe wrapper for css height declaration by hiding it
        slideWrapper.hide();
        cssHeight = parseInt(slideWrapper.css("height"), 10);
        slideWrapper.show();
      }
      if ( cssHeight < 24 || recalc_height ){
        // 24px because .editable_area min-height:1em when logged in
        // get height of tallest slide
        var max_h = 0;
        slides.each(function() {
          $(this).addClass("gpSlide-measureHeight");
          var slide_h = $(this).outerHeight();
          // alert(slide_h);
          $(this).removeClass("gpSlide-measureHeight");
          max_h = slide_h>max_h ? slide_h : max_h;
        });
      }
      // alert("max_h:" + max_h);
      newHeight = max_h;
      slideWrapper.addClass("gpSlider_calculateHeight");
    } else {
      newHeight = height;
    }
    // slides.height(newHeight);
    slideWrapper.outerHeight(newHeight);
    if (heightUnit == "px") {
      slideWrapper.outerHeight(newHeight).css("padding-bottom", 0);
    } else {
      slideWrapper.css({ "height" : 0, "padding-bottom" : newHeight + "%" });
    }
  }




  function gpSlider_gotoSlide(slideElements, opts, manual, fwd, instant) {
    // early exit if busy
    if ( opts.busy ){ 
      return; 
    }

    // var slidesParent = slideElements[0].parentNode;
    //var slidesParent = $(slideElements[0]).closest(".gpSlideWrapper").get(0);
    var slidesParent = $(slideElements[0]).closest(".filetype-wrapper_section").get(0);
    var currSlide  = slideElements[opts.currSlide]
    var nextSlide  = slideElements[opts.nextSlide];

    if ( slidesParent.sliderTimeout ){
      clearTimeout(slidesParent.sliderTimeout);
    }

    // pause when slider sections are edited or Section Manager is loaded
    if ( isadmin ){
      if ( ($(slidesParent).find(".gp_editing").length + $("#ckeditor_wrap").length) > 0 ){
        slidesParent.sliderPaused = 1;
      }
    }

    // console.log("currSlide:" + opts.currSlide + " | nextSlide:" + opts.nextSlide);
    // console.log("sliderTimeout:" + slidesParent.sliderTimeout + " | sliderPaused:" + slidesParent.sliderPaused);

    if ( slidesParent.sliderTimeout === 0 && !manual ){
      return;
    }

    if ( manual || !slidesParent.sliderPaused ){
      if ( opts.before.length ){
        $.each(opts.before, function(i,o) { 
          o.apply(nextSlide, [currSlide, nextSlide, opts, fwd]); 
        });
      }
      var after = function() {
        $.each(opts.after, function(i,o) { 
          o.apply(nextSlide, [currSlide, nextSlide, opts, fwd]); 
        });
        queueNext(opts);
      };

      if ( opts.nextSlide != opts.currSlide ){
        if ( instant ){
          var ospeed = opts.speed;
          opts.speed = 0;
          $.fn.gpSlider.custom(currSlide, nextSlide, opts, after);
          opts.speed = ospeed;
          // console.log("instant switch to slide " + opts.nextSlide);
        } else {
          opts.busy = 1;
          $.fn.gpSlider.custom(currSlide, nextSlide, opts, after);
        }
      }
      // set indicators
      if ( opts.showIndicators || isadmin ){
        var indicators = $(slideElements[0]).closest(".gpSlideWrapper").find(".gpSlideIndicators li");
        indicators.removeClass("gpSlideActive");
        indicators.eq(opts.nextSlide).addClass("gpSlideActive");
      }
      var roll = (opts.nextSlide + 1) == slideElements.length;
      opts.nextSlide = roll ? 0 : opts.nextSlide + 1;
      opts.currSlide = roll ? slideElements.length - 1 : opts.nextSlide - 1;
    } else {
      queueNext(opts);
    }

    function queueNext(opts) {
      if ( parseInt(opts.timeout) > 0 ){
        slidesParent.sliderTimeout = setTimeout( function() { 
          gpSlider_gotoSlide(slideElements,opts,0,!opts.rev, false); 
        }, parseInt(opts.timeout));
      }
    }
  }


  // advance slide fwd <-> back
  function gpSlider_advance(slideElements, opts, val) {
    var slidesParent = $(slideElements[0]).closest(".gpSlideWrapper").get(0);
    var timeout = slidesParent.sliderTimeout;
    if ( timeout ){
      clearTimeout(timeout);
      slidesParent.sliderTimeout = 0;
    }
    opts.nextSlide = opts.currSlide + val;

    if ( opts.nextSlide < 0 ){
      opts.nextSlide = slideElements.length - 1;
    }
    else if ( opts.nextSlide >= slideElements.length ){
      opts.nextSlide = 0;
    }
    gpSlider_gotoSlide(slideElements, opts, 1, val>=0, false);
    return false;
  }


  $.fn.gpSlider.custom = function(curr, next, opts, cb) {
    var $c = $(curr);
    var $n = $(next);
    var speed = opts.transitionEffect == "fadeOutIn" ? parseInt(opts.speed)/2 : parseInt(opts.speed);

    $c.removeClass("gpSlide-active");

    // skip animations if speed == 0
    if ( speed === 0 ){
      $c.velocity(opts.animOut, 0).css(opts.cssAfter);
      $n.velocity(opts.animIn, 0).css("clip", "auto").addClass("gpSlide-active");
      cb();
      return;
    }

    $c.addClass("gpSlide-out");
    $n.addClass("gpSlide-in").css(opts.cssBefore);
    // alert("nextSlide css" + $n.css("transform"));
    // alert("new Slide got cssBefore");
    

    // alert("current Slide starts Transition");
    $c.velocity(opts.animOut, speed, opts.easing, function() {
      $c.css(opts.cssAfter).removeClass("gpSlide-out");
      //alert("current Slide ended transition - got cssAfter");
      $n.removeClass("gpSlide-in").addClass("gpSlide-active");
      if ( opts.transitionEffect == "fadeOutIn" ){ 
        //alert("new Slide starts transition");
        $n.velocity(opts.animIn, speed, opts.easing, cb); 
      }
    });

    if ( opts.transitionEffect != "fadeOutIn" ){ 
      //alert("new Slide starts transition");
      $n.velocity(opts.animIn, speed, opts.easing, cb);
    }

    //alert("transition done");

    if ( opts.transitionEffect == "wipeH" || opts.transitionEffect == "wipeV" ){ 
      setTimeout( function() {
      $n.css("clip", "auto");
      }, speed+200 );
    }
    if ( opts.transitionEffect == "zoomFade" ){ 
      setTimeout( function() {
      $n.velocity({ scale : 1 }, 10);
      }, speed+200 );
    }
  }; 





  $.fn.gpSlider.transitions = {

    slideH : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        var dir = fwd === true ? 1 : -1;
        $slides.css( "visibility", "hidden");
        $(curr).css( "visibility", "visible");
        $(next).css( "visibility", "visible");
        opts.cssBefore = { left : (100*dir) + "%" };
        opts.cssAfter = {};

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn = { left : "0%" };
        opts.animOut = { left : (-100*dir) + "%" };
      });
    },

    slideV : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        var dir = fwd === true ? 1 : -1;
        $slides.css( "visibility", "hidden");
        $(curr).css( "visibility", "visible");
        $(next).css( "visibility", "visible");
        opts.cssBefore = { top : (100*dir) + "%" };
        opts.cssAfter = {};

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn = { top : "0%" };
        opts.animOut = { top : (-100*dir) + "%" };
      });
    },

    wipeH : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        var w = slideWrapper.outerWidth();
        var h = slideWrapper.outerHeight();
        var cl = fwd === true ? w : 0;
        $slides.css({ "visibility" : "hidden",  "z-index" : 0, clip : "auto" });
        $(curr).css({ "visibility" : "visible", "z-index" : (fwd === true ? 1 : 2) });
        $(next).css({ "visibility" : "visible", "z-index" : (fwd === true ? 2 : 1) });
        opts.cssBefore = { clip : "rect(0px, "+cl+"px, "+h+"px, "+cl+"px)" };
        opts.cssAfter = { clip : "auto", "z-index" : 0  };

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn = { clipTop : "0px", clipRight : w+"px", clipBottom : h+"px", clipLeft : "0px" };
        opts.animOut = { top : 0 };
      });
    },

    wipeV : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        var w = slideWrapper.outerWidth();
        var h = slideWrapper.outerHeight();
        var ct = fwd === true ? h : 0;
        $slides.css({ "visibility" : "hidden",  "z-index" : 0, clip : "auto" });
        $(curr).css({ "visibility" : "visible", "z-index" : (fwd === true ? 1 : 2) });
        $(next).css({ "visibility" : "visible", "z-index" : (fwd === true ? 2 : 1) });
        opts.cssBefore = { clip : "rect("+ct+"px, "+w+"px, "+ct+"px, 0px)" };
        opts.cssAfter = { clip : "auto", "z-index" : 0  };

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn = { clipTop : "0px", clipRight : w+"px", clipBottom : h+"px", clipLeft : "0px" };
        opts.animOut = { top : 0 };
      });
    },


    fadeOutIn : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        $slides.css({ "visibility" : "hidden", "z-index" : 0 });
        $(curr).css({ "visibility" : "visible", "z-index" : 1 });
        $(next).css({ "visibility" : "visible", "z-index" : 2 });
        opts.cssBefore = { opacity : 0 };
        opts.cssAfter = {};

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn = { opacity : 1 };
        opts.animOut = { opacity : 0 };
      });
    },

    crossfade : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        $slides.css({ "visibility" : "hidden",  "z-index" : 0 });
        $(curr).css({ "visibility" : "visible", "z-index" : 2 });
        $(next).css({ "visibility" : "visible", "z-index" : 1 });
        opts.cssBefore = { opacity : 1 };
        opts.cssAfter = { "z-index" : 0 };

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn = { top : 0 };
        opts.animOut = { opacity : 0 };
      });
    },

    zoomFade : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        $slides.css({ "visibility" : "hidden",  "z-index" : 0 });
        $(curr).css({ "visibility" : "visible", "z-index" : 2 });
        $(next).css({ "visibility" : "visible", "z-index" : 1 });
        opts.cssBefore = {};
        opts.cssAfter = { "z-index" : 0 }; 

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn =  { opacity : [1, 1], scale : [1, 1] }; /* needs forcefeeding :-/ */
        opts.animOut = { opacity : [0, 1], scale : [1.25, 1] }; /* needs forcefeeding :-/ */
      });
    },

    pulse : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        $slides.css({ "visibility" : "hidden",  "z-index" : 0 });
        $(curr).css({ "visibility" : "visible", "z-index" : 1 });
        $(next).css({ "visibility" : "visible", "z-index" : 2 });
        opts.cssBefore = {};
        opts.cssAfter = { "z-index" : 0 };

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn =  { opacity : [1, 0], scale : [1, 1.33] };
        opts.animOut = { scale : [1.33, 1], opacity : [0, 1] };
      });
    },

    emerge : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        $slides.css({ "visibility" : "hidden",  "z-index" : 0 });
        $(curr).css({ "visibility" : "visible", "z-index" : 1 });
        $(next).css({ "visibility" : "visible", "z-index" : 2 });
        opts.cssBefore = {};
        opts.cssAfter = {};

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn =  { opacity : [1, 0], scale : [1, 0.25] };
        opts.animOut = { opacity : 1, scale : 1 };
      });
    },

    vanish : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        $slides.css({ "visibility" : "hidden",  "z-index" : 0 });
        $(curr).css({ "visibility" : "visible", "z-index" : 2 });
        $(next).css({ "visibility" : "visible", "z-index" : 1 });
        opts.cssBefore = {};
        opts.cssAfter = { "z-index" : 0 , top : 0 };

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn =  { opacity : [1, 1], top : ["0%", "0%"], scale : [1, 1] };
        opts.animOut = { opacity : [0, 1], top : ["75%", "0%"], scale : [0.25, 1] };
      });
    },

    pushDown : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        $slides.css({ "visibility" : "hidden",  "z-index" : 0 });
        $(curr).css({ "visibility" : "visible", "z-index" : 2 });
        $(next).css({ "visibility" : "visible", "z-index" : 1 });
        var dir = fwd === true ? 1 : -1;
        opts.cssBefore = {};
        opts.cssAfter = {};

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn =  { top : ["0%", (-50*dir)+"%"] };
        opts.animOut = { top : [(100*dir)+"%", "0%"] };
      });
    },

    uncover : function(slideWrapper, $slides, opts) {
      opts.before.push(function(curr, next, opts, fwd) {
        $slides.css({ "visibility" : "hidden",  "z-index" : 0 });
        $(curr).css({ "visibility" : "visible", "z-index" : 2 });
        $(next).css({ "visibility" : "visible", "z-index" : 1 });
        var dir = fwd === true ? -1 : 1;
        opts.cssBefore = {};
        opts.cssAfter = { "z-index" : 0 };

        /* velocity.js (needs non-empty single-value property maps) */
        opts.animIn =  { translateY : [0, 0], translateX : [0, 0], rotateZ : [0, 0] };
        opts.animOut = { translateY : ["25%", "0%"], translateX : [(100*dir)+"%", "0%"], rotateZ : [(12*dir), 0] };
      });
    }

  }; /*  $.fn.gpSlider.transitions --end */


  $.fn.gpSlider.defaults = {
    slideSelector     : ".GPAREA:not(.filetype-slider_controls)",
    transitionEffect  : 'fadeout',
    before            : null,
    after             : null,
    randomStart       : false,
    shuffle           : false,
    showPrevNext      : true,
    showIndicators    : true,
    prev              : ".gpPrevSlide",
    next              : ".gpNextSlide",
    pauseOnHover      : true,
    delay             : 0,
    speed             : 600,
    timeout           : 6000,
    easing            : "swing" 
  };

})(jQuery);



/* 
 * touchswipe support. based on swipe plugin for Cycle2; version: 20121120 
 */

(function($) {
  "use strict";

  var supportTouch = 'ontouchend' in document;

  $.event.special.swipe = $.event.special.swipe || {
    scrollSupressionThreshold: 10,   // More than this horizontal displacement, and we will suppress scrolling.
    durationThreshold: 1000,         // More time than this, and it isn't a swipe.
    horizontalDistanceThreshold: 30, // Swipe horizontal displacement must be more than this.
    verticalDistanceThreshold: 75,   // Swipe vertical displacement must be less than this.

    setup: function() {
      var $this = $( this );

      $this.bind('touchstart', function(event) {
        var data = event.originalEvent.touches ? event.originalEvent.touches[0] : event;
        var stop, start = {
          time: (new Date()).getTime(),
          coords: [data.pageX, data.pageY],
          origin: $(event.target)
        };

        function moveHandler(event) {
          if ( !start ){
            return;
          }

          var data = event.originalEvent.touches ? event.originalEvent.touches[0] : event;

          stop = {
            time : (new Date()).getTime(),
            coords : [data.pageX, data.pageY]
          };

          // prevent scrolling
          if ( Math.abs(start.coords[0] - stop.coords[0]) > $.event.special.swipe.scrollSupressionThreshold ){
            event.preventDefault();
          }
        }

        $this.bind('touchmove', moveHandler)
          .one('touchend', function(event) {
            $this.unbind('touchmove', moveHandler);

            if (start && stop){
              if ( stop.time - start.time < $.event.special.swipe.durationThreshold &&
                  Math.abs( start.coords[ 0 ] - stop.coords[ 0 ] ) > $.event.special.swipe.horizontalDistanceThreshold &&
                  Math.abs( start.coords[ 1 ] - stop.coords[ 1 ] ) < $.event.special.swipe.verticalDistanceThreshold ){

                start.origin.trigger("swipe")
                  .trigger(start.coords[0] > stop.coords[0] ? "swipeleft" : "swiperight");
              }
            }
            start = stop = undefined;
          });
      });
    }
  };

  $.event.special.swipeleft = $.event.special.swipeleft || {
    setup: function() {
      $(this).bind('swipe', $.noop);
    }
  };
  $.event.special.swiperight = $.event.special.swiperight || $.event.special.swipeleft;

})(jQuery);

