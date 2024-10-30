var $ = jQuery;
$(document).ready(function () {
    ce_replace_form_placeholder();
});

// get scroll box content and overwrite the data
function ce_replace_form_placeholder(){
    var ce_form_content = cl_form_placeholder_vars.ce_wp_form_html;
    var ce_wp_form_placeholder = cl_form_placeholder_vars.ce_wp_form_placeholder;
    var sbc = $('.stb-content');

    if($(sbc).length > 0){
        var new_content = $(sbc).html().replace(ce_wp_form_placeholder, ce_form_content);
        $(sbc).html(new_content);
    }

}