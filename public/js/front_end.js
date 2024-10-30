// add ajax for widget submit
var $ = jQuery;
$(document).ready(function () {

    $(document).on('submit', '.cl_el_widget_from_ajax', function (e) {
        e.preventDefault();
        form_submit($(this));
    })

});

function validate_form(form_fields) {
    var valid = true;
    $.each(form_fields, function (v, k) {

        var label = $(k).closest('.wp_cl_pl_form_row').find('label');

        // prevent errors if there is no label
        if(label.length === 0 ){
            $(k).closest('.wp_cl_pl_form_row').prepend('<label class="js-label-added">Field</label>');
            var label = $(k).closest('.wp_cl_pl_form_row').find('label');
        }

        if ($(k).val().trim() == '') {
            label.html(label.html().replace(' is required', ''));
            label.html(label.html() + ' is required');
            $(label).css('color', '#ff0000');
            valid = false;
        }
        else {
            label.html(label.html().replace(' is required', ''));
            $(label).css('color', '#707070');
            $('.js-label-added').remove();
            // validate by field type
            if ($(k).data('fieldType')) {
                // if integer
                if ($(k).data('fieldType') == 2) {
                    // is not a number
                    if (isNaN(Number($(k).val()))) {
                        label.html(label.html().replace(' needs to be an integer', ''));
                        label.html(label.html() + ' needs to be an integer');
                        $(label).css('color', '#ff0000');
                        valid = false;
                    }
                    else {
                        label.html(label.html().replace(' needs to be an integer', ''));
                        $(label).css('color', '#707070');
                        $('.js-label-added').remove();
                    }
                }
            }

        }


    });

    return valid;
}

function form_submit(form) {
    var form_fields = $('input[type=text], textarea, select', form);
    $('.cl_form_error_message', form).hide();
    if (validate_form(form_fields)) {
        $.post(
            ajax_object.ajax_url,
            {
                'action': 'my_ajax',
                'data': form_fields.serialize().replace('&s=', '')
            },
            function (response) {
                var response = $.parseJSON(response);
                if (response.status == 1) {
                    $('input[type=text]', form).val('');
                    $('.cl_form_message', form).show();
                }
                else {
                    $('.cl_form_error_message', form).show();
                }
            }
        );
    }

}
