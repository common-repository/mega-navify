"use strict";
jQuery(document).ready(function($) {

    // Select all accordion items
    var accordionItems = $(".meganavify-accordion-item");

    // Add click event handler to accordion headers
    accordionItems.each(function() {
        var header = $(this).find(".meganavify-accordion-item-header");
        var body = $(this).find(".meganavify-accordion-item-body");

        header.on("click", function() {
            //accordionItems.removerClass("meganavify-collapsed");
            // Collapse other accordion items
            accordionItems.each(function() {
                var accItem = $(this);
                var accBody = accItem.find(".meganavify-accordion-item-body");

                if (!accItem.is($(this)) && !accBody.hasClass("meganavify-collapsed")) {
                    accBody.addClass("meganavify-collapsed").animate({ height: "0" }, 300);
                }
            });

            // Toggle current accordion item
            body.toggleClass("meganavify-collapsed");
            if (body.hasClass("meganavify-collapsed")) {
                body.animate({ height: "0" }, 300);
            } else {
                body.animate({ height: body[0].scrollHeight + "px" }, 300);
            }
        });
    });



    $(document).on('click', '.meganavify-menu-save', function(e) {
        e.preventDefault();

        var _this = $(this);
        var settings = $("[name^='meganavify_options'").serializeArray();
        var formData = {};

        $.each(settings, function(i, field) {
            formData[field.name] = field.value;
        });
        formData['action'] = 'meganavify_save_settings';
        formData['nonce'] = meganavify_object.nonce;
        
        $.ajax({
            type: "POST",
            url: meganavify_object.ajaxurl,
            data: formData,
            beforeSend: function() {
                _this.parent().find('.meganavify-loader').show();
            },
            success: function(response) {
                _this.parent().find('.meganavify-loader').hide();
            }  
        })
    });

    $('.navify-menu-close').on("click", function() {        
        $("#navify-menu-modal").css("display", "none");
       // localStorage.removeItem("navifyGrids");

    });

    // When the user clicks anywhere outside of the modal, close it
    $(window).on("click", function(event) {
        if (event.target === $("#navify-menu-modal")[0]) {
            $("#navify-menu-modal").css("display", "none");
        }
    });
   

    $(document).on('click', '.tablinks', function(e) {
        var target = $(this).data('tab-target');
        
        $('.tabcontent').hide();
        $(target).show();
        $('.tablinks').removeClass('active');
        $(this).addClass('active');
    });

    $('.tablinks.active').trigger('click'); 

    $("#menu-to-edit li.menu-item").each(function() {
        var lebel = meganavify_object.menu_label;
        var menu_item_id =  $(this).find('.menu-item-handle .menu-item-checkbox').data('menu-item-id');       
        var menu_item_title  = $(this).find('.menu-item-handle .menu-item-title').text(); 
        var newElement = $('<span>').addClass('meganavify-manage-menu').text(lebel).attr('data-menu-item-id', menu_item_id).attr('data-menu-item-title', menu_item_title);
        $(this).find('.menu-item-handle .item-title').append(newElement);        
    }); 

    $(document).on('click','.navify-add-location',function(){
        $('#new-menu-location-popup').fadeIn();
    });
    

    $(document).click(function(event) {      
        if (!$(event.target).closest('.new-menu-location-popup-content, .navify-add-location').length) {
            $('#new-menu-location-popup').fadeOut();
        }
    });


    $(document).on('submit','#add_new_menu_location',function(e){
        e.preventDefault();
        var form = $(this);
        $.ajax({
            type: "POST",
            url: meganavify_object.ajaxurl,
            data: form.serialize(), // serializes the form's elements.
            success: function(data){
                window.location.reload();
            }
        });
    });
    
       
    // Close all accordions by default
    $('.accordion-content').hide();

    // Check URL parameter and open the respective accordion
    var urlParam = new URLSearchParams(window.location.search);
    var accordionId = urlParam.get('location');
    
    if (accordionId) {
        $('#' + accordionId).show();
        $('#' + accordionId).find('.accordion-content').slideDown();
    }

    // Click event handler for accordion headers
    $(document).on('click', '.accordion-header', function() {
        var accordionContent = $(this).next('.accordion-content');
        if (accordionContent.is(':hidden')) {
            $('.accordion-content').slideUp();
            $('.accordion-header').removeClass('active');
            accordionContent.slideDown();
            $(this).addClass('active');
        } else {
            accordionContent.slideUp();
            $(this).removeClass('active');
        }
    });    
});