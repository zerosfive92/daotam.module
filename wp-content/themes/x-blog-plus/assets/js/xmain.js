(function ($) {
	"use strict";

	   $.fn.xblogAccessibleDropDown = function () {
			    var el = $(this);

			    $("a", el).focus(function() {
			        $(this).parents("li").addClass("hover");
			    }).blur(function() {
			        $(this).parents("li").removeClass("hover");
			    });
		}
    
    //document ready function
    jQuery(document).ready(function($){
    	$("#top-menu").xblogAccessibleDropDown();
    	
        }); // end document ready



}(jQuery));	