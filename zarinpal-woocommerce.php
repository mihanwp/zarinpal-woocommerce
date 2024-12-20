<?php
/**
 * Plugin Name: Zarinpal for Woocommerce
 * Plugin URI: https://mihanwp.com/zarinpal-woo
 * Author: MihanWP | Bavance.com
 * Author URI: https://mihanwp.com
 * Description: Adding zarinpal payment gateway to woocommerce plugin, compatible with classic and block mode checkout page
 * Version: 1.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH'))
    exit;

if(class_exists('WooZarinpalPlugin'))
    return false;

define('ZPGATE_PLUGIN_FILE_PATH', __FILE__);
define('ZPGATE_PATH', trailingslashit(plugin_dir_path(ZPGATE_PLUGIN_FILE_PATH)));
define('ZPGATE_INC_PATH', trailingslashit(ZPGATE_PATH . 'includes'));
define('ZPGATE_URL', trailingslashit(plugin_dir_url(ZPGATE_PLUGIN_FILE_PATH)));
define('ZPGATE_CSS_URL', trailingslashit(ZPGATE_URL . 'assets/css'));
define('ZPGATE_JS_URL', trailingslashit(ZPGATE_URL . 'assets/js'));
define('ZPGATE_IMG_URL', trailingslashit(ZPGATE_URL . 'assets/images'));

require_once ZPGATE_PATH . 'core.php';

WooZarinpalPlugin::Instance();
