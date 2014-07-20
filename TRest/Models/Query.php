<?php
/**
 * 
 */

namespace TRest\Models;

/**
 * @author marcosmercedesnunez
 *
 */
class Query {

    /**
     * @var string
     */
    protected $path = null;

    /**
     * @var string
     */
    protected $resource = null;

    /**
     * @var int
     */
    protected $cacheTtl = TREST_DEFAULT_CACHE_TTL;

    /**
     * @var array
     */
    protected $query = array();

    public function __call($method, $arguments){
        array_unshift($arguments, $this);
        return call_user_func_array(array($this->instance, 'scope' . ($method)), $arguments);
    }

    
    /**
     * @param \TRest\Models\Model $instance
     */
    public function __construct($instance){
        $this->instance = $instance;
    }

    /**
     * @param string $key
     * @param string $value
     * @return \TRest\Models\Query
     */
    public function where($key, $value){
        $this->query[$key] = $value;
        return $this;
    }

    /**
     * @return array<\TRest\Models\Model> 
     */
    public function all(){
        $klass = get_class($this->instance);
        return $klass::findByQuery($this);
    }

    /**
     * @param $id
     * @return \TRest\Models\Model
     */
    public function findOne($id){
        $klass = get_class($this->instance);
        return $klass::findOneByQuery($id, $this);
    }

    
    /**
     * @return array
     */
    public function toParams(){
        return $this->query;
    }

    /**
     * @param string $path
     * @return \TRest\Models\Query
     */
    public function setPath($path){
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(){
        return $this->path;
    }

    /**
     * @param string $resource
     * @return \TRest\Models\Query
     */
    public function setResource($resource){
        $this->resource = $resource;
        return $this;
    }

    
    /**
     * @return string
     */
    public function getResource(){
        return $this->resource;
    }

    /**
     * @param int $cacheTtl
     * @return \TRest\Models\Query
     */
    public function setCacheTtl($cacheTtl){
        $this->cacheTtl = $cacheTtl;
        return $this;
    }

    /**
     * @return int
     */
    public function getCacheTtl(){
        return $this->cacheTtl;
    }
}