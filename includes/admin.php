<?php


/**
 * Class CleverElementsAdmin.
 */
class CleverElementsAdmin
{
    /**
     * @var
     */
    private $form_messages;

    /**
     * set permissions of user how can access functionality.
     *
     * @var array
     */
    public static $permissions = array(
            'admin' => 'administrator',
    );

    /**
     * add option for changing names of menus items, view title etc.
     *
     * @var array
     */
    public static $printables = array(
        //main menu item
            'admin_menu_title'                       => 'Clever Elements',
            'admin_menu_page_title'                  => 'Clever Elements',
            'admin_page_slug'                        => 'clever-elements',
            'admin_page_icon_url'                    => 'ce/public/images/ce_icon.png',
        // settings array key 1
            array(
                    'sub_menu_item_title' => 'Checkboxes',
                    'sub_item_page_title' => 'Clever elements - Checkboxes',
                    'sub_page_slug'       => 'clever-elements-settings',
            ),
        // forms array key 2
            array(
                    'sub_menu_item_title' => 'My Forms',
                    'sub_item_page_title' => 'Clever elements - Forms',
                    'sub_page_slug'       => 'clever-elements-forms',
            ),
        //default checkbox title
            'default_checkbox_subscribe_title'       => 'Subscribe to newsletter',
            'default_cl_wp_pl_forms_success_message' => 'You are subscribed',
            'default_cl_wp_pl_forms_error_message'   => 'Error occurred',

    );

    /**
     *  call what needed on load.
     */
    public function __construct()
    {

        // wp add action
        add_action('admin_menu', array(
                $this,
                'cewp_admin_menu',
        ));

        //add styles and javascript
        add_action('admin_enqueue_scripts', array(
                $this,
                'admin_css_js',
        ));

        // call clever elements api and use if from globals
        $api = new CE_SOAPAuth(get_option('cl_wp_pl_user_id'), get_option('cl_wp_pl_api_key'), '1.0');

        $GLOBALS['CE_WP_PL_API_CONNECTION_CLASS'] = $api;
        $GLOBALS['CE_WP_PL_API_IS_CONNECTED'] = $api->cewp_is_connected();
        // set up default otpions
        $this->cewp_default_options();
    }

    /**
     * set up admin menu.
     */
    public function cewp_admin_menu()
    {
        // menu items for admin
        $menu_items = array(

            // settings
                array(
                        'parent_page_slug' => static::$printables['admin_page_slug'],
                        'menu_page_title'  => static::$printables[0]['sub_item_page_title'],
                        'menu_text'        => static::$printables[0]['sub_menu_item_title'],
                        'permissions'      => static::$permissions['admin'],
                        'page_slug'        => static::$printables[0]['sub_page_slug'],
                        'function'         => array(
                                $this,
                                'settings_page',
                        ),
                ),
            // settings
                array(
                        'parent_page_slug' => static::$printables['admin_page_slug'],
                        'menu_page_title'  => static::$printables[1]['sub_item_page_title'],
                        'menu_text'        => static::$printables[1]['sub_menu_item_title'],
                        'permissions'      => static::$permissions['admin'],
                        'page_slug'        => static::$printables[1]['sub_page_slug'],
                        'function'         => array(
                                $this,
                                'forms_page',
                        ),
                ),

        );

        // add wp filter for menu items
        $menu_items = apply_filters('ce_wp_pl', $menu_items);

        // add main menu item
        add_menu_page(
                static::$printables['admin_menu_title'],
                static::$printables['admin_menu_page_title'],
                static::$permissions['admin'],
                static::$printables['admin_page_slug'],
                array(
                        $this,
                        'cewp_home_page',
                ),
                C_L_PUBLIC_URL.'images/ce_icon_24x24.png' //static::$printables['admin_page_icon_url']

        );

        // add submenu pages
        foreach ($menu_items as $item) {
            add_submenu_page(
                    $item['parent_page_slug'],
                    $item['menu_page_title'],
                    $item['menu_text'],
                    $item['permissions'],
                    $item['page_slug'],
                    $item['function']
            );
        }
    }

