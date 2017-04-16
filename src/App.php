<?php

namespace litepubl\core\app\App;

use litepubl\core\storage\StorageInterface;

class App
{
    use Callbacks;

    protected $cache;
    protected $classes;
    protected $db;
    protected $installed;
    protected $logFactory;
    protected $memcache;
    protected $microtime;
    protected $options;
    protected $paths;
    protected $poolStorage;
    protected $router;
    protected $site;
    protected $storage;

    public function __construct(StorageInterface $storage, StorageInterface $pool)
    {
        $this->storage = $storage;
        $this->pool = $pool;
        $this->cache = $cache;
    }

    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    public function getPool(): StorageInterface
    {
        return $this->pool;
    }
}
