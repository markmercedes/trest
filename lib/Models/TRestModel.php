<?php

namespace TRest\Models;

use TRest\Config\TRestConfigFactory;
use TRest\Config\TRestConfig;
use TRest\Http\TRestClient;
use TRest\Http\TRestRequest;

abstract class TRestModel {

    const BELONGS_TO = 'RBelongsToRelation';

    const HAS_ONE = 'RHasOneRelation';

    const HAS_MANY = 'RHasManyRelation';

    protected static $resource;

    private static $requestClient;

    protected static $singleItemNode;

    protected static $listItemNode;

    protected static $configName = 'default';

    protected static $listCountNode;
    
    private static $isCacheEnabled;

    /**
     *
     * @return TRestConfig
     */
    protected static function getConfig() {
        return TRestConfigFactory::get(static::$configName);
    }

    protected static function getRequestClient() {
        return self::$requestClient ? self::$requestClient : self::$requestClient = new TRestClient();
    }

    public static function find($id, $params = array(), $cacheTtl = TREST_DEFAULT_CACHE_TTL) {
        $request = (new TRestRequest())->setUrl(self::getConfig()->getApiUrl())->setResource(static::$resource)->setPath($id)->setParameters($params);
        $url = $request->buildUrl();
        if (self::isValidCache($cacheTtl)) {
            if (self::getConfig()->getCacheAdapter()->exists($url)) {
                return self::getConfig()->getCacheAdapter()->get($url);
            }
            return self::getConfig()->getCacheAdapter()->set($url, self::mapToObject(self::getSingleItemNode(self::getRequestClient()->get($request)), get_called_class()), $cacheTtl)->get($url);
        } else {
            return self::mapToObject(self::getSingleItemNode(self::getRequestClient()->get($request)), get_called_class());
        }
    }

    public static function findAll($limit = 0, $page = 0, $params = array(), $cacheTtl = TREST_DEFAULT_CACHE_TTL) {
        $request = (new TRestRequest())->setUrl(self::getConfig()->getApiUrl())->setResource(static::$resource)->setParameters($params);
        if ($limit)
            $request->setParameter('limit', $limit);
        if ($page)
            $request->setParameter('page', $page);
        $result = new \stdClass();
        $result->items = array();
        $url = $request->buildUrl();
        if (self::isValidCache($cacheTtl)) {
            if (self::getConfig()->getCacheAdapter()->exists($url)) {
                return self::getConfig()->getCacheAdapter()->get($url);
            }
        }
        $response = self::getRequestClient()->get($request);
        $responseItems = self::getListItemNode($response);
        $result->count = self::getListCountNode($response);
        foreach ($responseItems as $item) {
            $result->items[] = self::mapToObject($item, get_called_class());
        }
        if (self::isValidCache($cacheTtl)) {
            self::getConfig()->getCacheAdapter()->set($url, $result, $cacheTtl);
        }
        return $result;
    }

    protected function assignPropertyValues($values, $fields) {
        $relations = $this->relations();
        foreach ($fields as $key => $value) {
            $this->{$key} = isset($values->{$key}) ? $values->{$key} : null;
            if (isset($values->{$key})) {
                switch ($fields[$key]['type']) {
                    case 'DateTime' :
                        {
                            if (array_key_exists('format', $fields[$key])) {
                                $date = new \DateTime();
                                $date->setTimestamp($values->{$key});
                                $this->{$key} = $date;
                            } else {
                                $this->{$key} = new \DateTime($values->{$key});
                            }
                        }
                        ;
                        break;
                    default :
                        $this->{$key} = $values->{$key};
                        break;
                }
            } else {
                $this->assignEmptyFieldValue($key, $fields[$key]['type']);
            }
        }
    }

    protected function assignRelations($values, $relations) {
        foreach ($relations as $key => $value) {
            if (isset($values->{$key})) {
                if ($value['type'] == self::HAS_MANY) {
                    $obj->$key = array();
                    foreach ($values->{$key} as $childObject) {
                        $obj->{$key}[] = self::mapToObject($childObject, $relations[$key]['class']);
                    }
                } else {
                    $this->{$key} = self::mapToObject($values->{$key}, $value['class']);
                }
            }
        }
    }

    protected function assignEmptyFieldValue($fieldName, $type) {
        if ($type == 'integer')
            $this->{$fieldName} = 0;
        else
            $this->{$fieldName} = null;
    }

    protected static function mapToObject($json_obj, $class) {
        return new $class($json_obj);
    }

    public function fields() {
        return array();
    }

    public function relations() {
        return array();
    }

    public static function getSingleItemNode($response) {
        $result = null;
        if (static::$singleItemNode)
            $result = $response->{static::$singleItemNode};
        else if (self::getConfig()->getSingleItemNode())
            $result = $response->{self::getConfig()->getSingleItemNode()};
        else
            $result = $response;
        return is_array($result) ? $result[0] : $result;
    }

    public static function getListItemNode($response) {
        $result = null;
        if (static::$listItemNode)
            $result = $response->{static::$listItemNode};
        else if (self::getConfig()->getListItemNode())
            $result = $response->{self::getConfig()->getListItemNode()};
        else
            $result = $response;
        return $result;
    }

    public function getListCountNode($response) {
        $result = 0;
        if (static::$listCountNode)
            $result = $response->{static::$listCountNode};
        else if (self::getConfig()->getListCountNode())
            $result = $response->{self::getConfig()->getListCountNode()};
        return $result;
    }

    public static function isCacheEnabled() {
        if(self::$isCacheEnabled === null){
            $parents = class_implements(get_class(self::getConfig()->getCacheAdapter()));
            return self::$isCacheEnabled = isset($parents['TRest\Cache\TRestCacheAdapterInterface']);
        }
        return self::$isCacheEnabled;
    }
    
    public static function isValidCache($cacheTtl){
        return ( self::isCacheEnabled() && ($cacheTtl > 0) );
    }

    public function __construct($values = null) {
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
}
