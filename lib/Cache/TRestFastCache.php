<?php

/**
 * Implementacion of {@link TRestCacheAdapterInterface} to use this library
 * caching capabilities with the Third Party Library {@link \phpFastCache}
 * {@link https://github.com/khoaofgod/phpfastcache}
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Cache
 */
namespace TRest\Cache;

class TRestFastCache implements TRestCacheAdapterInterface {

    /**
     * Loads the fast cache Third Party library
     */
    public function __construct() {
        if (! class_exists('phpFastCache')) {
            require_once (TREST_LIB_THIRD_PARTY_PATH . DIRECTORY_SEPARATOR . 'Caching' . DIRECTORY_SEPARATOR . 'phpfastcache' . DIRECTORY_SEPARATOR . 'php_fast_cache.php');
        }
        \phpFastCache::$storage = "apc";
    }

    /**
     * (non-PHPdoc)
     * @see \TRest\Cache\TRestCacheAdapterInterface::delete()
     */
    public function delete($key) {
        \phpFastCache::delete($key);
    }

    /**
     * (non-PHPdoc)
     * @see \TRest\Cache\TRestCacheAdapterInterface::exists()
     */
    public function exists($key) {
        return (boolean) (\phpFastCache::get($key));
    }

    /**
     * (non-PHPdoc)
     * @see \TRest\Cache\TRestCacheAdapterInterface::get()
     */
    public function get($key) {
        return \phpFastCache::get($key);
    }
    
    /**
     * (non-PHPdoc)
     * @see \TRest\Cache\TRestCacheAdapterInterface::set()
     */
    public function set($key, $data, $ttl = TREST_DEFAULT_CACHE_TTL) {
        \phpFastCache::set($key, $data, $ttl);
        return $this;
    }
}
