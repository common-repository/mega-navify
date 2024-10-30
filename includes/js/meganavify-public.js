
"use strict";
jQuery(document).ready(function($) {
    $(document).on('click','#primary-mobile-menu',function(){      
       $(".meganavify-menu-wrap").toggleClass('meganavify-menu-open');
       $(".meganavify-menu-wrap").toggleClass('meganavify-mobile-menu-open');
    });

    $(document).mouseup(function(e) {
        var popup = $(".meganavify-menu-wrap");
        var popupButton = $("#primary-mobile-menu");        
        
        if (!popup.is(e.target) && popup.has(e.target).length === 0 && !popupButton.is(e.target)) {
          popup.removeClass('meganavify-menu-open meganavify-mobile-menu-open'); 
          popupButton.attr('aria-expanded','false')
        }
    });


    function checkScreenResolution() {
        var menu_element = $('.meganavify-menu-wrap');
        if ($(window).width() <= megaNavifyPublicObject.mobile_resolution) {
            menu_element.addClass('mobile-slide');
            $('body').addClass('mega-navify-mobile-menu');
        } else {
            menu_element.removeClass('mobile-slide');
            $('body').removeClass('mega-navify-mobile-menu');
        }
      }      
   
    checkScreenResolution();     
      
    $(window).resize(function() {
        checkScreenResolution();
    });
   
});