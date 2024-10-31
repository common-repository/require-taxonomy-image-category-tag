jQuery(function ($) {
    window.setTimeout( function(){ rtict_handler1();}, 1000 );
});

function rtict_handler1()
{
    if( rtict_object.gutenberg =="yes" )
        jQuery(".edit-post-header__settings .editor-post-publish-button,  .edit-post-header__settings .editor-post-publish-panel__toggle").on("click",   rtict_checkFunction);
    else
        jQuery('#publish, #save-post').on('click', rtict_checkFunction);

    var unshift_event = function(what)
    {
        if (jQuery(what).data('events') != null) {
            var ps_click_events = jQuery(what).data('events').click;
            if (ps_click_events) {
                if (ps_click_events.length > 1) {
                    ps_click_events.unshift(ps_click_events.pop());
                }
            }
        }
    };
    //unshift_event('#publish');
    //unshift_event('#save-post');
}


function rtict_checkFunction(e) {
    rtict_object.limit_error = "";
    rtict_object.missing_error = "";
    var image_empty = rtict_object.fetured_image.has_image == "no";

    // =========== if GutenBerg =========== 
    if( rtict_object.gutenberg =="yes" )
    {
        var P = wp.data.select("core/editor").getCurrentPost();
        var P_edit = wp.data.select("core/editor").getPostEdits();

        jQuery.each(rtict_object.chosen_taxonomies, function (taxonomy, data) 
        {
            var was_empty = data.has_items == 0;
            var selector_base   = wp.data.select("core").getTaxonomy(taxonomy).rest_base;  //data.label
            //var hierarchical    =  data.hierarchical || wp.data.select("core").getTaxonomy(taxonomy).hierarchical;

            //var all_count  =( !P.hasOwnProperty(selector_base) ? 0 : P[selector_base].length ) + ( !P_edit.hasOwnProperty(selector_base) ? 0 : P_edit[selector_base].length );
            var all_count  = (wp.data.select("core/editor").getEditedPostAttribute(selector_base).length );

            if (all_count == 0 )
                rtict_object.missing_error += "\n- " + data.label;  //taxonomy;

            if (all_count  > rtict_object.limits[taxonomy] )
                rtict_object.limit_error += "\n- "  + data.label; // taxonomy;

        });

       // fetured_image
       if(rtict_object.fetured_image.required)
       {
            var selector_base = "featured_media";
            var all_count  =( !P.hasOwnProperty(selector_base) ? 0 : P[selector_base] ) + ( !P_edit.hasOwnProperty(selector_base) ? 0 : P_edit[selector_base]);
            if (all_count==0)
                rtict_object.missing_error += "\n- " + "Featured Image";
       }
    }

    // =========== if not GutenBerg =========== 
    else
    {
        jQuery.each(rtict_object.chosen_taxonomies, function (taxonomy, data) 
        { 
            if (
                (   data.hierarchical     && jQuery('#taxonomy-' + taxonomy + ' input:checked').length == 0 )
                ||
                ( ! data.hierarchical     && jQuery('#tagsdiv-' + taxonomy + ' .tagchecklist li').length ==0 )
            ){
                rtict_object.missing_error += "\n- " + data.label;  //taxonomy;
            }

            if (
                (   data.hierarchical     && jQuery('#' + taxonomy + 'checklist input:checked').length > rtict_object.limits[taxonomy] )
                ||
                ( ! data.hierarchical     && jQuery('#tagsdiv-' + taxonomy + ' .tagchecklist li').length > rtict_object.limits[taxonomy] )
            ){
                rtict_object.limit_error += "\n- "  + data.label; // taxonomy;
            }

        });

       // fetured_image
       if(rtict_object.fetured_image.required)
       {
            var selector_base = "featured_media";
            if (jQuery("#_thumbnail_id").val() == -1 ) {
                rtict_object.missing_error += "\n- " + "Featured Image";
            }
       }
    }



    //if error happened, return
    if(  rtict_object.missing_error != "" ||  rtict_object.limit_error != "" )
    {
        //if (window.hasOwnProperty("rtict_passed_once")) return true;

        if (window.hasOwnProperty("rtict_error_handler") ){
            window.rtict_error_handler(rtict_object);
        }
        else{
            var out = "";
            if( rtict_object.missing_error != "" )
                out +=  rtict_object.required_message.replace("%NAME%", ": " + rtict_object.missing_error.toUpperCase() ) ;
            if( rtict_object.limit_error != "" )
                out +=  "\n\n" + rtict_object.limit_message.replace("%NAME%", ": " + rtict_object.limit_error.toUpperCase() ) ;
            alert( out );
        }
        e.stopImmediatePropagation();
        return false;
    }
    else
    {
        window.rtict_passed_once = true;
    }
}