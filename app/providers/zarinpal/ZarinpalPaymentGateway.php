<?php
namespace ZarinpalGate\App\Providers\Zarinpal;

class ZarinpalPaymentGateway extends \WC_Payment_Gateway
{
    private $merchantCode;
    private $failedMassage;
    private $successMassage;

    public function __construct()
    {
        $this->id = ZarinPalUnifiedGateway::ID;
        $this->method_title = __('پرداخت امن زرین پال', 'woocommerce');
        $this->method_description = __('تنظیمات درگاه پرداخت زرین پال برای افزونه فروشگاه ساز ووکامرس', 'woocommerce');
        $this->icon = ZPGATE_IMG_URL . 'logo.png';
        $this->has_fields = false;

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_setting('title');
        $this->description = $this->get_setting('description');

        $this->merchantCode = $this->get_setting('merchant_id');

        $this->successMassage = $this->get_setting('success_massage');
        $this->failedMassage = $this->get_setting('failed_massage');

        if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        } else {
            add_action('woocommerce_update_options_payment_gateways', array($this, 'process_admin_options'));
        }

        add_action('woocommerce_receipt_' . $this->id, array($this, 'send_to_gateway'));
        add_action('woocommerce_api_' . strtolower(ZarinpalUnifiedGateway::ID), array($this, 'return_from_gateway'));
    }

    public function get_setting($key)
    {
        return $this->settings[$key] ?? false;
    }

    private function get_order($order_id)
    {
        return new \WC_Order($order_id);
    }

    public function init_form_fields()
    {
        $this->form_fields = apply_filters(
            ZarinpalUnifiedGateway::get_prefix() . '_Config',
            array(
                'base_config' => array(
                    'title' => __('تنظیمات پایه ای', 'woocommerce'),
                    'type' => 'title',
                    'description' => '',
                ),
                'enabled' => array(
                    'title' => __('فعالسازی/غیرفعالسازی', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('فعالسازی درگاه زرین پال', 'woocommerce'),
                    'description' => __('برای فعالسازی درگاه پرداخت زرین پال باید چک باکس را تیک بزنید', 'woocommerce'),
                    'default' => 'yes',
                    'desc_tip' => true,
                ),
                'sandbox_enabled' => array(
                    'title' => __('فعالسازی/غیرفعالسازی', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('فعالسازی سندباکس', 'woocommerce'),
                    'description' => __('برای فعالسازی سندباکس (حالت تست) باید چک باکس را تیک بزنید', 'woocommerce'),
                    'default' => 'no',
                    'desc_tip' => true,
                ),
                'title' => array(
                    'title' => __('عنوان درگاه', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('عنوان درگاه که در طی خرید به مشتری نمایش داده میشود', 'woocommerce'),
                    'default' => __('پرداخت امن زرین پال', 'woocommerce'),
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => __('توضیحات درگاه', 'woocommerce'),
                    'type' => 'text',
                    'desc_tip' => true,
                    'description' => __('توضیحاتی که در طی عملیات پرداخت برای درگاه نمایش داده خواهد شد', 'woocommerce'),
                    'default' => __('پرداخت امن به وسیله کلیه کارت های عضو شتاب از طریق درگاه زرین پال', 'woocommerce')
                ),
                'account_config' => array(
                    'title' => __('تنظیمات حساب زرین پال', 'woocommerce'),
                    'type' => 'title',
                    'description' => '',
                ),
                'merchant_id' => array(
                    'title' => __('مرچنت کد', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('مرچنت کد درگاه زرین پال', 'woocommerce'),
                    'default' => '',
                    'desc_tip' => true
                ),
                'payment_config' => array(
                    'title' => __('تنظیمات عملیات پرداخت', 'woocommerce'),
                    'type' => 'title',
                    'description' => '',
                ),
                'success_massage' => array(
                    'title' => __('پیام پرداخت موفق', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('متن پیامی که میخواهید بعد از پرداخت موفق به کاربر نمایش دهید را وارد نمایید . همچنین می توانید از شورت کد {transaction_id} برای نمایش کد رهگیری (توکن) زرین پال استفاده نمایید .', 'woocommerce'),
                    'default' => __('با تشکر از شما . سفارش شما با موفقیت پرداخت شد .', 'woocommerce'),
                ),
                'failed_massage' => array(
                    'title' => __('پیام پرداخت ناموفق', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('متن پیامی که میخواهید بعد از پرداخت ناموفق به کاربر نمایش دهید را وارد نمایید . همچنین می توانید از شورت کد {fault} برای نمایش دلیل خطای رخ داده استفاده نمایید . این دلیل خطا از سایت زرین پال ارسال میگردد .', 'woocommerce'),
                    'default' => __('پرداخت شما ناموفق بوده است . لطفا مجددا تلاش نمایید یا در صورت بروز اشکال با مدیر سایت تماس بگیرید .', 'woocommerce'),
                ),
            )
        );
    }

    public function process_payment($order_id)
    {
        $order = $this->get_order($order_id);
        return array(
            'result' => 'success',
            'redirect' => $order->get_checkout_payment_url(true)
        );
    }

    private function get_base_url()
    {
        $type = $this->get_setting('sandbox_enabled') == 'yes' ? 'sandbox' : 'payment';
        return 'https://' . $type . '.zarinpal.com/';
    }

    public function send_request($action, $params)
    {
        try {
            $ch = curl_init($this->get_base_url() . 'pg/v4/payment/' . $action . '.json');
            curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params)
            ));
            $result = curl_exec($ch);
            return json_decode($result, true);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function send_to_gateway($order_id)
    {
        global $woocommerce;
        $woocommerce->session->order_id_zarinpal = $order_id;

        $order = $this->get_order($order_id);
        $currency = apply_filters(ZarinpalUnifiedGateway::get_prefix() . '_Currency', $order->get_currency(), $order_id);

        $form = '<form action="" method="POST" class="zarinpal-checkout-form" id="zarinpal-checkout-form">
                <input type="submit" name="zarinpal_submit" class="button alt" id="zarinpal-payment-button" value="' . __('پرداخت', 'woocommerce') . '"/>
                <a class="button cancel" href="' . wc_get_checkout_url() . '">' . __('بازگشت', 'woocommerce') . '</a>
             </form><br/>';

        $form = apply_filters(ZarinpalUnifiedGateway::get_prefix() . '_Form', $form, $order_id, $woocommerce);

        do_action(ZarinpalUnifiedGateway::get_prefix() . '_gateway_before_form', $order_id, $woocommerce);
        echo wp_kses($form, [
            'form' => ['action', 'method', 'class', 'id'],
            'input' => ['type', 'name', 'class', 'id', 'value'],
            'a' => ['class', 'href']
        ]);
        do_action('WC_ZPal_Gateway_After_Form', $order_id);

        $Amount = intval($order->get_total());
        $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_before_check_currency', $Amount, $currency);
        $strToLowerCurrency = strtolower($currency);

        switch ($strToLowerCurrency) {
            case 'irht':
                $Amount *= 1000;
                break;
            case 'irhr':
                $Amount *= 100;
                break;
        }

        $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_after_check_currency', $Amount, $currency);
        $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_irt', $Amount, $currency);
        $Amount = apply_filters('woocommerce_order_amount_total_ZarinPal_gateway', $Amount, $currency);

        $CallbackUrl = add_query_arg('wc_order', $order_id, WC()->api_request_url(strtolower(ZarinpalUnifiedGateway::ID)));

        $Description = sprintf('خرید به شماره سفارش : %s | خریدار : %s %s',
            $order->get_order_number(),
            $order->get_billing_first_name(),
            $order->get_billing_last_name()
        );

        $Mobile = apply_filters(ZarinpalUnifiedGateway::get_prefix() . '_Mobile', $order->get_billing_phone(), $order_id);
        $Email = filter_var($order->get_billing_email(), FILTER_VALIDATE_EMAIL) ?: '';

        do_action(ZarinpalUnifiedGateway::get_prefix() . '_gateway_payment', $order_id, $Description, $Mobile);

        if (preg_match('/^(\+989|989|\+9809|9809)([0-9]{9})$/i', $Mobile, $matches)) {
            $Mobile = '09' . $matches[2];
        } elseif (preg_match('/^9[0-7]{1}[0-9]{8}$/i', $Mobile)) {
            $Mobile = preg_replace('/^9/', '0$0', $Mobile);
        } else {
            $Mobile = preg_match('/^09[0-7]{1}[0-9]{8}$/i', $Mobile) ? $Mobile : '';
        }

        $data = [
            'merchant_id' => $this->merchantCode,
            'amount' => $Amount,
            'callback_url' => $CallbackUrl,
            'description' => $Description,
            'currency' => strtoupper($currency),
            'metadata' => ['order_id' => "سفارش شماره $order_id"]
        ];

        if ($Mobile) {
            $data['metadata']['mobile'] = $Mobile;
        }
        if ($Email) {
            $data['metadata']['email'] = $Email;
        }
        $result = $this->send_request('request', json_encode($data));

        if ($result === false) {
            echo esc_html('cURL Error #:');
        } elseif (isset($result['data']['code']) && $result['data']['code'] == 100) {
            header('Location: ' . $this->get_base_url() . 'pg/StartPay/' . $result['data']["authority"]);
            exit;
        } else {
            $this->handle_payment_error($result, $order);
        }
    }

    public function return_from_gateway()
    {
        global $woocommerce;
        $InvoiceNumber = isset($_POST['InvoiceNumber']) ? sanitize_text_field($_POST['InvoiceNumber']) : '';
        $order_id = isset($_GET['wc_order']) ? sanitize_text_field($_GET['wc_order']) : ($InvoiceNumber ?: $woocommerce->session->order_id_zarinpal);

        if ($order_id) {
            $order = $this->get_order($order_id);
            $currency = apply_filters(ZarinpalUnifiedGateway::get_prefix() . '_currency', $order->get_currency(), $order_id);

            if ($order->get_status() !== 'completed') {
                $MerchantID = $this->merchantCode;
                if ($_GET['Status'] === 'OK') {
                    $Amount = intval($order->get_total());
                    $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_before_check_currency', $Amount, $currency);
                    $strToLowerCurrency = strtolower($currency);

                    switch ($strToLowerCurrency) {
                        case 'irht':
                            $Amount *= 1000;
                            break;
                        case 'irhr':
                            $Amount *= 100;
                            break;
                    }

                    $Authority = sanitize_text_field($_GET['Authority']);
                    $data = [
                        'merchant_id' => $MerchantID,
                        'authority' => $Authority,
                        'amount' => $Amount
                    ];
                    $result = $this->send_request('verify', json_encode($data));

                    $this->handle_transaction_result($result, $order, $MerchantID, $order_id);
                } else {
                    $this->handle_failed_transaction($order);
                }
            }
        }
    }

    private function handle_payment_error($result, $order)
    {
        if (isset($result['errors']['code'])){
            $Message = ' تراکنش ناموفق بود- کد خطا : ' . $result['errors']['code'];
        } else {
            $Message = 'تراکنش ناموفق بود';
        }
        $Note = sprintf(__('خطا در هنگام ارسال به بانک : %s', 'woocommerce'), $Message);
        $order->add_order_note($Note);

        $Notice = sprintf(__('در هنگام اتصال به بانک خطای زیر رخ داده است : <br/>%s', 'woocommerce'), $Message);
        if ($Notice) {
            wc_add_notice($Notice, 'error');
        }
        if (isset($result['errors']['code']))
            do_action(ZarinpalUnifiedGateway::get_prefix() . '_send_to_gateway_failed', $order->get_id(), $result['errors']['code']);
    }

    private function handle_transaction_result($result, $order, $MerchantID, $order_id)
    {
        global $woocommerce;

        if ($result['data']['code'] == 100) {
            $Transaction_ID = $result['data']['ref_id'];
            update_post_meta($order_id, '_transaction_id', $Transaction_ID);
            $order->payment_complete($Transaction_ID);
            $woocommerce->cart->empty_cart();

            $Note = sprintf(__('پرداخت موفقیت آمیز بود .<br/> کد رهگیری : %s', 'woocommerce'), $Transaction_ID);
            $order->add_order_note($Note, 1);

            $Notice = wpautop(wptexturize($this->successMassage));
            $Notice = str_replace('{transaction_id}', $Transaction_ID, $Notice);
            wc_add_notice($Notice, 'success');

            do_action(ZarinpalUnifiedGateway::get_prefix() . '_return_from_gateway_success', $order_id, $Transaction_ID);
            wp_redirect(add_query_arg('wc_status', 'success', $this->get_return_url($order)));
            exit;
        } elseif ($result['data']['code'] == 101) {
            $Message = 'این تراکنش قبلاً تایید شده است';
            wc_add_notice($Message, 'error');
            wp_redirect(add_query_arg('wc_status', 'success', $this->get_return_url($order)));
            exit;
        } else {
            $Fault = $result['errors']['code'];
            $Message = sprintf('تراکنش ناموفق بود - کد خطا : %s', $Fault);
            $Note = sprintf(__('خطا در هنگام بررسی پرداخت : %s', 'woocommerce'), $Message);
            $order->add_order_note($Note);

            $Notice = sprintf(__('تراکنش ناموفق بود - خطا : %s', 'woocommerce'), $Fault);
            wc_add_notice($Notice, 'error');
            do_action(ZarinpalUnifiedGateway::get_prefix() . '_return_from_gateway_failed', $order_id, $Fault);

            wp_redirect(wc_get_checkout_url());
        }
    }

    private function handle_failed_transaction($order)
    {
        $Message = __('تراکنش ناموفق بود', 'woocommerce');
        $Note = __('پرداخت ناموفق بود.', 'woocommerce');
        $order->add_order_note($Note);
        wc_add_notice($Message, 'error');
        wp_redirect(wc_get_checkout_url());
        exit();
    }
}