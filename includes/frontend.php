<?php
/**
 * Class Frontend.
 */
class CE_Frontend
{
    /**
     * @var CE_SOAPAuth
     */
    private $api;

    /**
     * Form HTML
     *
     * @var string
     */
    private $ce_form_content;

    /**
     * Clever Elements Form Placeholder
     *
     * @var string
     */
    private $ce_form_placeholder;

    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('wp_register_script', array($this, 'cewp_add_css_js'));
        add_action('wp_enqueue_scripts', array($this, 'cewp_add_css_js'));

        add_action('wp_ajax_nopriv_my_ajax', array($this, 'cewp_ajax_callback'));
        add_action('wp_ajax_my_ajax', array($this, 'cewp_ajax_callback'));

        if (isset($_GET['action']) && $_GET['action'] == 'register') {
            if (get_option('cl_wp_pl_api_to_registration') == 1) {
                add_filter('register_form', array($this, 'cewp_add_subscribe_field'));
                add_action('user_register', array($this, 'cewp_send_subscribe_checkbox'));
            }
        } else {
            if (get_option('cl_wp_pl_api_to_comment') == 1) {
                add_filter('comment_form_field_comment', array($this, 'cewp_add_subscribe_field'));
                add_action('comment_post', array($this, 'cewp_send_subscribe_checkbox'));
            }
        }

        add_filter('the_content', array($this, 'cewp_replace_content'));
        add_filter('widget_text', array($this, 'cewp_replace_content'));

        // Set Form Widget HTML
        $form_content = new cl_wp_pl_widget();
        $this->ce_form_content = $form_content->cewp_form_view();
        $this->ce_form_placeholder = $form_content->ce_get_form_placeholder();
    }

    /**
     * Replace content.
     *
     * @param $content
     * @return mixed
     */
    public function cewp_replace_content($content)
    {
        $content = str_replace($this->ce_form_placeholder, $this->ce_form_content, $content);
        return $content;
    }

    /**
     * Add checkbox for subscription.
     *
     * @param $fields
     * @return string
     */
    public function cewp_add_subscribe_field($fields)
    {
        $value = $fields ? 1 : 2;
        $checked = (isset($_POST['cl_wp_pl_api_register_to_api_checkbox']) && $_POST['cl_wp_pl_api_register_to_api_checkbox'] == 2) ? 'checked="checked"' : '';
        $field_html = '<div class="cl_wp_pl_subscribe_checkbox_row">';
        $field_html .= '<input type="checkbox" id="cl_wp_pl_api_register_to_api_checkbox" name="cl_wp_pl_api_register_to_api_checkbox" value="'.$value.'" '.$checked.'>';
        $field_html .= '<label for="cl_wp_pl_api_register_to_api_checkbox">'.get_option('cl_wp_pl_checkbox_title').'</label>';
        $field_html .= '<div class="clear clearfix"></div>';
        $field_html .= '</div>';

        if ($fields) {
            $fields .= $field_html;
            return $fields;
        }

        echo $field_html;
    }

    /**
     * Save subscriber.
     */
    public function cewp_send_subscribe_checkbox($from)
    {
        $checkbox_value = isset($_POST['cl_wp_pl_api_register_to_api_checkbox']) ? $_POST['cl_wp_pl_api_register_to_api_checkbox'] : '';

        if (isset($checkbox_value) && ($checkbox_value == 1 || $checkbox_value == 2)) {
            $user = $this->cewp_get_user_data($_POST, $checkbox_value);
            $this->api_client_init();
            $this->api->cewp_subscribe_to_all($user);
        }
    }

    /**
     * Get user data.
     *
     * @param null $post
     * @param int $from
     * @return array
     */
    public function cewp_get_user_data($post = null, $from = 1)
    {
        $user = array();

        if ($from == 1) {
            $current_user = wp_get_current_user();

            if (!$current_user->ID) {
                $user['email'] = isset($post['email']) ? $post['email'] : '';
                $user['name'] = isset($post['author']) ? $post['author'] : '';
            } else {
                $user['email'] = $current_user->user_email;
                $user['name'] = $current_user->display_name;

                if (isset($user['first_name']) || isset($user['last_name'])) {
                    $user['name'] = trim($current_user->user_firstname.' '.$current_user->user_lastname);
                }
            }
        }

        if ($from == 2) {
            $user['name'] = isset($post['user_login']) ? $post['user_login'] : '';
            $user['email'] = isset($post['user_email']) ? $post['user_email'] : '';
        }

        return $user;
    }

    /**
     * Add JS and CSS.
     */
    public function cewp_add_css_js()
    {
        wp_enqueue_style('cl-style', C_L_PUBLIC_URL.'css/front_end.css', array(), '1.0.0', false);
        wp_enqueue_script('cl-frontend', C_L_PUBLIC_URL.'js/front_end.js', array('jquery'));
        wp_enqueue_script('cl-form-placeholder', C_L_PUBLIC_URL.'js/cl-form-placeholder.js');
        wp_localize_script('cl-form-placeholder', 'cl_form_placeholder_vars', array(
            'ce_wp_form_html' => $this->ce_form_content,
            'ce_wp_form_placeholder' => $this->ce_form_placeholder,
        ));
        wp_localize_script('cl-frontend', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }

    /**
     * AJAX callback.
     */
    public function cewp_ajax_callback()
    {
        if (isset($_POST['data'])) {
            parse_str($_POST['data'], $post);
            $post_user_data = array(
                'name' => isset($post['name']) ? $post['name'] : '',
                'email' => isset($post['email']) ? $post['email'] : '',
            );
            unset($post['name']);
            unset($post['email']);

            $this->api_client_init();
            $this->api->cewp_subscribe_to_form($post_user_data, $post);
            $errors = $this->api->cewp_get_errors();

            if (empty($errors)) {
                echo json_encode(array('status' => 1));
                exit();
            } else {
                $response = array('status' => 0, 'errors' => $errors);
                echo json_encode($response);
                exit();
            }
        }
    }

    /**
     * Initialize API client.
     */
    public function api_client_init()
    {
        $this->api = new CE_SOAPAuth(get_option('cl_wp_pl_user_id'), get_option('cl_wp_pl_api_key'), '1.0');
    }
}

// Instantiate the class
$front_end = new CE_Frontend();

