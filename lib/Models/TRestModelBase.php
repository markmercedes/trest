<?php

namespace TRest\Models;

use TRest\Config\TRestConfigFactory;
use TRest\Config\TRestConfig;
use TRest\Http\TRestClient;

abstract class TRestModelMapper extends TRestModelEntity {

    protected static function mapToObject($json_obj, $class) {
        if (! $json_obj)
            return null;
        return new $class($json_obj);
    }

    public function mapToJSON() {
        $jsonObj = new \stdClass();
        $fields = $this->fields();
        foreach ($fields as $key => $value) {
            /**
             * Avoid to post the record id for new fields
             */
            if ((! $this->{static::$recordId}) && $key != (string) static::$recordId) {
                $jsonObj->{$key} = $this->{$key};
            } else if ($this->{static::$recordId}) {
                $jsonObj->{$key} = $this->{$key};
            }
        }
        $relations = $this->relations();
        if (count($relations)) {
            $jsonObj = $this->mapRelationsToJSON($jsonObj, $relations);
        }
        return $jsonObj;
    }

    public function mapRelationsToJSON($jsonObj, $relations) {
        foreach ($relations as $relationName => $relationData) {
            if (@$relationData['postOnSave']) {
                $postedPropertyName = $relationName . (@$relationData['postSuffix'] ? @$relationData['postSuffix'] : '');
                $jsonObj = $this->mapRelationToJSON($jsonObj, $relationName, $postedPropertyName, $relationData);
            }
        }
        return $jsonObj;
    }

    private function mapRelationToJSON($jsonObj, $relationName, $postedPropertyName, $relationData) {
        if (isset($this->{$relationName})) {
            $relationProperty = $this->{$relationName};
            if ((@$relationData['type']) == self::HAS_MANY) {
                $jsonObj = $this->mapHasManyRelationToJson($jsonObj, $postedPropertyName, $relationProperty);
            } else if ((@$relationData['type']) == self::HAS_ONE) {
                $jsonObj->{$postedPropertyName} = $relationProperty->mapToJSON();
            }
        }
        return $jsonObj;
    }

    private function mapHasManyRelationToJson($jsonObj, $postedPropertyName, $relationProperty) {
        $jsonObj->{$postedPropertyName} = array();
        foreach ($relationProperty as $relationPropertyItem) {
            $jsonObj->{$postedPropertyName}[] = $relationPropertyItem->mapToJSON();
        }
        return $jsonObj;
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

    protected static $recordId = 'id';

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

    public static function getListCountNode($response) {
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

    public function build() {
    }
}

abstract class TRestModelBase extends TRestModelMapper {

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
}
