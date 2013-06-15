<?php

namespace TRest\Cache;

class TRestFastCache implements TRestCacheAdapterInterface {

    public function __construct() {
        if(!class_exists('phpFastCache')){
            require_once (TREST_LIB_THIRD_PARTY_PATH . DIRECTORY_SEPARATOR . 'Caching' . DIRECTORY_SEPARATOR . 'phpfastcache' . DIRECTORY_SEPARATOR . 'php_fast_cache.php');
        }
        \phpFastCache::$storage = "apc";
    }

    public function delete($key) {
        \phpFastCache::delete(md5($key));
    }

    public function exists($key) {
        // TODO Auto-generated method stub
        return (boolean)(\phpFastCache::get(md5($key)));
    }

    public function get($key) {
        // TODO Auto-generated method stub
        return \phpFastCache::get(md5($key));
    }

    public function set($key, $data, $ttl = TREST_DEFAULT_CACHE_TTL) {
        // TODO Auto-generated method stub
        \phpFastCache::set(md5($key), $data, $ttl);
        return $this;
    }
}
