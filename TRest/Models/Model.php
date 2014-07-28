<?php

/**
 *
 * Model that should be use used to inherit the functionallity of this library
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Models
 */
namespace TRest\Models;

use TRest\Http\Request;

abstract class Model extends Base {

    /**
     * Creates (POST) or updates(PUT) an entity
     *
     * @return boolean
     */
    public function save() {
        $request = (new Request())->setUrl(self::getConfig()->getApiUrl())->setResource(static::$resource)->setPath($this->{static::$recordId})->setEntity(json_encode(array(
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
        $request = (new Request())->setUrl(self::getConfig()->getApiUrl())->setResource(static::$resource)->setPath($this->{static::$recordId});
        self::getRequestClient()->delete($request);
    }

    /**
     *
     * Finds a entity by it's specified identifer
     *
     * @return Model
     */
    public static function findOne($id) {
        return static::find()->findOne($id);
    }
    
    public static function findOneByQuery($id, Query $query){
        $request = self::getRequest($query, $id);
        $cacheKey = $request->getUrlHash();
        if (self::isValidCache($query->getCacheTtl())) {
            if (self::getConfig()->getCacheAdapter()->exists($cacheKey)) {
                return self::getConfig()->getCacheAdapter()->get($cacheKey);
            }
            $singleItemNode = self::getSingleItemNode(self::getRequestClient()->get($request));
            if (! $singleItemNode)
                return null;
            return self::getConfig()->getCacheAdapter()->set($cacheKey, self::mapToObject($singleItemNode, get_called_class()), $query->getCacheTtl())->get($cacheKey);
        } else {
            return self::mapToObject(self::getSingleItemNode(self::getRequestClient()->get($request)), get_called_class());
        }        
    }

    public static function find(){
        return new Query(new static);
    }
    
    protected static function getRequest($query, $id = null){
        return (new Request())
        ->setUrl(self::getConfig()->getApiUrl())
        ->setPath($query->getPath())
        ->setResource($query->getResource() ? $query->getResource() : static::$resource)
        ->setEntity($id)
        ->setParameters($query->toParams());
    }

    public static function findByQuery($query){
        $request = self::getRequest($query);
        $cacheKey = $request->getUrlHash();
        if (self::isValidCache($query->getCacheTtl())) {
            if (self::getConfig()->getCacheAdapter()->exists($cacheKey)) {
                return self::getConfig()->getCacheAdapter()->get($cacheKey);
            }
        }
        $response = self::getRequestClient()->get($request);
        if(is_array($query->getPath()) && array_key_exists('ITEM_NODE', $query->getPath())){
            $responseItems = self::getListItemNode($response, $path['ITEM_NODE']);
        } else {
            $responseItems = self::getListItemNode($response);
        }
        $result = new \stdClass();
        $result->items = array();
        $result->count = self::getListCountNode($response);
        if($responseItems){
            foreach ($responseItems as $item) {
                $result->items[] = self::mapToObject($item, get_called_class());
            }
        }
        if (self::isValidCache($query->getCacheTtl())) {
            self::getConfig()->getCacheAdapter()->set($cacheKey, $result, $query->getCacheTtl());
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
        $this->build($values);
    }
}
