<?php
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$cl_api_class = $GLOBALS['CE_WP_PL_API_CONNECTION_CLASS'];

// set list columns data
$i = 0;
$list_col1 = '';
$no_lists_message = 'There are no lists available. Please check your connection to the API and check if you have active lists in your Clever Elements account';
$list_from_database = unserialize(get_option('cl_wp_pl_api_subscriber_lists_form'));

if ($cl_api_class->cewp_is_connected()) {
    $no_lists_message  = '';
    foreach ($cl_api_class->cewp_get_user_subscr_list() as $list) {
        $list_col1 .= '<legend class="screen-reader-text"><span>'.$list->listName.'</span></legend>
                            <label for="cl_wp_pl_api_subscriber_lists_form'.$list->listID.'_checkbox">
                            <input type="checkbox" id="cl_wp_pl_api_subscriber_lists_form'.$list->listID.'_checkbox"> '.$list->listName.'</label>
                            <input type="hidden" data-id="cl_wp_pl_api_subscriber_lists_form'.$list->listID.'" name="cl_wp_pl_api_subscriber_lists_form['.$list->listID.']" value="'.esc_attr($list_from_database[$list->listID]).'"><br/>';
    }
}

$ce_widget = new cl_wp_pl_widget();

?>

<?php include_once C_L_VIEWS_DIR.'header.php'; ?>

<div class="cl_wp_pl_wrapper wrap">
  <h3>API connection <?php echo ($GLOBALS['CE_WP_PL_API_IS_CONNECTED']) ? '<span class="status wp-ui-highlight">connected</span>' : '<span class="status wp-ui-notification">disconnected</span>'; ?></h3>
  <p><big>Generate a form for newsletter sign-ups here, and then integrate the HTML code anywhere.</big></p>
  <form action="" method="POST" class="settings_form forms_form">
    <table class="form-table">
      <tr>
        <th scope="row"><label for="subscribes-lists">Choose Subscriber lists</label><p class="description">Select one or more recipient groups to which the new recipient will be registered.</p></th>
        <td><fieldset>
            <?php echo $list_col1; ?>
            <p><a href="admin.php?page=<?php echo esc_attr($_GET['page']); ?>">Refresh list</a></p>
          </fieldset></td>
      </tr>
        <tr>
            <th scope="row"><label for="subscribes-lists">Double Opt-In</label>

                <p class="description">Activate or deactivate Double Opt-In.</p></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span>DOI active</span></legend>
                    <label for="cl_wp_pl_api_doi_is_active_form_checkbox">
                        <input type="checkbox" id="cl_wp_pl_api_doi_is_active_form_checkbox">
                        DOI active</label>
                    <input type="hidden" data-id="cl_wp_pl_api_doi_is_active_form" name="cl_wp_pl_api_doi_is_active_form"
                           value="<?php echo esc_attr(get_option('cl_wp_pl_api_doi_is_active_form')); ?>">
                </fieldset>
            </td>
        </tr>
    </table>
      <h3 class="title">Customize messages</h3>
      <table class="form-table">
      <tr>
        <th scope="row"><label for="msg-error">Success message</label></th>
        <td><input type="text" name="cl_wp_pl_forms_success_message" value="<?php echo esc_attr(get_option('cl_wp_pl_forms_success_message')); ?>" class="regular-text"></td>
      </tr>
      <tr>
        <th scope="row"><label for="msg-error">Error message</label></th>
        <td><input type="text" name="cl_wp_pl_forms_error_message" value="<?php echo esc_attr(get_option('cl_wp_pl_forms_error_message')); ?>" class="regular-text"></td>
      </tr>
      </table>

<h3 class="title">Compose form HTML</h3>
<p>Assemble the HTML code for the form, and then copy it anywhere into your blog.</p>


    <table class="form-table">
      <tr>
        <th scope="row"><label for="msg-error">Create field</label></th>
        <td><select id="cl_el_pl_custom_fields" name="cl_el_pl_custom_fields">
                    <option value="">Select field</option>
                    <option value="Submit">Submit</option>
                    <option value="Email">Email</option>
                    <?php foreach ($cl_api_class->cewp_get_custom_fields() as $custom_fileds => $custom_filed):?>
                        <option value="<?php echo esc_attr($custom_filed->customFieldID); ?>" data-field-type="<?php echo esc_attr($custom_filed->customFieldType); ?>"><?php echo esc_html($custom_filed->customFieldName); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="clear"></div>
                <p><label for="cl_wp_pl_custom_field_title">Field custom title</label></p>
                <input type="text" name="cl_wp_pl_custom_field_title" id="cl_wp_pl_custom_field_title" class="regular-text">
          <div class="clear"></div>
                <textarea name="cl_wp_pl_field_generated" rows="5" cols="50" id="cl_wp_pl_field_generated" class="code"></textarea>
                <p><a href="" id="cl_wp_pl_add_to_form_button" class="cl_wp_pl_button float-right">Add to form</a></p></td>
      </tr>
      <tr>
        <th scope="row">
            <label for="form-html">Generated form HTML
                    <p class="description">Copy code below & Paste it to display your form on a post, page and text widget: <input id="ce_form_code_to_paste" readonly="" value="<?php echo esc_attr($ce_widget->ce_get_form_placeholder()); ?>"></p>
            </label></th>
        <td><textarea name="cl_wp_pl_form_html" rows="15" cols="50" id="cl_wp_pl_form_html" class="large-text code"><?php echo htmlentities(stripslashes(get_option('cl_wp_pl_form_html'))); ?></textarea></td>
      </tr>
    </table>


    <p class="submit">
      <input type="submit" class="button button-primary menu-save" value="Save">
    </p>

    </form>
</div>
