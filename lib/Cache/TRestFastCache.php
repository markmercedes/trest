<?php

namespace TRest\Cache;

class TRestFastCache implements TRestCacheAdapterInterface {

    public function __construct() {
        if (! class_exists('phpFastCache')) {
            require_once (TREST_LIB_THIRD_PARTY_PATH . DIRECTORY_SEPARATOR . 'Caching' . DIRECTORY_SEPARATOR . 'phpfastcache' . DIRECTORY_SEPARATOR . 'php_fast_cache.php');
        }
        \phpFastCache::$storage = "apc";
    }

    public function delete($key) {
        \phpFastCache::delete($key);
    }

    public function exists($key) {
        return (boolean) (\phpFastCache::get($key));
    }

    public function get($key) {
        return \phpFastCache::get($key);
    }

    public function set($key, $data, $ttl = TREST_DEFAULT_CACHE_TTL) {
        \phpFastCache::set($key, $data, $ttl);
        return $this;
    }
}
