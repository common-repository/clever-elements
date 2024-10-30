<?php
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$cl_api_class = isset($GLOBALS['CE_WP_PL_API_CONNECTION_CLASS']) ? $GLOBALS['CE_WP_PL_API_CONNECTION_CLASS'] : null;

// set list columns data
$no_lists_message = 'There are no lists available. Please check your connection to the API and check if you have active lists in your Clever Elements account';

$list_from_database = unserialize(get_option('cl_wp_pl_api_subscriber_lists'));

$list_col = '';

if ($cl_api_class && $cl_api_class->cewp_is_connected()) {
    $no_lists_message = '';
    foreach ($cl_api_class->cewp_get_user_subscr_list() as $list) {
        $list_col .= '<legend class="screen-reader-text"><span>'.esc_html($list->listName).'</span></legend>
                            <label for="cl_wp_pl_api_subscriber_lists_'.esc_attr($list->listID).'_checkbox">
                            <input type="checkbox" id="cl_wp_pl_api_subscriber_lists_'.esc_attr($list->listID).'_checkbox"> '.esc_html($list->listName).'</label>
                            <input type="hidden" data-id="cl_wp_pl_api_subscriber_lists_'.esc_attr($list->listID).'" name="cl_wp_pl_api_subscriber_lists['.esc_attr($list->listID).']" value="'.esc_attr($list_from_database[$list->listID]).'"><br/>';
    }
}

?>
<?php include_once esc_html(C_L_VIEWS_DIR.'header.php'); ?>

<div class="cl_wp_pl_wrapper wrap">
    <h3>API connection <?php echo ($GLOBALS['CE_WP_PL_API_IS_CONNECTED']) ? '<span class="status wp-ui-highlight">connected</span>' : '<span class="status wp-ui-notification">disconnected</span>'; ?></h3>

    <p><big>Extend your existing forms with an option for newsletter sign up.</big></p>

    <form action="" method="POST" class="settings_form">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="subscribes-lists">Subscriber lists</label>

                    <p class="description">Select one or more recipient groups to which the new recipient will be
                        registered.</p></th>
                <td>
                    <fieldset>
                        <?php echo $list_col ?>
                        <p><a href="admin.php?page=<?php echo esc_attr($_GET['page']) ?>">Refresh list</a></p>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="subscribes-lists">Double Opt-In</label>

                    <p class="description">Activate or deactivate Double Opt-In.</p></th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span>DOI active</span></legend>
                        <label for="cl_wp_pl_api_doi_is_active_checkbox">
                            <input type="checkbox" id="cl_wp_pl_api_doi_is_active_checkbox">
                            DOI active</label>
                        <input type="hidden" data-id="cl_wp_pl_api_doi_is_active" name="cl_wp_pl_api_doi_is_active"
                               value="<?php echo esc_attr(get_option('cl_wp_pl_api_doi_is_active')) ?>">
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="subscribe-form">Add checkboxes</label>

                    <p class="description">Select where a check box for newsletter registration should be displayed.</p>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span>Comment</span></legend>
                        <label for="cl_wp_pl_api_to_comment_checkbox">
                            <input type="checkbox" id="cl_wp_pl_api_to_comment_checkbox">
                            Comment</label>
                        <input type="hidden" data-id="cl_wp_pl_api_to_comment" name="cl_wp_pl_api_to_comment"
                               value="<?php echo esc_attr(get_option('cl_wp_pl_api_to_comment')) ?>">
                        <br/>
                        <legend class="screen-reader-text"><span>Registration form</span></legend>
                        <label for="cl_wp_pl_api_to_registration_checkbox">
                            <input type="checkbox" id="cl_wp_pl_api_to_registration_checkbox">
                            Wordpress registration form</label>
                        <input type="hidden" data-id="cl_wp_pl_api_to_registration" name="cl_wp_pl_api_to_registration"
                               value="<?php echo esc_attr(get_option('cl_wp_pl_api_to_registration')) ?>">
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="checkbox-title">Checkbox title</label></th>
                <td><input type="text" class="regular-text" name="cl_wp_pl_checkbox_title"
                           value="<?php echo esc_attr(get_option('cl_wp_pl_checkbox_title')) ?>"></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button button-primary menu-save" value="Save">
        </p>
    </form>
</div>
