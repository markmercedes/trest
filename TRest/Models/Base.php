<?php

/**
 *
 * Base model classes to decouple logic from the {@link TRestModel} class
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Models
 */
namespace TRest\Models;

use TRest\Http\Client;



/**
 *
 * Base model class, the father of the models
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Models
 */

/**
 *
 * Base model class wich holds the information of the client used to perform
 * HTTP. It's the responsable of assign values to the attributes
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Models
 */
abstract class Base extends ModelMapper {

    /**
     *
     * @var \TRest\Http\Client
     */
    protected static $requestClient;

    /**
     *
     * returns the active {@link \TRest\Http\Client} used to perform
     * requests
     *
     * @return \TRest\Http\Client
     */
    protected static function getRequestClient() {
        return self::$requestClient ? self::$requestClient : self::$requestClient = new Client();
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
                    case '\DateTime' :
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
        if ($type == 'integer'){
            $this->{$fieldName} = 0;
        } else {
            $this->{$fieldName} = null;
        }
    }
}
