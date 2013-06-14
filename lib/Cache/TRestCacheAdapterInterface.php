<?php

/**
 * Interface that should be implemented by the cache adapters
 *
 * @author    Marcos Mercedes <marcos.mercedesn@gmail.com>
 */

namespace TRest\Cache;

interface TRestCacheAdapterInterface {
    
    public function get($key);
    
    public function set($key, $data, $ttl);
    
    public function exists($key);
    
    public function delete($key);
    
}
