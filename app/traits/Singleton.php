<?php
namespace ZarinpalGate\App\Traits;

trait Singleton
{
    private static $instance = null;

    public static function Instance(){
        if(!self::$instance){
            self::$instance = new self();
        }

        return self::$instance;
    }
}