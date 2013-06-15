<?php

namespace TRest\Models;

use TRest\Config\TRestConfigFactory;
use TRest\Config\TRestConfig;
use TRest\Http\TRestClient;

abstract class TRestModelBase extends TRestModelEntity {

    protected static $requestClient;

    protected static function getRequestClient() {
        return self::$requestClient ? self::$requestClient : self::$requestClient = new TRestClient();
    }

    protected function assignPropertyValues($values, $fields) {
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
                $this->assignEmptyPropertyValue($key, $fields[$key]['type']);
            }
        }
    }

    protected function assignRelations($values, $relations) {
        foreach ($relations as $key => $value) {
            if (isset($values->{$key})) {
                if ($value['type'] == self::HAS_MANY) {
                    $this->{$key} = array();
                    foreach ($values->{$key} as $childObject) {
                        $this->{$key}[] = self::mapToObject($childObject, $relations[$key]['class']);
                    }
                } else {
                    $this->{$key} = self::mapToObject($values->{$key}, $value['class']);
                }
            }
        }
    }

    protected function assignEmptyPropertyValue($fieldName, $type) {
        if ($type == 'integer')
            $this->{$fieldName} = 0;
        else
            $this->{$fieldName} = null;
    }

    protected static function mapToObject($json_obj, $class) {
        if (! $json_obj)
            return null;
        return new $class($json_obj);
    }
}

abstract class TRestModelEntity {

    const BELONGS_TO = 'RBelongsToRelation';

    const HAS_ONE = 'RHasOneRelation';

    const HAS_MANY = 'RHasManyRelation';

    protected static $configName = 'default';

    protected static $resource;

    protected static $singleItemNode;

    protected static $listItemNode;

    protected static $listCountNode;

    protected static $isCacheEnabled;

    public function fields() {
        return array();
    }

    public function relations() {
        return array();
    }

    public static function isCacheEnabled() {
        if (self::$isCacheEnabled === null) {
            $parents = class_implements(get_class(self::getConfig()->getCacheAdapter()));
            return self::$isCacheEnabled = isset($parents['TRest\Cache\TRestCacheAdapterInterface']);
        }
        return self::$isCacheEnabled;
    }

    public static function isValidCache($cacheTtl) {
        return (self::isCacheEnabled() && ($cacheTtl > 0));
    }

    public static function getSingleItemNode($response) {
        if (! $response)
            return null;
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

    /**
     *
     * @return TRestConfig
     */
    protected static function getConfig() {
        return TRestConfigFactory::get(static::$configName);
    }
}