    /**
     * content of the page.
     */
    public function cewp_home_page()
    {
        $this->cewp_api_key_page();
    }

/**
 * Content of the API key page.
 */
public function cewp_api_key_page()
{
    if ($_POST) {
        // Säubere und validiere die API-Schlüsselform
        $validated_data = $this->validate_and_save_api_form($_POST);
        
        if ($validated_data !== false) {
            $api = new CE_SOAPAuth(get_option('cl_wp_pl_user_id'), $validated_data['api_key'], '1.0');
            $GLOBALS['CE_WP_PL_API_CONNECTION_CLASS'] = $api;
            $GLOBALS['CE_WP_PL_API_IS_CONNECTED'] = $api->cewp_is_connected();
        }

        // Drucke Nachrichten (Fehler oder Erfolg)
        //@TODO Stilisiere diese Seite
        $this->cewp_print_messages($this->form_messages);
    }

    include_once C_L_VIEWS_DIR . 'api_key_page.php';
}

/**
 * Content of the settings page.
 */
public function settings_page()
{
    if ($_POST) {
        // Säubere und validiere die Einstellungenform
        $validated_data = $this->cewp_validate_and_save_settings_form($_POST);
        
        if ($validated_data !== false) {
            //@TODO Implementiere die Speicherung der validierten Daten
        }

        //@TODO Stilisiere diese Seite
        $this->cewp_print_messages($this->form_messages);
    }

    include_once C_L_VIEWS_DIR . 'settings_page.php';
}

/**
 * Content of the forms page.
 */
public function forms_page()
{
    if ($_POST) {
        // Säubere und validiere die Formulardaten
        $validated_data = $this->cewp_validate_and_save_forms_form($_POST);
        
        if ($validated_data !== false) {
            //@TODO Implementiere die Speicherung der validierten Daten
        }

        //@TODO Stilisiere diese Seite
        $this->cewp_print_messages($this->form_messages);
    }

    include_once C_L_VIEWS_DIR . 'forms_page.php';
}



    /**
     * add js and css.
     */
    public function admin_css_js()
    {
        if (!isset($_GET['page']) || strpos($_GET['page'], static::$printables['admin_page_slug']) !== 0) {
            return false;
        }

        // add css and js
        wp_enqueue_style('cl-style', C_L_PUBLIC_URL.'css/admin.css', array(), '1.0.0', false);
        wp_enqueue_script('cl-js-admin', C_L_PUBLIC_URL.'js/admin.js', array('jquery'));

        return true;
    }

    /**
     * @param $post
     *
     * @return bool
     */
    public function validate_and_save_api_form($post)
    {
        //error messages
        $error_messages = array(
                'cl_wp_pl_user_id' => 'User id is required',
                'cl_wp_pl_api_key' => 'Api key is required',
        );

        $notice_messages = array(
                'saved' => 'Changes are saved',
        );

        $valid = true;
        foreach ($post as $field => $data) {
            if (empty($post[$field]) and $post[$field] != 0) {
                $valid = false;
                $this->form_messages[] = $error_messages[$field];
            }

            update_option($field, $data);
        }

        if (!$valid) {
            return false;
        }

        // update database
        $this->form_messages[] = $notice_messages['saved'];
    }

