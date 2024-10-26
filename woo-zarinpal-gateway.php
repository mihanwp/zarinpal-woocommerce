<?php
/**
 * Plugin Name: درگاه پرداخت زرین پال برای ووکامرس
 * Plugin URI: https://mihanwp.com/zarinpal-woo
 * Author: میهن وردپرس | علیرضا دهکار
 * Author URI: https://mihanwp.com
 * Description: افزودن درگاه پرداخت زرین پال به پلاگین ووکامرس، سازگار با حالت کلاسیک و بلاک صفحه تسویه حساب
 * Version: 1.0.0
 */

if (!defined('ABSPATH'))
    exit;

define('ZPGATE_PLUGIN_FILE_PATH', __FILE__);
define('ZPGATE_PATH', trailingslashit(plugin_dir_path(ZPGATE_PLUGIN_FILE_PATH)));
define('ZPGATE_INC_PATH', trailingslashit(ZPGATE_PATH . 'includes'));
define('ZPGATE_URL', trailingslashit(plugin_dir_url(ZPGATE_PLUGIN_FILE_PATH)));
define('ZPGATE_CSS_URL', trailingslashit(ZPGATE_URL . 'assets/css'));
define('ZPGATE_JS_URL', trailingslashit(ZPGATE_URL . 'assets/js'));
define('ZPGATE_IMG_URL', trailingslashit(ZPGATE_URL . 'assets/images'));

require_once ZPGATE_PATH . 'core.php';

if(class_exists('ZarinpalPlugin')){
    ZarinpalPlugin::Instance();
}
