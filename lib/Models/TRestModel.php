<?php

/**
 *
 * Model that should be use used to inherit the functionallity of this library
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Models
 */
namespace TRest\Models;

use TRest\Http\TRestRequest;

abstract class TRestModel extends TRestModelBase {

    /**
     * Creates (POST) or updates(PUT) an entity
     *
     * @return boolean
     */
    public function save() {
        $request = (new TRestRequest())->setUrl(self::getConfig()->getApiUrl())->setResource(static::$resource)->setPath($this->{static::$recordId})->setEntity(json_encode(array(
            static::$singleItemNode => $this->mapToJSON()
        )));
        $result = $this->{static::$recordId} ? self::getRequestClient()->put($request) : self::getRequestClient()->post($request);
        if ($result)
            $this->constructObject(self::getSingleItemNode($result));
        return true;
    }

    /**
     * Deletes an entity
     */
    public function delete() {
        $request = (new TRestRequest())->setUrl(self::getConfig()->getApiUrl())->setResource(static::$resource)->setPath($this->{static::$recordId});
        self::getRequestClient()->delete($request);
    }

    /**
     *
     * Finds a entity by it's specified identifer
     *
     * @return TRestModel
     */
    public static function find($id, $params = array(), $path = null, $cacheTtl = TREST_DEFAULT_CACHE_TTL) {
        $request = (new TRestRequest())->setUrl(self::getConfig()->getApiUrl())->setPath($path)->setResource(static::$resource)->setPath($id)->setParameters($params);
        $cacheKey = $request->getUrlHash();
        if (self::isValidCache($cacheTtl)) {
            if (self::getConfig()->getCacheAdapter()->exists($cacheKey)) {
                return self::getConfig()->getCacheAdapter()->get($cacheKey);
            }
            $singleItemNode = self::getSingleItemNode(self::getRequestClient()->get($request));
            if (! $singleItemNode)
                return null;
            return self::getConfig()->getCacheAdapter()->set($cacheKey, self::mapToObject($singleItemNode, get_called_class()), $cacheTtl)->get($cacheKey);
        } else {
            return self::mapToObject(self::getSingleItemNode(self::getRequestClient()->get($request)), get_called_class());
        }
    }

    /**
     *
     * Returns an object with a list of objects in a property named items and in
     * case that pagination is used, in another property name count returns the
     * total items that are available for this response
     *
     * @param number $limit            
     * @param number $page            
     * @param array $params            
     * @param string $path            
     * @param number $cacheTtl            
     * @return array of TRestModels
     */
    public static function findAll($limit = 0, $page = 0, $params = array(), $path = null, $cacheTtl = TREST_DEFAULT_CACHE_TTL) {
        $request = (new TRestRequest())->setUrl(self::getConfig()->getApiUrl())->setPath($path)->setResource(static::$resource)->setParameters($params)->setParameter('limit', $limit)->setParameter('page', $page);
        $cacheKey = $request->getUrlHash();
        if (self::isValidCache($cacheTtl)) {
            if (self::getConfig()->getCacheAdapter()->exists($cacheKey)) {
                return self::getConfig()->getCacheAdapter()->get($cacheKey);
            }
        }
        $response = self::getRequestClient()->get($request);
        $responseItems = self::getListItemNode($response);
        $result = new \stdClass();
        $result->items = array();
        $result->count = self::getListCountNode($response);
        foreach ($responseItems as $item) {
            $result->items[] = self::mapToObject($item, get_called_class());
        }
        if (self::isValidCache($cacheTtl)) {
            self::getConfig()->getCacheAdapter()->set($cacheKey, $result, $cacheTtl);
        }
        return $result;
    }

    /**
     *
     * Assigns the properties values to this class
     *
     * @param object $values            
     */
    protected function constructObject($values = null) {
        $fields = $this->fields();
        if ($values) {
            $this->assignPropertyValues($values, $fields);
            $this->assignRelations($values, $this->relations());
        } else {
            foreach ($fields as $key => $value) {
                $this->assignEmptyPropertyValue($key, $value['type']);
            }
        }
    }

    /**
     *
     * Constructs an object and assign the parameters specified by the $values
     * argument passed to the constructor
     *
     * @param object $values            
     */
    public function __construct($values = null) {
        $this->constructObject($values);
        $this->build();
    }
}
