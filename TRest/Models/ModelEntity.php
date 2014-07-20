<?php

/**
 *
 * Base model classes to decouple logic from the {@link TRestModel} class
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Models
 */
namespace TRest\Models;

use TRest\Config\ConfigFactory;

abstract class ModelEntity {

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
            return self::$isCacheEnabled = isset($parents['TRest\Cache\CacheAdapterInterface']);
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
     * @return Config
     */
    protected static function getConfig() {
        return ConfigFactory::get(static::$configName);
    }

    /**
     * this function should be overwritten in the case you want to execute any
     * code after the construction of the model, this function is invoked after
     * construct
     */
    public function build() {
    }
}
