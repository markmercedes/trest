<?php

/**
 *
 * Base model classes to decouple logic from the {@link TRestModel} class
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Models
 */
namespace TRest\Models;

use TRest\Config\TRestConfigFactory;
use TRest\Config\TRestConfig;
use TRest\Http\TRestClient;

/**
 *
 * This class contains the methods to map plain objetcs to {@link TRestModel}
 * instances and viceversa
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Models
 */
abstract class TRestModelMapper extends TRestModelEntity {

    /**
     *
     * @param object $json_obj            
     * @param string $class
     *            classname
     * @return TRest\Models\TRestModel
     */
    protected static function mapToObject($json_obj, $class) {
        if (! $json_obj)
            return null;
        return new $class($json_obj);
    }

    /**
     * converts $this to a valid it's JSON representation
     *
     * @return \stdClass
     */
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

    /**
     *
     * Adds the relations to a JSON representation of $this
     *
     * @param \stdClass $jsonObj            
     * @param array $relations            
     * @return \stdClass
     */
    public function mapRelationsToJSON($jsonObj, $relations) {
        foreach ($relations as $relationName => $relationData) {
            if (@$relationData['postOnSave']) {
                $postedPropertyName = $relationName . (@$relationData['postSuffix'] ? @$relationData['postSuffix'] : '');
                $jsonObj = $this->mapRelationToJSON($jsonObj, $relationName, $postedPropertyName, $relationData);
            }
        }
        return $jsonObj;
    }

    /**
     *
     * Maps a single relations to it's JSON representation
     *
     * @param \stdClass $jsonObj            
     * @param string $relationName            
     * @param string $postedPropertyName            
     * @param string $relationData            
     * @return \stdClass
     */
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

    /**
     *
     * Maps a self::HAS_MANY relation to it's JSON representation
     *
     * @param \stdClass $jsonObj            
     * @param string $postedPropertyName            
     * @param string $relationProperty            
     * @return \stdClass
     */
    private function mapHasManyRelationToJson($jsonObj, $postedPropertyName, $relationProperty) {
        $jsonObj->{$postedPropertyName} = array();
        foreach ($relationProperty as $relationPropertyItem) {
            $jsonObj->{$postedPropertyName}[] = $relationPropertyItem->mapToJSON();
        }
        return $jsonObj;
    }
}

/**
 *
 * Base model class, the father of the models
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Models
 */
abstract class TRestModelEntity {

    const BELONGS_TO = 'RBelongsToRelation';

    const HAS_ONE = 'RHasOneRelation';

    const HAS_MANY = 'RHasManyRelation';

    /**
     *
     * name of the config that will be used for this model
     *
     * @var string
     */
    protected static $configName = 'default';

    /**
     *
     * The name of the resource that will be handled by this model
     *
     * @var string
     */
    protected static $resource;

    /**
     *
     * if single items are returned inside a node here should go the name of
     * that node
     *
     * @var string
     */
    protected static $singleItemNode;

    /**
     *
     * the name of the node that contains the list of items
     *
     * @var string
     */
    protected static $listItemNode;

    /**
     *
     * The name of the node that contains the total count of items retreived
     *
     * @var string
     */
    protected static $listCountNode;

    /**
     *
     * holds the information about the cache status
     *
     * @var boolean
     */
    protected static $isCacheEnabled;

    /**
     *
     * The name of the property that will be used as primaryKey
     *
     * @var string
     */
    protected static $recordId = 'id';

    /**
     *
     * An array of the fields that should be present in the model return array(
     * 'id' => array( 'type' => 'integer' ),
     *
     * @return array
     */
    public function fields() {
        return array();
    }

    /**
     *
     * Available relations for this model return array( 'ingredients' => array(
     * 'class' => 'Ingredient', 'type' => [self::HAS_MANY, self::HAS_ONE,
     * self::BELONGS_TO 'postOnSave' => true, //If this relation should be
     * included in the save method 'postSuffix' => '_attributes' //Adds a
     * postSuffix to the name of the relation when posted, i.e:
     * ingredients_attributes ) );
     *
     * @return array
     */
    public function relations() {
        return array();
    }

    /**
     *
     * Says if caching functionallity is enabled
     *
     * @return boolean
     */
    public static function isCacheEnabled() {
        if (self::$isCacheEnabled === null) {
            $parents = class_implements(get_class(self::getConfig()->getCacheAdapter()));
            return self::$isCacheEnabled = isset($parents['TRest\Cache\TRestCacheAdapterInterface']);
        }
        return self::$isCacheEnabled;
    }

    /**
     *
     * returns if a number of seconds is a valid number to be cached
     *
     * @param number $cacheTtl            
     * @return boolean
     */
    public static function isValidCache($cacheTtl) {
        return (self::isCacheEnabled() && ($cacheTtl > 0));
    }

    /**
     *
     * @see TRest\Models\TRestModelEntity::$singleItemNode
     * @param string $response            
     * @return string
     */
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

    /**
     *
     * @see TRest\Models\TRestModelEntity::$listItemNode
     * @param string $response            
     * @return string
     */
    public static function getListItemNode($response, $itemNode = 'NONE') {
        if(($itemNode != 'NONE')){
            if(!$itemNode)
                return $response;
            return $response->{$itemNode};
        }
        $result = null;
        if (static::$listItemNode)
            $result = $response->{static::$listItemNode};
        else if (self::getConfig()->getListItemNode())
            $result = $response->{self::getConfig()->getListItemNode()};
        else
            $result = $response;
        return $result;
    }

    /**
     *
     * @see TRest\Models\TRestModelEntity::$countItemNode
     * @param string $response            
     * @return string
     */
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

    /**
     * this function should be overwritten in the case you want to execute any
     * code after the construction of the model, this function is invoked after
     * construct
     */
    public function build() {
    }
}

/**
 *
 * Base model class wich holds the information of the client used to perform
 * HTTP. It's the responsable of assign values to the attributes
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Models
 */
abstract class TRestModelBase extends TRestModelMapper {

    /**
     *
     * @var \TRest\Http\TRestClient
     */
    protected static $requestClient;

    /**
     *
     * returns the active {@link \TRest\Http\TRestClient} used to perform
     * requests
     *
     * @return \TRest\Http\TRestClient
     */
    protected static function getRequestClient() {
        return self::$requestClient ? self::$requestClient : self::$requestClient = new TRestClient();
    }

    /**
     *
     * assigns values to model attributes
     *
     * @param object $values            
     * @param array $fields            
     */
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

    /**
     *
     * assigns values to attributes that are supposed relations
     *
     * @param object $values            
     * @param array $relations            
     */
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

    /**
     * Assigns the default value to a field
     *
     * @param string $fieldName            
     * @param string $type            
     */
    protected function assignEmptyPropertyValue($fieldName, $type) {
        if ($type == 'integer')
            $this->{$fieldName} = 0;
        else
            $this->{$fieldName} = null;
    }
}
