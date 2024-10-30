<?php
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}
$cl_api_class = $GLOBALS['CE_WP_PL_API_CONNECTION_CLASS'];
foreach ($cl_api_class->cewp_get_errors() as $error => $msg) {
    echo '<p class="cl_wp_pl_error">'.$msg.'</p>';
}
?>
<?php include_once C_L_VIEWS_DIR.'header.php'; ?>

<div class="cl_wp_pl_wrapper wrap">
  <h3>Api connection <?php echo($GLOBALS['CE_WP_PL_API_IS_CONNECTED']) ? '<span class="status wp-ui-highlight">connected</span>' : '<span class="status wp-ui-notification">disconnected</span>' ?></h3>
  <p><big>Connect your blog now with Clever Elements.<br/>Your API key can be found here: <a href="http://support.cleverelements.com/kb/how-do-i-generate-my-api-key/" target="_blank">How do I generate my API key?</a> (Not registered yet? <a href="http://www.cleverelements.com/_lnd/idaf:38845">Register here!</a>)</big></p>
  <form action="" method="POST" class="api_key">
    <table class="form-table">
      <tr>
        <th scope="row"><label for="user-id">Add your user id</label></th>
        <td><input type="text" name="cl_wp_pl_user_id" class="regular-text" value="<?php echo get_option('cl_wp_pl_user_id') ?>"></td>
      </tr>
      <tr>
        <th scope="row"><label for="api-key">Add your api key</label></th>
        <td><input type="text" name="cl_wp_pl_api_key" class="regular-text" value="<?php echo get_option('cl_wp_pl_api_key') ?>">
        </td>
      </tr>
    </table>
    <p class="description">You are using the official Clever Elements WordPress plugin. If you have questions or need help, please do not hesitate to contact us: <a href="http://support.cleverelements.com/">http://support.cleverelements.com/</a> or <a href="mailto:support@cleverelements.com">support@cleverelements.com</a>.</p>
    <p class="submit">
      <input type="submit" class="button button-primary menu-save" value="Save">
    </p>
  </form>
</div>
