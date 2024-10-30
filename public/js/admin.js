var $ = jQuery;
$(document).ready(function () {

    if ($('#cl_wp_pl_form_html').length > 0) {
        default_data();
    }

    $.each($('.cl_wp_pl_wrapper input[type="checkbox"]'), function (v, k) {
        check_checkbox_element($(k));
    });

    $('.cl_wp_pl_wrapper input[type="checkbox"]').on('click', function () {
        set_checkbox_val(this);
    })

    $('#cl_el_pl_custom_fields').on('change', function () {

        if($(this).val() == '')
        {
            $('#cl_wp_pl_field_generated').val('');
            return false;
        }

        var html = generate_element($(this).val(), $(this).find('option:selected').text(),
            $(this).find('option:selected').data('fieldType'));
        var title = $(this).find('option:selected').text()

        $('#cl_wp_pl_custom_field_title').val(upper_case_first_letter(title));
        $('#cl_wp_pl_field_generated').val(html);

    });

    $(document).on('click', '#cl_wp_pl_add_to_form_button', function (e) {
        e.preventDefault();
        add_to_form();
        $('#cl_wp_pl_field_generated').val('');
    });

    $('#cl_wp_pl_custom_field_title').on('keyup', function () {
        set_title($(this), false);
    });

    $('#cl_wp_pl_form_save').on('click', function () {
        if (form_validate()) {
            $(this).closest('form').submit();
        }
    });

    // select all in form code readonly field
    $("#ce_form_code_to_paste").click(function () {
        $(this).select();
    });

});

function set_checkbox_val(el) {
    //strip _checkbox and try to find input with same name
    var input_name = $(el).attr('id').replace('_checkbox', '');

    if ($(el).prop('checked')) {
        $('input[data-id="' + input_name + '"]').val('1');
    }
    else {
        $('input[data-id="' + input_name + '"]').val('0');
    }

}

function check_checkbox_element(el) {
    var input_name = $(el).attr('id').replace('_checkbox', '');

    if ($('input[data-id="' + input_name + '"]').val() == 1) {
        $(el).prop('checked', true);
    }
    else {
        $(el).prop('checked', false);
    }
}

function generate_element(api_field_value, api_field_name, api_field_type) {

    var html = '';
    var tab = "\t";

    if (api_field_name == 'Submit') {
        html = '';
        html += '<!-- start form custom field  ' + api_field_name + '-->' + '\n';
        html += tab + '<div class="wp_cl_pl_form_row">' + '\n';
        html += tab + '<input type="submit" value="' + api_field_name + '">' + '\n';
        html += tab + '</div>' + '\n';
        html += '<!-- end form submit field  ' + api_field_name + '-->' + '\n' + '\n';

        return html;
    }

    if (api_field_name == 'Email' ||  api_field_name == 'email') {

        html = '';
        html += '<!-- start form custom field  ' + api_field_name + '-->' + '\n';
        html += tab + '<div class="wp_cl_pl_form_row">' + '\n';
        html += tab + '<label>' + api_field_name + '</label>' + '\n';
        html += tab + '<input type="text" name="email">' + '\n';
        html += tab + '</div>' + '\n';
        html += '<!-- end form email field  ' + api_field_name + '-->' + '\n' + '\n';

        return html;
    }

    var api_field_name = upper_case_first_letter(api_field_name)

    var field = '';
    // bit
    if(api_field_type == 4)
    {
        field += tab + '<select type="text" name="' + api_field_value + '" data-field-type="' + api_field_type + '">' + '\n';
        field += tab + '<option value="">--Select--</option>' + '\n';
        field += tab + '<option value="1">Yes</option>' + '\n';
        field += tab + '<option value="2">No</option>' + '\n';
        field += tab + '</select>' + '\n';
    }
    else if(api_field_type == 3)
    {
        field += tab + '<textarea type="text" name="' + api_field_value + '" data-field-type="' + api_field_type + '"></textarea>' + '\n';
    }
    else
    {
        field += tab + '<input type="text" name="' + api_field_value + '" data-field-type="' + api_field_type + '">' + '\n';
    }

    html = '';
    html += '<!-- start form custom field  ' + api_field_name + '-->' + '\n';
    html += tab + '<div class="wp_cl_pl_form_row">' + '\n';
    html += tab + '<label>' + api_field_name + '</label>' + '\n';
    html += field;
    html += tab + '</div>' + '\n';
    html += '<!-- end form custom field  ' + api_field_name + '-->' + '\n' + '\n';

    return html;
}

function add_to_form() {

    var new_content = set_title($('#cl_wp_pl_custom_field_title'), true);

    if (new_content) {
        $('#cl_el_pl_custom_fields').css('color', '#000000');
        $('#cl_wp_pl_form_html').val($('#cl_wp_pl_form_html').val().concat(new_content));
    }
    else {
        $('#cl_el_pl_custom_fields').css('color', '#ff0000')
    }
}

function upper_case_first_letter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1)
}

function set_title(element, return_it) {

    if ($('#cl_wp_pl_field_generated').val() != '') {
        var generated_text = $('#cl_wp_pl_field_generated').val();

        var label_html = $('label', generated_text).html();
        var input_value = $('input', generated_text).val();

        var new_generated_text = generated_text.replace('<label>' + label_html + '</label>', '<label>' + $(element).val() + '</label>');
        new_generated_text = new_generated_text.replace('<input type="submit" value="' + input_value + '">', '<input type="submit" value="' + $(element).val() + '">');


        if (return_it) {
            return new_generated_text;
        }

        $('#cl_wp_pl_field_generated').val(new_generated_text);
    }
    return false;
}

function default_data() {

    var field = $('#cl_wp_pl_form_html');
    var html = '';

    html += '<!-- start form email address field -->' + '\n';
    html += '<div class="wp_cl_pl_form_row">' + '\n';
    html += '<label>Email address</label>' + '\n';
    html += '<input type="text" name="email">' + '\n';
    html += '</div>' + '\n';
    html += '<!-- end form email address field -->' + '\n' + '\n';

    html += '<!-- start form submit button -->' + '\n';
    html += '<div class="wp_cl_pl_form_row">' + '\n';
    html += '<input type="submit" value="Submit">' + '\n';
    html += '</div>' + '\n';
    html += '<!-- end form submit field -->' + '\n' + '\n';

    if (field.length > 0 && field.val().trim() == '') {

        field.val(html);
        return false
    }

    return true;

}

function form_validate() {
    var field = $('#cl_wp_pl_form_html');
    if (field.val().search('<input type="text" name="email">') == -1) {
        alert('Email field is required');
        return false
    }

    return true
}
