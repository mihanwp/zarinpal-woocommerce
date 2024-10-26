<?php
namespace ZarinpalGate\App\Providers\Zarinpal;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;

class ZarinpalBlocksIntegration {
    public function __construct() {
        $this->init();
    }

    public function init() {
        add_action(
            'woocommerce_blocks_payment_method_type_registration',
            function( PaymentMethodRegistry $registry ) {
                $registry->register(
                    new ZarinpalBlocksPaymentMethod()
                );
            }
        );
    }
}
