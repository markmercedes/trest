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

class FastCache implements CacheAdapterInterface {

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
     * @see \TRest\Cache\CacheAdapterInterface::delete()
     */
    public function delete($key) {
        \phpFastCache::delete($key);
    }

    /**
     * (non-PHPdoc)
     * @see \TRest\Cache\CacheAdapterInterface::exists()
     */
    public function exists($key) {
        return (boolean) (\phpFastCache::get($key));
    }

    /**
     * (non-PHPdoc)
     * @see \TRest\Cache\CacheAdapterInterface::get()
     */
    public function get($key) {
        return \phpFastCache::get($key);
    }
    
    /**
     * (non-PHPdoc)
     * @see \TRest\Cache\CacheAdapterInterface::set()
     */
    public function set($key, $data, $ttl = TREST_DEFAULT_CACHE_TTL) {
        \phpFastCache::set($key, $data, $ttl);
        return $this;
    }
}
