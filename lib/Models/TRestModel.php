<?php

namespace TRest\Models;

use TRest\Http\TRestRequest;

abstract class TRestModel extends TRestModelBase {

    public static function find($id, $params = array(), $cacheTtl = TREST_DEFAULT_CACHE_TTL) {
        $request = (new TRestRequest())->setUrl(self::getConfig()->getApiUrl())->setResource(static::$resource)->setPath($id)->setParameters($params);
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

    public static function findAll($limit = 0, $page = 0, $params = array(), $cacheTtl = TREST_DEFAULT_CACHE_TTL) {
        $request = (new TRestRequest())->setUrl(self::getConfig()->getApiUrl())->setResource(static::$resource)->setParameters($params)->setParameter('limit', $limit)->setParameter('page', $page);
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
