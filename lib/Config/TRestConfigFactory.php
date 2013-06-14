<?php

/**
 * This class is a factory of multiple TRestConfig, the name 'default' will be used for the default
 * config by your models. So, somewhere in your application <trest_init.php is your file, look for it>
 *
 *
 *  TRestConfigFactory::add('default', new TRestConfig(array(
 *    'apiUrl' => 'your_api_url',
 *  )));
 *
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 */

namespace TRest\Config;

class TRestConfigFactory {

    private static $configs = array();

    public static function add($name, TRestConfig $config){
        self::$configs[$name] = $config;
    }

    public static function get($configName){
        return self::$configs[$configName];
    }

}