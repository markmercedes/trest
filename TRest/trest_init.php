<?php

/**
 * procedural file used to setup the TRest library
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest
 */
namespace TRest;

define('TREST_LIB_PATH', __DIR__);
define('TREST_LIB_THIRD_PARTY_PATH', TREST_LIB_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ThirdParty');
/**
 *
 * @var integer the amount of time in seconds that will be used by default save
 *      cache for the responses
 */
define('TREST_DEFAULT_CACHE_TTL', 120);

use TRest\Config\ConfigFactory;
use TRest\Config\Config;
use TRest\Cache\FastCache;

spl_autoload_register(function ($class) {
    $classParts = explode("\\", $class);
    if ($classParts[0] == __NAMESPACE__) {
        require __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array_slice($classParts, 1)) . '.php';
    }
});

/**
 * Api brought to you by Josue Abreu <https://github.com/gotjosh> from Pixel
 * Perfect Tree <http://pixelpt.com/>
 */

/**
 * The config named 'default' will be used for the models without the property
 * => protected static $configName = 'StackOverflow';
 */
ConfigFactory::add('default', new Config(array(
    'apiUrl' => 'http://pixelpt-sandwich-api.herokuapp.com/',
    'singleItemNode' => 'sandwich',
    'cacheAdapter' => new FastCache()
)));

/**
 * Stack overflow public used to look for users data
 */

/**
 * The model with this property value will use this configuration => protected
 * static $configName = 'StackOverflow';
 */
ConfigFactory::add('StackOverflow', new Config(array(
    'apiUrl' => 'http://api.stackoverflow.com/1.1/',
    'cacheAdapter' => new FastCache()
)));
