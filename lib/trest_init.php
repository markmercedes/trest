<?php

/**
 * procedural file used to setup the TRest library 
 *
 * @author    Marcos Mercedes <marcos.mercedesn@gmail.com>
 */

require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'TRestConfig.php');
require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'TRestRequest.php');
require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'TRestClient.php');
require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . 'TRestModel.php');

/**
 * Api brought to you by Josue Abreu <https://github.com/gotjosh> from Pixel Perfect Tree <http://pixelpt.com/>
 */

TRestConfigFactory::add('default', new TRestConfig(array(
'apiUrl' => 'http://pixelpt-sandwich-api.herokuapp.com/',
'singleItemNode' => 'sandwich'
)));

/**
 * Stack overflow public used to look for users data
 */

TRestConfigFactory::add('StackOverflow', new TRestConfig(array(
'apiUrl' => 'http://api.stackoverflow.com/1.1/',
)));

