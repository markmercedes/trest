<?php

namespace TRest\Config;

/**
 * This class contains the global configurations for this library
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 */
class TRestConfig {

    public function __construct($parameters) {
        $this->apiUrl = $parameters['apiUrl'];
        if (array_key_exists('singleItemNode', $parameters))
            $this->singleItemNode = $parameters['singleItemNode'];
        if (array_key_exists('listItemNode', $parameters))
            $this->listItemNode = $parameters['listItemNode'];
        if (array_key_exists('cacheAdapter', $parameters))
            $this->cacheAdapter = $parameters['cacheAdapter'];
        if (array_key_exists('listCountNode', $parameters))
            $this->listCountNode = $parameters['listCountNode'];
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
     * @var CacheAdapter, has a cache adapter instance that should be used to
     *      cache data in the models
     */
    private $cacheAdapter;

    /**
     *
     * @var string, the name of the node that has the item count for a list result
     */
    private $listCountNode;

    /**
     * @return the $listCountNode
     */
    public function getListCountNode() {
        return $this->listCountNode;
    }

	/**
     *
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