<?php

/**
 * procedural file used to setup the TRest library
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest
 */
namespace TRest;
/**
 *
 * @var integer the amount of time in seconds that will be used by default save
 *      cache for the responses
 */
define('TREST_DEFAULT_CACHE_TTL', 120);

require (realpath(__DIR__ . '/../vendor/autoload.php'));

use TRest\Config\ConfigFactory;
use TRest\Config\Config;

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
    'singleItemNode' => 'sandwich'
)));

/**
 * Stack overflow public used to look for users data
 */

/**
 * The model with this property value will use this configuration => protected
 * static $configName = 'StackOverflow';
 */
ConfigFactory::add('StackOverflow', new Config(array(
    'apiUrl' => 'https://api.stackexchange.com/2.2/',
)));
