<?php

/**
 * Interface that should be implemented by the cache adapters
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 */
namespace TRest\Cache;

interface TRestCacheAdapterInterface {

    public function get($key);

    /**
     *
     * @param string $key            
     * @param any $data            
     * @param integer $ttl            
     * @return TRestCacheAdapter
     */
    public function set($key, $data, $ttl = TREST_DEFAULT_CACHE_TTL);

    public function exists($key);

    public function delete($key);
}
