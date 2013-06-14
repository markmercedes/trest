<?php

abstract class TRestModel {

    const BELONGS_TO = 'RBelongsToRelation';

    const HAS_ONE = 'RHasOneRelation';

    const HAS_MANY = 'RHasManyRelation';

    protected static $resource;

    private static $requestClient;

    protected static $singleItemNode;
    
    protected static $listItemNode;

    protected static $configName = 'default';

    protected static function getConfig() {
        return TRestConfigFactory::get(static::$configName);
    }

    protected static function getRequestClient() {
        return self::$requestClient ? self::$requestClient : self::$requestClient = new TRestClient();
    }

    public static function find($id, $params = array()) {
        $request = (new TRestRequest())->setUrl(self::getConfig()->getApiUrl())->setResource(static::$resource)->setPath($id)->setParameters($params);
        return self::mapToObject(self::getSingleItemNode(self::getRequestClient()->get($request)), get_called_class());
    }

    public static function findAll($limit = 0, $page = 0, $params = array()) {
        $request = (new TRestRequest())->setUrl(self::getConfig()->getApiUrl())->setResource(static::$resource);
        if ($limit)
            $request->setParameter('limit', $limit);
        if ($page)
            $request->setParameter('page', $page);
        $result = array();
        $items = self::getListItemNode(self::getRequestClient()->get($request));
        foreach ($items as $item) {
            $result[] = self::mapToObject($item, get_called_class());
        }
        return $result;
    }

    protected static function mapToObject($json_obj, $class) {
        $obj = new $class();
        $fields = $obj->fields();
        $relations = $obj->relations();
        foreach ($json_obj as $key => $value) {
            if (array_key_exists($key, $fields)) {
                switch ($fields[$key]['type']) {
                    case 'DateTime' : {
                        if(array_key_exists('format', $fields[$key])){
                            $date = new DateTime();
                            $date->setTimestamp($value);
                            $obj->$key = $date;
                        } else {
                            $obj->$key = new DateTime($value);
                        }
                    }; break;
                    default :
                        $obj->$key = $value;
                        break;
                }
            } else if (array_key_exists($key, $relations)) {
                if ($relations[$key]['type'] == self::HAS_MANY) {
                    $obj->$key = array();
                    foreach ($value as $childObject) {
                        $obj->{$key}[] = self::mapToObject($childObject, $relations[$key]['class']);
                    }
                } else {
                    // Default relation => HAS_ONE
                    $obj->$key = self::mapToObject($value, $relations[$key]['class']);
                }
            }
        }
        return $obj;
    }

    public function fields() {
        return array();
    }

    public function relations() {
        return array();
    }

    public function getSingleItemNode($response) {
        $result = null;
        if (static::$singleItemNode)
            $result = $response->{static::$singleItemNode};
        else if (self::getConfig()->getSingleItemNode())
            $result = $response->{self::getConfig()->getSingleItemNode()};
        else
            $result = $response;
        return is_array($result) ? $result[0] : $result;
    }
    
    public function getListItemNode($response) {
        $result = null;
        if (static::$listItemNode)
            $result = $response->{static::$listItemNode};
        else if (self::getConfig()->getListItemNode())
            $result = $response->{self::getConfig()->getListItemNode()};
        else
            $result = $response;
        return $result;
    }
    

    public function __construct() {
        $fields = $this->fields();
        foreach ($fields as $key => $value) {
            if ($value['type'] == 'integer')
                $this->{$key} = 0;
            else
                $this->{$key} = null;
        }
    }
}

?>