    /**
     * @param $post
     *
     * @return bool
     */
    public function cewp_validate_and_save_settings_form($post)
    {

        //error messages
        $error_messages = array(
                'list_empty'              => 'Al least one list should be selected',
                'cl_wp_pl_checkbox_title' => 'Check box title is required.',
        );

        // notice message
        $notice_messages = array(
                'saved' => 'Changes are saved',
                'test_mode_disabled' => '',
        );

        $valid = true;

        $subscriber_lists = $post['cl_wp_pl_api_subscriber_lists'];
        if (count($post['cl_wp_pl_api_subscriber_lists'])) {
            if (!array_search(1, $subscriber_lists)) {
                // no list selectes
                $this->form_messages[] = $error_messages['list_empty'];
                $valid = false;
            } else {
                // prepare to save to database
                $lists_to_database = serialize($subscriber_lists);
            }
        }

        if ($post['cl_wp_pl_checkbox_title'] == '') {
            $valid = false;
            $this->form_messages[] = $error_messages['cl_wp_pl_checkbox_title'];
            //static::$printables['default_checkbox_subscribe_title'];
        }

        // remove list check box array from post
        unset($post['cl_wp_pl_api_subscriber_lists']);

        if (!$valid) {
            return false;
        }

        foreach ($post as $field => $value) {
            update_option($field, $value);
        }

        // update database
        update_option('cl_wp_pl_api_subscriber_lists', $lists_to_database);
        $this->form_messages[] = $notice_messages['saved'];

        if (get_option('cl_wp_pl_api_is_test_mode')) {
            $this->form_messages[] = $notice_messages['test_mode_enabled'];
        } else {
            $this->form_messages[] = $notice_messages['test_mode_disabled'];
        }
    }

    /**
     * @param $post
     *
     * @return bool
     */
    public function cewp_validate_and_save_forms_form($post)
    {
        //error messages
        $error_messages = array(
                'list_empty'                     => 'Al least one list should be selected',
                'cl_wp_pl_checkbox_title'        => 'Check box title is required.',
                'form_html_empty'                => 'Form HTML is required',
                'cl_wp_pl_forms_success_message' => 'Success message is required',
                'cl_wp_pl_forms_error_message'   => 'Error message is required',
        );

        // notice message
        $notice_messages = array(
                'saved' => 'Changes are saved',
        );

        $valid = true;

        $subscriber_lists = $post['cl_wp_pl_api_subscriber_lists_form'];

        if (count($post['cl_wp_pl_api_subscriber_lists_form'])) {
            if (!array_search(1, $subscriber_lists)) {
                // no list selected
                $this->form_messages[] = $error_messages['list_empty'];
                $valid = false;
            } else {
                // prepare to save to database
                $lists_to_database = serialize($subscriber_lists);
            }
        }

        if (trim($post['cl_wp_pl_form_html']) == '') {
            // form html is empty
            $this->form_messages[] = $error_messages['form_html_empty'];
            $valid = false;
        }

        if (trim($post['cl_wp_pl_forms_success_message']) == '') {
            // form html is empty
            $this->form_messages[] = $error_messages['cl_wp_pl_forms_success_message'];
            $valid = false;
        }

        if (trim($post['cl_wp_pl_forms_error_message']) == '') {
            // form html is empty
            $this->form_messages[] = $error_messages['cl_wp_pl_forms_error_message'];
            $valid = false;
        }

        if (!$valid) {
            return false;
        }

        // remove list check box array from post
        unset($post['cl_wp_pl_api_subscriber_lists_form']);

        foreach ($post as $field => $value) {
            update_option($field, $value);
        }

        // update database
        update_option('cl_wp_pl_api_subscriber_lists_form', $lists_to_database);
        update_option('cl_wp_pl_form_html', $post['cl_wp_pl_form_html']);
        $this->form_messages[] = $notice_messages['saved'];
    }

    /**
     * @param $messages
     */
    public function cewp_print_messages($messages)
    {
        $_messages = '';
        foreach ($messages as $message) {
            if (!empty($message)) {
                $_messages .= '<div class="update-nag">' . esc_html($message) . '</div>';
            }
        }

        echo $_messages;
    }


    /**
     * Set all default options (into database) need for the plugin to function properly.
     */
    public function cewp_default_options()
    {
        if (get_option('cl_wp_pl_checkbox_title') == null) {
            update_option('cl_wp_pl_checkbox_title', static::$printables['default_checkbox_subscribe_title']);
        }

        if (get_option('cl_wp_pl_forms_success_message') == null) {
            update_option('cl_wp_pl_forms_success_message',
                    static::$printables['default_cl_wp_pl_forms_success_message']);
        }

        if (get_option('cl_wp_pl_forms_error_message') == null) {
            update_option('cl_wp_pl_forms_error_message', static::$printables['default_cl_wp_pl_forms_error_message']);
        }
    }
}

$admin = new CleverElementsAdmin();
