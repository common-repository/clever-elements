<?php
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$cl_api_class = $GLOBALS['CE_WP_PL_API_CONNECTION_CLASS'];

// Set list columns data
$i = 0;
$list_col1 = '';
$list_col2 = '';
$list_col3 = '';
$no_lists_message = esc_html__('There are no lists available. Please check your connection to the API and check if you have active lists in your Clever Elements account', 'text-domain');

$list_from_database = unserialize(get_option('cl_wp_pl_api_subscriber_lists'));

if ($cl_api_class->cewp_is_connected()) {
    $no_lists_message = '';
    foreach ($cl_api_class->get_user_subscr_list() as $list) {
        $i++;

        $checkbox_id = 'cl_wp_pl_api_subscriber_lists_' . $list->listID . '_checkbox';
        $checkbox_name = 'cl_wp_pl_api_subscriber_lists[' . $list->listID . ']';
        $checkbox_value = $list_from_database[$list->listID];

        $list_item = sprintf(
            '<div>
                <input type="checkbox" id="%s">
                <label for="%s">%s</label>
                <input type="hidden" data-id="%s" name="%s" value="%s">
            </div>',
            esc_attr($checkbox_id),
            esc_attr($checkbox_id),
            esc_html($list->listName),
            esc_attr($checkbox_id),
            esc_attr($checkbox_name),
            esc_attr($checkbox_value)
        );

        if ($i % 3 == 1) {
            $list_col1 .= $list_item;
        } elseif ($i % 3 == 2) {
            $list_col2 .= $list_item;
        } else {
            $list_col3 .= $list_item;
        }
    }
}

?>

<?php include_once C_L_VIEWS_DIR . 'header.php'; ?>

<div class="cl_wp_pl_wrapper">

    <h3><?php esc_html_e('API Connection', 'text-domain'); ?> - <?php echo ($GLOBALS['CE_WP_PL_API_IS_CONNECTED']) ? esc_html__('connected', 'text-domain') : esc_html__('disconnected', 'text-domain'); ?></h3>

    <form action="" method="POST" class="settings_form">
        <div class="checkbox_group">
            <h4><?php esc_html_e('Subscribe Lists', 'text-domain'); ?></h4>
            <?php echo $no_lists_message; ?>
            <div class="checkbox_column"><?php echo $list_col1; ?></div>
            <div class="checkbox_column"><?php echo $list_col2; ?></div>
            <div class="checkbox_column"><?php echo $list_col3; ?></div>
            <div class="clear"></div>
            <div class="publishing-action">
                <a href="admin.php?page=<?php echo esc_attr($_GET['page']); ?>" class="button button-primary menu-save"><?php esc_html_e('Refresh List', 'text-domain'); ?></a>
            </div>

            <div class="clear"></div>
        </div>

        <div class="checkbox_group">
            <h4><?php esc_html_e('Subscribe From', 'text-domain'); ?></h4>

            <div class="checkbox_column">
                <input type="checkbox" id="cl_wp_pl_api_to_comment_checkbox">
                <label for="cl_wp_pl_api_to_comment_checkbox"><?php esc_html_e('Comment', 'text-domain'); ?></label>
                <input type="hidden" data-id="cl_wp_pl_api_to_comment" name="cl_wp_pl_api_to_comment" value="<?php echo esc_attr(get_option('cl_wp_pl_api_to_comment')); ?>">
            </div>
            <div class="checkbox_column">
                <input type="checkbox" id="cl_wp_pl_api_to_registration_checkbox">
                <label for="cl_wp_pl_api_to_registration_checkbox"><?php esc_html_e('Registration Form', 'text-domain'); ?></label>
                <input type="hidden" data-id="cl_wp_pl_api_to_registration" name="cl_wp_pl_api_to_registration" value="<?php echo esc_attr(get_option('cl_wp_pl_api_to_registration')); ?>">
            </div>
            <div class="clear"></div>

            <div class="form_row">
                <h4><?php esc_html_e('Checkbox Title', 'text-domain'); ?></h4>
                <input type="text" class="long" name="cl_wp_pl_checkbox_title" value="<?php echo esc_attr(get_option('cl_wp_pl_checkbox_title')); ?>">
            </div>
        </div>

        <div class="form_row">
            <div class="publishing-action">
                <input type="submit" class="button button-primary menu-save" value="<?php esc_attr_e('Save', 'text-domain'); ?>">
                <div class="clear"></div>
            </div>
        </div>

    </form>
</div>
