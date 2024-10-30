<?php

// Security - Prevent direct file access
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}
/*
 * Plugin Name: Clever Elements
 * Plugin URI: http://www.cleverelements.com
 * Description: Clever Elemente email marketing for WordPress. Adds various sign-up methods to your website.
 * Version: 1.6.0
 * Author: Clever Elements
 * Author URI: http://www.cleverelements.com
 * License: GPLv2 or later
 */
if ( ! extension_loaded('soap')) {
    echo '<div style="color:red; font-weight: bold; margin: 20px 0 0 180px">Clever Elements require PHP module SOAP to be installed on your server.</div>';
} else {
    define('C_L_PLUGIN_DIR', plugin_dir_path(__FILE__));
    define('C_L_PLUGIN_DIR_URL', plugins_url('/', __FILE__));
    define('C_L_VIEWS_DIR', C_L_PLUGIN_DIR.'views/');
    define('C_L_PUBLIC_URL', C_L_PLUGIN_DIR_URL.'public/');
    define('C_L_PLUGIN_FILE', __FILE__);
    
    // api class
    include_once C_L_PLUGIN_DIR.'includes/clever_elements_api.php';
    
    // widget
    include_once C_L_PLUGIN_DIR.'includes/cl_widget.php';
    
    if (is_admin()) {
        require_once C_L_PLUGIN_DIR.'includes/admin.php';
    }
    
    require_once C_L_PLUGIN_DIR.'includes/frontend.php';
}
