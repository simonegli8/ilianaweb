/*
######################################################################
JS/jQuery for Typesetter CMS plugin Back2Top Button - Admin
Author: J. Krausz
Date: 2018-07-14
Version 1.0.2
######################################################################
*/

window.currentScrollTop = $(window).scrollTop();

$(function(){

  $(".gp_Back2Top").on("click", function() {
    $("html, body").stop().animate({ scrollTop : "0px" }, parseInt(bt2_config.scroll_speed));
  });

  // DETECT PAGE SCROLL AND SHOW B2T BUTTON
  $(window).on("load scroll resize", function(e) {
    if ($(window).scrollTop() > bt2_config.scroll_trigger && window.currentScrollTop <= bt2_config.scroll_trigger) {
      window.currentScrollTop = $(window).scrollTop();
      $(".gp_Back2Top").addClass("b2t-show");
      return;
    }
    if ($(window).scrollTop() < bt2_config.scroll_trigger && window.currentScrollTop >= bt2_config.scroll_trigger) {
      window.currentScrollTop = $(window).scrollTop();
      $(".gp_Back2Top").removeClass("b2t-show");
      return;
    }
  });

});
