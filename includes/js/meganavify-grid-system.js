
"use strict";
jQuery(document).ready(function($) {
    
    var mediaUploader;

    var add_meganavify_enabled_class = function() {
        if ($("input.menu-item-checkbox:checked") && $("input.menu-item-checkbox:checked").length) {        
            $("body").addClass("meganavify_enabled");
        } else {
            $("body").removeClass("meganavify_enabled");
        }
    }

    $("input.menu-item-checkbox").on("change", function() {
        add_meganavify_enabled_class();
    });

    add_meganavify_enabled_class();

    // When the user clicks on the button, open the modal
    $(document).on('click', '.meganavify-manage-menu', function(e) {
        var menu_item_id = $(this).data('menu-item-id');
        
        if( menu_item_id === undefined ) {
            alert(meganavify_object.save_menu_error);
            return false;
        } 

        if (!$("body").hasClass("meganavify_enabled")) {
            alert(meganavify_object.meganavify_disabled);
            return;
        }
        var menu_item_title = $(this).data('menu-item-title');
        $('.navify-menu-modal-header h2').text(menu_item_title);

        $.ajax({
            type: "POST",
            url: megaNavifyGridObject.ajaxurl,
            data: {
                'action' : 'meganavify_get_active_tab',
                'menu_item_id' : menu_item_id,
                'nonce': megaNavifyGridObject.nonce,
            },
            success: function(response) {       
                
                $("#navify-menu-modal").show().addClass('open-popup');
                $("#menu_item_id").val(menu_item_id); 
                $('.tablinks[data-tab-target="'+response.tab+'"]').addClass('active').trigger('click');
                
                localStorage.removeItem("navifyGrids");
                if(response.item_grid != ''){
                    localStorage.setItem("navifyGrids",response.item_grid);
                }
                
            }  
        });
    }); 


    // Save active tab 
    $(document).on('click', '.navify-model-tab-container .tablinks', function() {
        var menu_item_id = $("#menu_item_id").val();
        var current_tag = $(this).data('tab-target');
       
        
        if( menu_item_id === undefined  ) {
            alert(meganavify_object.save_menu_error);
        }

        var isSubmenu = $('#menu-item-'+menu_item_id).hasClass('menu-item-depth-0') ? false : true;
        
        $.ajax({
            type: "POST",
            url: megaNavifyGridObject.ajaxurl,
            data: {
                'action' : 'meganavify_save_get_tab_content',
                'current_tab' : current_tag,
                'menu_item_id' : menu_item_id,
                'isSubmenu' : isSubmenu,
                'nonce': megaNavifyGridObject.nonce,
            },
            success: function(response) {
                $('.tab_content-result').html(response.html);                
                if( response.active_tab == '#navify-menu'){                    
                    $('#navify_menu_display_mode').val(response.display_mode).trigger('change');
                }  
                if( response.active_tab == '#navify-menu-icons'){                    
                    $('.navify-tab-item.active').trigger('click');
                } 
               
            }  
        });
    });


    $(document).on('change', '#navify_menu_display_mode', function() {

        var displayMode = $(this).val();
        $('.row-button-wrapper,#panel_widgets').hide();
        
        $("#navify_grid_result").html('');
       
        if(displayMode == 'grid'){       
            var navifyGrids =   JSON.parse(localStorage.getItem("navifyGrids"));           
            // If menu item is not have any grid system then create a default grid system
            if( navifyGrids == null ||  navifyGrids == '' ||  navifyGrids == undefined){
                
                var navifyGrids = {
                    'rows': [
                        {
                            'columns': [
                                {
                                    'column_width': 16.66,
                                    'no_of_columns': 2,
                                    'hide_on_mobile' : 0,
                                    'hide_on_desktop' : 0,
                                    'column_items': {} 
                                }
                            ]
                        },
                    ]
                };                
                localStorage.setItem("navifyGrids",JSON.stringify(navifyGrids));
            }
            $('.row-button-wrapper,#panel_widgets').show();
        }
        navfityGetGridSystem();
       
    });
    
    function navfityGetGridSystem() {

        $('.saving-loader').show();
        var formData = {};
        var navifyGrids =  JSON.parse(localStorage.getItem("navifyGrids"));

        formData['action'] = 'meganavify_get_grid_system';
        formData['nonce'] = megaNavifyGridObject.nonce;
        formData['navifyGrids'] = navifyGrids;
        formData['menu_item_id'] = $("#menu_item_id").val();
        formData['display_mode'] = $("#navify_menu_display_mode").val();


        $.ajax({
            type: "POST",
            url: megaNavifyGridObject.ajaxurl,
            data: formData,
            success: function(response) {
                var display_mode = $("#navify_menu_display_mode").val();

                if( display_mode == 'grid' ){
                    $(".meganavify-model-header-container").show();
                    $(".row-button-wrapper").show();
                    $('#navify_grid_result').html(response);
                    $('.navify-model-errors').html('').hide();
                    //Initialization of sortable
                    navifyGridsSortable();                    
                }
                $('.saving-loader').hide();
            }  
        });
    }

    function navifyGridsSortable() {
       $(".navify-grid").sortable({
            items: ".widget",
            cursor: "crosshair",
            opacity: 0.35,
            connectWith: ".navify-grid",
            start: function(event, ui) {
                // Save the original indexes of the item
                ui.item.startRowIndex = ui.item.parents('.navify-grid-container').index();
                ui.item.startColumnIndex = ui.item.parents('.navify-grid').index();
                ui.item.startItemIndex = ui.item.index(); // Save the index of the item being dragged
            },
            stop: function(event, ui) {
                var navifyGrids = JSON.parse(localStorage.getItem("navifyGrids"));

                // Get the original index of the widget, its parent column, and its grandparent row
                var startWidgetIndex = ui.item.startItemIndex;
                var startColumnIndex = ui.item.startColumnIndex;
                var startRowIndex = ui.item.startRowIndex;             

                // Get the new index of the widget, its parent column, and its grandparent row
                var newWidgetIndex = ui.item.index();
                var newColumnIndex = ui.item.parents('.navify-grid').index();
                var newRowIndex = ui.item.parents('.navify-grid-container').index();

                // Check if the column_items array exists in the start column
                if (!navifyGrids['rows'][startRowIndex]['columns'][startColumnIndex]['column_items']) {
                    navifyGrids['rows'][startRowIndex]['columns'][startColumnIndex]['column_items'] = [];
                }

                // Get the widget that was moved
                var movedWidget = navifyGrids['rows'][startRowIndex]['columns'][startColumnIndex]['column_items'].splice(startWidgetIndex, 1)[0];

                // Check if the column_items array exists in the new column
                if (!navifyGrids['rows'][newRowIndex]['columns'][newColumnIndex]['column_items']) {
                    navifyGrids['rows'][newRowIndex]['columns'][newColumnIndex]['column_items'] = [];
                }

                // Check if column_items is an array before calling splice
                if (Array.isArray(navifyGrids['rows'][newRowIndex]['columns'][newColumnIndex]['column_items'])) {
                    // Insert the moved widget at its new position
                    navifyGrids['rows'][newRowIndex]['columns'][newColumnIndex]['column_items'].splice(newWidgetIndex, 0, movedWidget);
                } else {
                    // If column_items is not an array, create it as an array with the moved widget as its first item
                    navifyGrids['rows'][newRowIndex]['columns'][newColumnIndex]['column_items'] = [movedWidget];
                }

                // Update the localStorage
                localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
                navfityGetGridSystem();
            }
        });

        $(".navify-grid-container").sortable({
            items: ".navify-grid",
            handle: ".drag-drop-column",
            cursor: "crosshair",
            opacity: 0.35,
            connectWith: $(this).parents('.navify-grid-container'),
            start: function(event, ui) {
                // Save the original index of the column
                ui.item.startRowIndex = ui.item.parents('.navify-grid-container').index();
                ui.item.startColumnIndex = ui.item.index();
            },
            update: function(event, ui) {
                var navifyGrids = JSON.parse(localStorage.getItem("navifyGrids"));
                var startRowIndex = ui.item.startRowIndex;
        
                var movedColumn = navifyGrids['rows'][startRowIndex]['columns'].splice(ui.item.startColumnIndex, 1)[0];
                navifyGrids['rows'][ui.item.parents('.navify-grid-container').index()]['columns'].splice(ui.item.index(), 0, movedColumn);
        
                localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
                navfityGetGridSystem();
            },
        });
     
        $("#navify_grid_result").sortable({
            items: ".navify-grid-container",
            handle: ".drag-drop-row",
            cursor: "crosshair",
            opacity: 0.35,
            start: function(event, ui) {
                // Save the original index of the row
                ui.item.startRowIndex = ui.item.index();
            },
            update: function(event, ui) {
                var navifyGrids = JSON.parse(localStorage.getItem("navifyGrids"));
                var movedRow = navifyGrids['rows'].splice(ui.item.startRowIndex, 1)[0];
                navifyGrids['rows'].splice(ui.item.index(), 0, movedRow);
        
                console.log(navifyGrids)
                localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
                navfityGetGridSystem();
            },
        });

    }
    // Call the function to make the grid sortable
    navifyGridsSortable();
   
    //Add new row
    $(document).on('click', '.navify-add-row', function() {
        var navifyGrids =  JSON.parse(localStorage.getItem("navifyGrids"));  
        var NavfyBlankRowWithOneColumn = {
            'columns': [
                {
                    'column_width': 16.66,
                    'no_of_columns': 2,
                    'hide_on_mobile' : 0,
                    'hide_on_desktop' : 0,
                    'column_items': {} 
                }
            ]
        };
        navifyGrids['rows'].push(NavfyBlankRowWithOneColumn);
        
        localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
        navfityGetGridSystem();
    });

    // Open row settings
    $(document).on('click', '.add-row-classs-toggle', function() {
        var columWrap = $(this).parents('.navify-grid'); 
        $(this).toggleClass('open');               
        if( columWrap.length > 0 ){
            columWrap.find('.setting-wrap').slideToggle();
        }
        else{
            var rowWrap = $(this).parents('.navify-grid-container');
            rowWrap.find('.setting-wrap.row-settings').slideToggle().toggleClass('open');
        }
    });

    // Update row and column settings
   $(document).on('click', '.add-class-btn', function() {
        var navifyGrids = JSON.parse(localStorage.getItem("navifyGrids")) || {};  

        var cssClass = $(this).parents('.setting-wrap').find('input').val();
        var parentRow = $(this).parents('.navify-grid-container');
        var rowIndex = parseInt(parentRow.data('row'));

        // Ensure rows is an array and the specific row exists
        if (!Array.isArray(navifyGrids['rows'])) {
            navifyGrids['rows'] = [];
        }
        if (!navifyGrids['rows'][rowIndex]) {
            navifyGrids['rows'][rowIndex] = {};
        }

        // Add css_class to the row
        navifyGrids['rows'][rowIndex]['css_class'] = cssClass;

        if ($(this).hasClass('column')) {
            var parentColumn = $(this).parents('.navify-grid');
            var columnIndex = parseInt(parentColumn.data('column-index'));

            // Ensure columns is an array and the specific column exists
            if (!Array.isArray(navifyGrids['rows'][rowIndex]['columns'])) {
                navifyGrids['rows'][rowIndex]['columns'] = [];
            }
            if (!navifyGrids['rows'][rowIndex]['columns'][columnIndex]) {
                navifyGrids['rows'][rowIndex]['columns'][columnIndex] = {};
            }

            // Add css_class to the column
            navifyGrids['rows'][rowIndex]['columns'][columnIndex]['css_class'] = cssClass;
        }

        // Update the localStorage
        localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
        navfityGetGridSystem();
    });


    // Duplicate row
    $(document).on('click', '.duplicate-row', function() {
        var navifyGrids = JSON.parse(localStorage.getItem("navifyGrids"));  

        var parentRow = $(this).parents('.navify-grid-container');
        var rowIndex = parseInt(parentRow.data('row'));

        // Ensure the row to be duplicated exists
        if (navifyGrids['rows'] && navifyGrids['rows'][rowIndex]) {
            var rowToDuplicate = navifyGrids['rows'][rowIndex];

            // Insert the duplicated row next to the current row
            navifyGrids['rows'].splice(rowIndex + 1, 0, rowToDuplicate);

            // Update the localStorage
            localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
            navfityGetGridSystem();
        }
    });

    // Adde new column
    $(document).on('change', '.navify-add-colum', function() {
        var col_no = parseInt($(this).val());

        if( col_no == '' ){
            return;
        }
        
        var navifyGrids =  JSON.parse(localStorage.getItem("navifyGrids"));  
        var NavfyBlankColumn =  {
            'column_width': 8.33 * parseInt(col_no),
            'no_of_columns': col_no,
            'hide_on_mobile' : 0,
            'hide_on_desktop' : 0

        };

        var parentRow = $(this).parents('.navify-grid-container');
        var rowIndex = parentRow.data('row');
        rowIndex = parseInt(rowIndex); 
        var errorWrapper = parentRow.find('.row-error-message');

        var rowColumnCounts = parentRow.data('column-count');
        rowColumnCounts = parseInt(rowColumnCounts);

        var totalColumns = navifyGrids['rows'][rowIndex]['columns'].reduce(function(sum, column) {
            return sum + parseInt(column.no_of_columns);
        }, 0);

        if( totalColumns + col_no  <= 12 ){

             // Check if the row exists in the array
            if (navifyGrids['rows'][rowIndex]['columns']) {
                // Add the new column to the specific row
                navifyGrids['rows'][rowIndex]['columns'].push(NavfyBlankColumn);
            }
            else {
                errorWrapper.html(megaNavifyGridObject.row_not_exits).show();
            }
        }
        else{
            errorWrapper.html(megaNavifyGridObject.column_limit_reached).show();
            return false;
        }        
        localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
        navfityGetGridSystem();
    });


    $(document).on('click', '.duplicate-column', function() {
        var navifyGrids = JSON.parse(localStorage.getItem("navifyGrids"));  

        var parentRow = $(this).parents('.navify-grid-container');
        var rowIndex = parseInt(parentRow.data('row'));

        var parentColumn = $(this).parents('.navify-grid');
        var columnIndex = parseInt(parentColumn.data('column-index'));
        var errorWrapper = parentRow.find('.row-error-message');

        // Ensure the column to be duplicated exists
        if (navifyGrids['rows'] && navifyGrids['rows'][rowIndex] && navifyGrids['rows'][rowIndex]['columns'] && navifyGrids['rows'][rowIndex]['columns'][columnIndex]) {
            var columnToDuplicate = navifyGrids['rows'][rowIndex]['columns'][columnIndex];
            var upcomingColumnCount = parseInt(columnToDuplicate.no_of_columns);

            var totalColumns = navifyGrids['rows'][rowIndex]['columns'].reduce(function(sum, column) {
                return sum + parseInt(column.no_of_columns);
            }, 0);
            
            // Ensure the total number of columns does not exceed 12
            if (upcomingColumnCount + totalColumns <= 12) {
                // Insert the duplicated column next to the current column
                navifyGrids['rows'][rowIndex]['columns'].splice(columnIndex + 1, 0, columnToDuplicate);

                // Update the localStorage
                localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
                navfityGetGridSystem();
            } else {
                errorWrapper.html(megaNavifyGridObject.column_limit_reached).show();
                
            }
        }
    });

    $(document).on('click','.column-show-desktop,.column-show-smartphone',function(){
        var navifyGrids = JSON.parse(localStorage.getItem("navifyGrids"));  

        var parentRow = $(this).parents('.navify-grid-container');
        var rowIndex = parseInt(parentRow.data('row'));

        var parentColumn = $(this).parents('.navify-grid');
        var columnIndex = parseInt(parentColumn.data('column-index'));

        if( $(this).hasClass('column-show-desktop')){
            if( navifyGrids['rows'][rowIndex]['columns'][columnIndex].hide_on_desktop == '1'){
                navifyGrids['rows'][rowIndex]['columns'][columnIndex].hide_on_desktop = 0;
            }
            else{
                navifyGrids['rows'][rowIndex]['columns'][columnIndex].hide_on_desktop = 1;
            }
            
        }

        if( $(this).hasClass('column-show-smartphone')){
            if(  navifyGrids['rows'][rowIndex]['columns'][columnIndex].hide_on_mobile == '1'){
                navifyGrids['rows'][rowIndex]['columns'][columnIndex].hide_on_mobile = 0;
            }
            else{
                navifyGrids['rows'][rowIndex]['columns'][columnIndex].hide_on_mobile = 1;
            }
        }
        // Update the localStorage
        localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
        navfityGetGridSystem();
        
    });

    $(document).on('click','.row-show-desktop,.row-show-smartphone',function(){
        var navifyGrids = JSON.parse(localStorage.getItem("navifyGrids"));  

        var parentRow = $(this).parents('.navify-grid-container');
        var rowIndex = parseInt(parentRow.data('row'));

        var parentColumn = $(this).parents('.navify-grid');
        var columnIndex = parseInt(parentColumn.data('column-index'));

        if( $(this).hasClass('row-show-desktop')){
            if( navifyGrids['rows'][rowIndex].hide_on_desktop == '1'){
                navifyGrids['rows'][rowIndex].hide_on_desktop = 0;
            }
            else{
                navifyGrids['rows'][rowIndex].hide_on_desktop = 1;
            }
            
        }

        if( $(this).hasClass('row-show-smartphone')){
            if(  navifyGrids['rows'][rowIndex].hide_on_mobile == '1'){
                navifyGrids['rows'][rowIndex].hide_on_mobile = 0;
            }
            else{
                navifyGrids['rows'][rowIndex].hide_on_mobile = 1;
            }
        }
        
        // Update the localStorage
        localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
        navfityGetGridSystem();
    });

    //Increse grid column width
    $(document).on('click', '.manage-col-width', function() {
        var navifyGrids =  JSON.parse(localStorage.getItem("navifyGrids"));  

        var parentRow = $(this).parents('.navify-grid-container');
        var rowIndex = parentRow.data('row');
        rowIndex = parseInt(rowIndex);
        var rowColumnCounts = parentRow.data('column-count');
        rowColumnCounts = parseInt(rowColumnCounts);
        
        var parentColumn = $(this).parents('.navify-grid');
        var ColumnIndex = parentColumn.data('column-index');
        ColumnIndex = parseInt(ColumnIndex);

        var errorWrapper = parentRow.find('.row-error-message');
        
        if (navifyGrids['rows'][rowIndex]['columns'][ColumnIndex]) {

            var no_of_columns =navifyGrids['rows'][rowIndex]['columns'][ColumnIndex].no_of_columns;
            var column_width =navifyGrids['rows'][rowIndex]['columns'][ColumnIndex].column_width;
            if( $(this).hasClass('increase') ) {
                //  Validate the column count is not more than 12
                if( rowColumnCounts >= 12 ){
                    errorWrapper.html(megaNavifyGridObject.column_limit_reached).show();
                    return false;
                }
                var updated_column_width = parseFloat(column_width) + 8.33;
                var updated_no_of_calumn = parseInt(no_of_columns) + 1;
                navifyGrids['rows'][rowIndex]['columns'][ColumnIndex].no_of_columns = updated_no_of_calumn;
                navifyGrids['rows'][rowIndex]['columns'][ColumnIndex].column_width = updated_column_width;
            }
            else{
                //  Validate the column count is not less than 1
                if( no_of_columns <= 1 ){
                    errorWrapper.html(megaNavifyGridObject.min_column_reached).show();
                    return false;
                }

                var updated_column_width = parseFloat(column_width) - 8.33;
                var updated_no_of_calumn = parseInt(no_of_columns) - 1;

                navifyGrids['rows'][rowIndex]['columns'][ColumnIndex].no_of_columns = updated_no_of_calumn;
                navifyGrids['rows'][rowIndex]['columns'][ColumnIndex].column_width = updated_column_width;
            }
            localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
            navfityGetGridSystem();
        }
    });

    // Remove column from row
    $(document).on('click', '.remove-column', function() {
        var navifyGrids =  JSON.parse(localStorage.getItem("navifyGrids"));  

        if(!confirm(megaNavifyGridObject.confirm_delete_column)){
            return false;
        }

        var parentRow = $(this).parents('.navify-grid-container');
        var rowIndex = parentRow.data('row');
        rowIndex = parseInt(rowIndex);

        var parentColumn = $(this).parents('.navify-grid');
        var ColumnIndex = parentColumn.data('column-index');
        ColumnIndex = parseInt(ColumnIndex);

        var errorWrapper = parentRow.find('.row-error-message');
        
        if (navifyGrids['rows'][rowIndex]['columns'][ColumnIndex]) {
            navifyGrids['rows'][rowIndex]['columns'].splice(ColumnIndex, 1);
        }
        else {
            errorWrapper.html(megaNavifyGridObject.column_limit_reached).show();
        }

        localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
        navfityGetGridSystem();
    });

    // Remove row from grid
    $(document).on('click', '.remove-row', function() {
        var navifyGrids =  JSON.parse(localStorage.getItem("navifyGrids"));  

        if(!confirm(megaNavifyGridObject.confirm_delete_row)){
            return false;
        }
        var parentRow = $(this).parents('.navify-grid-container');
        var rowIndex = parentRow.data('row');
        rowIndex = parseInt(rowIndex);

        var errorWrapper = parentRow.find('.row-error-message');

        if (navifyGrids['rows'][rowIndex]) {
            navifyGrids['rows'].splice(rowIndex, 1);
        }
        else {
            errorWrapper.html(megaNavifyGridObject.row_not_exits).show();
        }
        
        localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
        navfityGetGridSystem();
    });

    // Add widget to column    
    $(document).on('change', '#panel_widgets', function() {
        var widget = $(this).val();
        var title = $("#panel_widgets").find("option:selected").text();
        var menu_item_id = $("#menu_item_id").val();
        
        var data = {
            'action': 'meganavify_add_column_widget',
            'widget': widget,
            'title': title,
            'menu_item_id' : menu_item_id,
            'nonce': megaNavifyGridObject.nonce,
        };

        $.ajax({
            type: "POST",
            url: megaNavifyGridObject.ajaxurl,
            data: data,
            success: function(response) {
                $("#panel_widgets").val('');
               response = $(response);
                $(".navify-grid:first .column-contant").append(response);

                var navifyGrids =  JSON.parse(localStorage.getItem("navifyGrids"));
                var item = {
                    'type' : 'widget',
                    'widget_id' :response.data('id'),
                };

                if (navifyGrids['rows'][0] && navifyGrids['rows'][0]['columns'][0]) {
                    if (!Array.isArray(navifyGrids['rows'][0]['columns'][0]['column_items'])) {
                      navifyGrids['rows'][0]['columns'][0]['column_items'] = [];
                    }
                    navifyGrids['rows'][0]['columns'][0]['column_items'].push(item);
                  } else {
                    console.error('Target column does not exist');
                  }

                localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
                
                navfityGetGridSystem();
            }  
        });
    });
        
    // Edit widget
    $(document).on("click", ".widget .widget-action", function() {

        var action = "meganavify_edit_column_widget";
        var widget = $(this).closest(".widget");
        var widget_title = widget.find(".widget-title");
        var widget_inner = widget.find(".widget-inner");
        var id = widget.attr("id");       

        if (!widget.hasClass("open") && !widget.data("loaded")) {
            widget_title.addClass("loading");           
            $.post(ajaxurl, {
                action: action,
                widget_id: id,
                nonce: megaNavifyGridObject.nonce
            }, function(response) {
                var $response = $(response);
                widget_inner.html($response);
                widget.data("loaded", true).toggleClass("open");
                widget_title.removeClass("loading");

                // Init Black Studio TinyMCE
                if (widget.is('[id*=black-studio-tinymce]')) {
                    bstw(widget).deactivate().activate();
                }
                var widgetActionSpan = widget.find('.widget-action span');
                if (widget.hasClass("open")) {
                widgetActionSpan.removeClass('dashicons-admin-tools').addClass('dashicons-arrow-up');
                } else {
                widgetActionSpan.removeClass('dashicons-arrow-up').addClass('dashicons-admin-tools');
                }
                setTimeout(function(){
                    if (wp.textWidgets !== undefined) {
                        wp.textWidgets.widgetControls = {};
                    }
                    if (wp.mediaWidgets !== undefined) {
                        wp.mediaWidgets.widgetControls = {};
                    }
                    if (wp.customHtmlWidgets !== undefined) {
                        wp.customHtmlWidgets.widgetControls = {};
                    }
                    $(document).trigger("widget-added", [widget]);
                },100);
            });

        } else {
            widget.toggleClass("open");
            var widgetActionSpan = widget.find('.widget-action span');
            if (widget.hasClass("open")) {
            widgetActionSpan.removeClass('dashicons-admin-tools').addClass('dashicons-arrow-up');
            } else {
            widgetActionSpan.removeClass('dashicons-arrow-up').addClass('dashicons-admin-tools');
            }
        }      
        
        // close all other widgets
        $(".widget").not(widget).removeClass("open");

    });

    // Delete widget
    $(document).on('click', '.widget-controls .delete', function(e) {
        e.preventDefault();
        var navifyGrids =  JSON.parse(localStorage.getItem("navifyGrids"));

        var widget = $(this).parents('.widget');
        var itemIndex = widget.data('item-index');
        itemIndex = parseInt(itemIndex);
        var columnIndex = widget.parents('.navify-grid').data('column-index');
        columnIndex = parseInt(columnIndex);
        var rowIndex = widget.parents('.navify-grid-container').data('row');     
        rowIndex = parseInt(rowIndex);   

        if (navifyGrids['rows'][rowIndex]['columns'][columnIndex]['column_items'][itemIndex]) {
            navifyGrids['rows'][rowIndex]['columns'][columnIndex]['column_items'].splice(itemIndex, 1);
        }

        localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
        navfityGetGridSystem();        
    });

    //Close widget
    $(document).on('click', '.widget-controls .close', function(e) {
        e.preventDefault();
        $(this).parents('.widget').removeClass('open');
    });
    
    //Save widget
    $(document).on('click', '.save-column-widget', function() {
        $(this).parents('.widget-form').trigger('submit');
    });

    // Append widget title  
    $(document).on('keyup', 'input[id*="-title"]', function() {

        var title = $(this).val() || '';

        console.log("title" + title );
        if ( title ) {
            title = title.replace(/<[^<>]+>/g, '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }        
        $(this).parents('.widget').find('.widget-title h4').html(title);
    });
    

    // Save widget
    $(document).on('submit', '.mega-navify-widget-form', function() {

        var navifyGrids =  JSON.parse(localStorage.getItem("navifyGrids"));  

        var parentRow = $(this).parents('.navify-grid-container');
        var rowIndex = parentRow.data('row');
        rowIndex = parseInt(rowIndex);
        var rowColumnCounts = parentRow.data('column-count');
        rowColumnCounts = parseInt(rowColumnCounts);
        
        var parentColumn = $(this).parents('.navify-grid');
        var ColumnIndex = parentColumn.data('column-index');
        ColumnIndex = parseInt(ColumnIndex);   
        
        var widget_id = $(this).find(".widget-id").val();
        var itemIndex = $(this).parent().data('item-key');
        
        var data = $(this).serializeArray(); 
        
        $.ajax({
            type: "POST",
            url: megaNavifyGridObject.ajaxurl,
            data: data,
            success: function(response) {
                if(response.status == 'success'){               
                    // if( itemIndex === undefined  || itemIndex === '' || itemIndex === null || itemIndex == '0' ){
                
                    //     if (!Array.isArray(navifyGrids['rows'][rowIndex]['columns'][ColumnIndex]['column_items'])) {
                    //         navifyGrids['rows'][rowIndex]['columns'][ColumnIndex]['column_items'] = [];
                    //     }

                    //     var widget_items = {
                    //         'type' : 'widget',
                    //         'widget_id' : widget_id,
                    //     };

                    //     navifyGrids['rows'][rowIndex]['columns'][ColumnIndex]['column_items'].push(widget_items);                   

                    //     localStorage.setItem("navifyGrids", JSON.stringify(navifyGrids));
                    //     navfityGetGridSystem();
                    // }
                }
                else{
                    alert(response.message);
                }
            }
        });
        
        return false;
    });
    

    // Save general settings
    $(document).on('submit', '#navify_menu_settings_form', function(e) {    
        e.preventDefault();
        $('.saving-loader').show();
        var formData    = $(this).serialize();
        var action      = $(this).find('input[name="action"]').val();
        var nounce      = $(this).find('input[name="_wpnonce"]').val();
        $('.navify-menu-settings-form-message').html('').removeClass('error').removeClass('updated');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: megaNavifyGridObject.ajaxurl,
            data: {
                'action': action, // the PHP function to run
                'form_data': formData,
                'nounce': nounce,
            },
            success: function(response) {              
                $('.navify-menu-settings-form-message').addClass(response.status).html(response.message);
                $('.saving-loader').hide();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
   
    // Save menu item icon
    $(document).on('change', '.navify-menu-icon', function(e) {    
        
        var item_icon = $(this).val();
        var nonce = megaNavifyGridObject.nonce;
        var menu_item_id = $('#menu_item_id').val();
        var icon_type = $('.navify-tab-item.active').data('tab');

        var data = {           
            'action': 'meganavify_update_menu_icon', // the PHP function to run
            'item_icon' : item_icon,
            'icon_type' : icon_type,
            'menu_item_id': menu_item_id,
            'nonce': nonce
        };
        meganavify_update_menu_icon(data);       
    });


    function meganavify_update_menu_icon(data){
        $('.saving-loader').show();
        $('.navify-menu-settings-form-message').html('').removeClass('error').removeClass('updated');

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: megaNavifyGridObject.ajaxurl,
            data: data ,
            success: function(response) {
                $('.saving-loader').hide();
            },
        });
    }

    // Search for the icons
    $(document).on('keyup','#icon-search',function(){
        var search = $(this).val();        
        $('.navify-icon-wrap .navify-icon-item ').each(function() {
           var text = $(this).find('input').val().toLowerCase();
            if (text.includes(search)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });


    $(document).on('click','.navify-tab-item',function(){
        var tabId = $(this).data('tab');
        var menu_item_id = $("#menu_item_id").val();
        $('.navify-tab-item').removeClass('active');
        $('.navify-tab-content').removeClass('active');
        $(this).addClass('active');
        $('#' + tabId).addClass('active');        
        
        var nonce = megaNavifyGridObject.nonce;       
        $.ajax({
            type: 'POST',
            url: megaNavifyGridObject.ajaxurl,
            data: {
                'action': 'meganavify_get_lib_icons', // the PHP function to run
                'icon_type' : tabId,
                'menu_item_id' : menu_item_id,
                'nonce': nonce,
            },
            success: function(response) { 
                $('#icon-search').hide();
                if( tabId != 'custom-icon'){
                    $('#icon-search').show();
                }
                $('.navify-icon-tab-content').html(response)
            },
        });
    });
     
    $(document).on('click','#custom-icon-upload-button',function(e){        
        e.preventDefault();

        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create the media uploader object
        mediaUploader = wp.media({
            title: megaNavifyGridObject.select_upload_icon,
            button: {
                text: megaNavifyGridObject.use_this_icon
            },
            multiple: false // Set to true if you want to allow multiple files to be selected
        });

        // When a file is selected, grab the URL and set it as the text field's value
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            
            $('.navify-custom-icon-wrapper img').attr('src',attachment.url)
            
            var item_icon = attachment.id;
            var nonce = megaNavifyGridObject.nonce;
            var menu_item_id = $('#menu_item_id').val();
            var icon_type = $('.navify-tab-item.active').data('tab');
            
            var data = {
                'action': 'meganavify_update_menu_icon', // the PHP function to run
                'item_icon' : item_icon,
                'icon_type' : icon_type,
                'menu_item_id': menu_item_id,
                'nonce': nonce
            };

            meganavify_update_menu_icon(data);
            setTimeout(() => {
                $('.navify-icon-tab-wrap .navify-tab-item.active').trigger('click')
            }, 500);
        });

        // Open the uploader dialog
        mediaUploader.open();
    });  


    $(document).on('click','.remove-custom-icon',function(){
        var iconIndex = $(this).data('custom-icon-index');

        var nonce = megaNavifyGridObject.nonce;       
        $.ajax({
            type: 'POST',
            url: megaNavifyGridObject.ajaxurl,
            data: {
                'action': 'meganavify_remove_custom_icon', // the PHP function to run
                'iconIndex' : iconIndex,
                'nonce': nonce,
            },
            success: function(response) { 
                setTimeout(() => {
                    $('.navify-tab-item.active').trigger('click')
                }, 200);
            },
        });
    });
    
});
