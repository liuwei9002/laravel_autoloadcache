<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/24
 * Time: 15:25
 */
namespace Suyain\Stores;

use Suyain\Contracts\Store;
use Suyain\Stores\BaseStore;

class RedisStore extends BaseStore implements Store
{
    private static $redis;

    public function __construct($config)
    {
        $connection = $this->getConnection(array_get($config, 'connection', 'default'));
        if (self::$redis == null) {
            if (array_get($connection, 'cluster')) { // 集群
                $this->createClusterObject($connection);
            } else {
                $this->createSimpleObejct($connection);
            }
        }
    }

    public function get($key, $isAutoLoad = true, $waitTime = 0)
    {
        if ($isAutoLoad) {
            return $this->getAutoLoad($key, $waitTime, debug_backtrace());
        }
        return self::$redis->get($key);
    }

    public function put($key, $value, $expir_time = 60)
    {
        self::$redis->set($key, $value, $expir_time);
    }

    public function ttl($key)
    {
        return self::$redis->ttl($key);
    }

    public function exist($key)
    {
        return self::$redis->exists($key);
    }

    public function getLock($key)
    {
        return self::$redis->setnx($key, 1);
    }

    public function unlock($key)
    {
        return self::$redis->del($key);
    }

    public function incr($key)
    {
        self::$redis->incr($key);
    }

    /**
     * create cluster redis object
     *
     * @param $connects
     */
    private function createClusterObject($connections)
    {
         $connectionHost = [];
         foreach ($connections as $connection) {
             $connectionHost[] = array_get($connection, 'host', '') . ":" . array_get($connection, 'host', '');
         }
        self::$redis = new \RedisCluster(null, $connectionHost);
    }

    /**
     * create simple redis object
     *
     * @param $connect
     */
    private function createSimpleObejct($connection)
    {
        self::$redis = new \Redis();
        self::$redis->connect(array_get($connection, 'host', ''), array_get($connection, 'port', ''));
    }

    /**
     * get connection info
     *
     * @param $connectionType
     * @return mixed
     */
    private function getConnection($connectionType)
    {
        return \Config::get("database.redis.$connectionType");
    }

}