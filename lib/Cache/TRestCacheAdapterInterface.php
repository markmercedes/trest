<?php

/**
 * Interface that should be implemented by the cache adapters
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Cache
 */
namespace TRest\Cache;

interface TRestCacheAdapterInterface {

    /**
     * 
     * returns a cached value
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     *
     * Adds information to the cache
     *
     * @param string $key            
     * @param any $data            
     * @param integer $ttl  
     * @return TRestCacheAdapter
     */
    public function set($key, $data, $ttl = TREST_DEFAULT_CACHE_TTL);

    /**
     * 
     * says if a kay has already been cached
     * 
     * @param string $key
     * @return boolean
     */
    public function exists($key);

    
    /**
     * 
     * Deletes a key from the cache
     * 
     * @param string $key
     */
    public function delete($key);
}
