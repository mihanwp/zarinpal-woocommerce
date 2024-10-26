<?php
add_filter('woocommerce_payment_gateways', 'handle_woocommerce_register_zarinpal_gateway');

function handle_woocommerce_register_zarinpal_gateway($methods)
{
    $methods[] = \ZarinpalGate\App\Providers\Zarinpal\ZarinpalUnifiedGateway::BASE_CLASS;
    return $methods;
}

add_filter('woocommerce_currencies', 'handle_zarinpal_register_ir_currency');

function handle_zarinpal_register_ir_currency($currencies)
{
    $currencies['IRR'] = __('ریال', 'woocommerce');
    $currencies['IRT'] = __('تومان', 'woocommerce');
    $currencies['IRHR'] = __('هزار ریال', 'woocommerce');
    $currencies['IRHT'] = __('هزار تومان', 'woocommerce');

    return $currencies;
}

add_filter('woocommerce_currency_symbol', 'handle_zarinpal_register_ir_symbol', 10, 2);

function handle_zarinpal_register_ir_symbol($currency_symbol, $currency)
{
    switch ($currency) {
        case 'IRR':
            $currency_symbol = 'ریال';
            break;
        case 'IRT':
            $currency_symbol = 'تومان';
            break;
        case 'IRHR':
            $currency_symbol = 'هزار ریال';
            break;
        case 'IRHT':
            $currency_symbol = 'هزار تومان';
            break;
    }
    return $currency_symbol;
}

if(!function_exists('get_zarinpal_version')){
    function get_zarinpal_version()
    {
        if(!function_exists('get_plugin_data')){
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $plugin_data = get_plugin_data(ZPGATE_PLUGIN_FILE_PATH);
        return $plugin_data['Version'];
    }
}