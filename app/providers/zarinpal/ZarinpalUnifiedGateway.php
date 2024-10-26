<?php
namespace ZarinpalGate\App\Providers\Zarinpal;

use ZarinpalGate\App\Traits\Singleton;

class ZarinpalUnifiedGateway {
    use Singleton;

    const ID = 'zarinpal';
    const BASE_CLASS = '\ZarinpalGate\App\Providers\Zarinpal\ZarinpalPaymentGateway';

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'handle_enqueue_assets']);
        if($this->has_block_checkout()){
            $this->register_block_gateway();
        }
    }

    public function handle_enqueue_assets()
    {
        wp_enqueue_style(
            'zarinpal-gateway-style',
            ZPGATE_CSS_URL . 'style.css',
            [],
            get_zarinpal_version()
        );
    }

    private function has_block_checkout()
    {
        $checkout_page_id = wc_get_page_id('checkout');
        return $checkout_page_id && has_block('woocommerce/checkout', $checkout_page_id);
    }

    private function register_block_gateway() {
        return new ZarinpalBlocksIntegration;
    }

    public static function get_prefix()
    {
        $input = self::BASE_CLASS;
        $input = trim($input, '\\/');
        return str_replace('\\', '_', $input);
    }
}