<?php

namespace ZarinpalGate\App\Providers\Zarinpal;

class ZarinpalBlocksPaymentMethod extends \Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType {

    protected $name;

    public function __construct()
    {
        $this->name = ZarinpalUnifiedGateway::ID;
    }

    public function initialize() {
        $this->settings = get_option( 'woocommerce_zarinpal_settings', [
            'title' => 'پرداخت امن زرین‌پال',
            'description' => 'پرداخت امن به وسیله درگاه زرین‌پال',
            'enabled' => 'yes',
            'merchant_id' => '',
        ]);
    }

    public function is_active() {
        return filter_var( $this->get_setting( 'enabled', false ), FILTER_VALIDATE_BOOLEAN );
    }

    public function get_payment_method_script_handles() {
        wp_register_script(
            'zarinpal-blocks-integration',
            ZPGATE_URL . 'build/index.js',
            [ 'wp-element', 'wp-components', 'wp-i18n', 'wp-hooks' ],
            get_zarinpal_version(),
            true
        );

        wp_set_script_translations(
            'zarinpal-blocks-integration',
            'zarinpal-blocks-integration'
        );

        wp_localize_script(
            'zarinpal-blocks-integration',
            'wcZarinpalSettings',
            [
                'logoUrl' => ZPGATE_IMG_URL . 'logo.png',
                'title' => $this->get_setting('title'),
                'description' => $this->get_setting('description'),
                'gateName' => ZarinpalUnifiedGateway::ID
            ]
        );

        return ['zarinpal-blocks-integration'];
    }

    public function get_payment_method_script_handles_for_admin() {
        return $this->get_payment_method_script_handles();
    }

    public function get_payment_method_data() {
        return [
            'title'       => $this->get_setting('title'),
            'description' => $this->get_setting('description'),
            'supports'    => $this->get_supported_features(),
        ];
    }

    public function get_supported_features() {
        return [
            'products',
            'refunds',
        ];
    }
}

