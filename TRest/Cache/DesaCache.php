<?php

/**
 * Implementacion of {@link CacheAdapterInterface} to use this library
 * caching capabilities with the Third Party Library {@link \phpFastCache}
 * {@link https://github.com/khoaofgod/phpfastcache}
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Cache
 */
namespace TRest\Cache;

use Desarrolla2\Cache\Cache;

class DesaCache implements CacheAdapterInterface {

    private $cache = null;
    
    /**
     * Loads the fast cache Third Party library
     */
    public function __construct($adapter) {
        $this->cache = new Cache($adapter);
    }

    /**
     * (non-PHPdoc)
     * @see \TRest\Cache\CacheAdapterInterface::delete()
     */
    public function delete($key) {
        $this->cache->delete(__CLASS__ . $key);
    }

    /**
     * (non-PHPdoc)
     * @see \TRest\Cache\CacheAdapterInterface::exists()
     */
    public function exists($key) {
        return $this->cache->has(__CLASS__ . $key);
    }

    /**
     * (non-PHPdoc)
     * @see \TRest\Cache\CacheAdapterInterface::get()
     */
    public function get($key) {
        return $this->cache->get(__CLASS__ . $key);
    }
    
    /**
     * (non-PHPdoc)
     * @see \TRest\Cache\CacheAdapterInterface::set()
     */
    public function set($key, $data, $ttl = TREST_DEFAULT_CACHE_TTL) {
        $this->cache->set(__CLASS__ . $key, $data, $ttl);
        return $this;
    }
}
