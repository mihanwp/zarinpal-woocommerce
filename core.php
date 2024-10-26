<?php
/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

final class ZarinpalPlugin {
    private static $instance = null;

    public static function Instance(){
        if(!self::$instance){
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        add_action('plugins_loaded', [$this, 'include_files']);
    }

    /**
     * Include init files
     *
     * @return void
     */
    public function include_files()
    {
        $this->register_autoload();

        require_once ZPGATE_INC_PATH . 'functions.php';

        \ZarinpalGate\App\Hooks::init();
    }

    public function register_autoload()
    {
        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }

        spl_autoload_register([$this, 'autoloader']);
    }

    public function autoloader($class)
    {
        if(strpos($class, 'ZarinpalGate') !== false){
            $class = str_replace(['ZarinpalGate\\', 'ZarinpalGate', '\\'], ['', '', '/'], $class);
            $class_arr = explode('/', $class);
            $file_name = $class_arr[array_key_last($class_arr)] . '.php';
            unset($class_arr[array_key_last($class_arr)]);
            $file_path = ZPGATE_PATH . strtolower(implode('/', $class_arr)) . '/' . $file_name;

            if(file_exists($file_path) && is_readable($file_path)){
                include_once($file_path);
            }
        }
    }
}