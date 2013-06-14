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

class TRestConfigFactory {

    private static $configs = array();

    public static function add($name, TRestConfig $config){
        self::$configs[$name] = $config;
    }

    public static function get($configName){
        return self::$configs[$configName];
    }

}

/**
 * This class contains the global configurations for this library
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 */
class TRestConfig {

    public function __construct($parameters) {
        $this->apiUrl = $parameters['apiUrl'];
        if(array_key_exists('singleItemNode', $parameters))
            $this->singleItemNode = $parameters['singleItemNode'];
        if(array_key_exists('listItemNode', $parameters))
            $this->listItemNode = $parameters['listItemNode'];
        if(array_key_exists('cacheAdapter', $parameters))
            $this->cacheAdapter = $parameters['cacheAdapter'];
    }

    /**
     *
     * @var string, the baseUrl of the api
     */
    private $apiUrl;

    /**
     *
     * @var string, in case of the data for single a single is contained inside
     *      of a property of the json result
     */
    private $singleItemNode;

    /**
     *
     * @var string, in case of the data for lists of items is contained inside
     *      of a property of the json result
     */
    private $listItemNode;
    
    /**
     * 
     * @var CacheAdapter, has a cache adapter instance that should be used to cache data in the models
     */
    private $cacheAdapter;

    /**
     * @return the $cacheAdapter
     */
    public function getCacheAdapter() {
        return $this->cacheAdapter;
    }

	/**
     *
     * @return the $api_url
     */
    public function getApiUrl() {
        return $this->apiUrl;
    }

    /**
     *
     * @return the $singleItemNode
     */
    public function getSingleItemNode() {
        return $this->singleItemNode;
    }

    /**
     *
     * @return the $listItemNode
     */
    public function getListItemNode() {
        return $this->listItemNode;
    }
}

?>