<?php
namespace ZarinpalGate\App;

use ZarinpalGate\App\Providers\Zarinpal\ZarinpalUnifiedGateway;

class Hooks
{
    public static function init()
    {
        ZarinpalUnifiedGateway::instance();
    }
}