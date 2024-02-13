/*
######################################################################
JS/jQuery for Typesetter CMS plugin Back2Top Button - Admin
Author: J. Krausz
Date: 2018-07-14
Version 1.0.2
######################################################################
*/

var b2t_unsavedChanges = false;

$(function() {

  $(window).bind("beforeunload", function() {
    if ( b2t_unsavedChanges ){
      return "Warning: There are unsaved changes that will be lost. Proceed?";
    }
  });

  $("#b2t_config_form select, #b2t_config_form input").on("change keyup paste", function() {
    b2t_unsavedChanges = true;
  });

  $("#b2t_config_form input[name='save']").on("click", function() {
    b2t_unsavedChanges = false;
  });

  // init botstrap_colorpicker on #bgcolor_input
  $("#b2t_config_form .colorpicker")
  .colorpicker()
  .on('hidePicker.colorpicker', function(event){
    $(this).trigger("keyup");
  });
  
});