<?php

namespace TRest\Models;

/**
 *
 * This class contains the methods to map plain objetcs to {@link TRestModel}
 * instances and viceversa
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Models
 */
abstract class ModelMapper extends ModelEntity {

    /**
     *
     * @param object $json_obj
     * @param string $class
     *            classname
     * @return TRest\Models\TRestModel
     */
    protected static function mapToObject($json_obj, $class) {
        if (! $json_obj){
            return null;
        }
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