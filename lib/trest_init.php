<?php

/**
 * procedural file used to setup the TRest library
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 */
namespace TRest;

define('TREST_LIB_PATH', __DIR__);
define('TREST_LIB_THIRD_PARTY_PATH', TREST_LIB_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ThirdParty');
define('TREST_DEFAULT_CACHE_TTL', 120);

use TRest\Config\TRestConfigFactory;
use TRest\Config\TRestConfig;
use TRest\Cache\TRestFastCache;

spl_autoload_register(function ($class) {
    $classParts = explode("\\", $class);
    if ($classParts[0] == __NAMESPACE__)
        require __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array_slice($classParts, 1)) . '.php';
});

/**
 * Api brought to you by Josue Abreu <https://github.com/gotjosh> from Pixel
 * Perfect Tree <http://pixelpt.com/>
 */

TRestConfigFactory::add('default', new TRestConfig(array(
    'apiUrl' => 'http://pixelpt-sandwich-api.herokuapp.com/',
    'singleItemNode' => 'sandwich',
    'cacheAdapter' => new TRestFastCache()
)));

/**
 * Stack overflow public used to look for users data
 */

TRestConfigFactory::add('StackOverflow', new TRestConfig(array(
    'apiUrl' => 'http://api.stackoverflow.com/1.1/',
    'cacheAdapter' => new TRestFastCache()
)));
