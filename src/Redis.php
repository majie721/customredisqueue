<?php

namespace Majie721\CustomRedisQueue;

use Workerman\Timer;


/**
 * Class Redis
 * @package Majie721\CustomRedisQueue
 * @method static bool send(WbConsumer $consumer, array $data, int $delay=0)
 */
class Redis
{
    /**
     * @var RedisConnection[]
     */
    protected static $_connections = [];

    /**
     * @param string $name
     * @return RedisConnection
     */
    public static function connection($name = 'default') {
        if (!isset(static::$_connections[$name])) {
            $configs = config('custom_redis_queue', config('plugin.majie721.customredisqueue.redis', []));
            if (!isset($configs[$name])) {
                throw new \RuntimeException("RedisQueue connection $name not found");
            }
            $config = $configs[$name];
            static::$_connections[$name] = static::connect($config);
        }
        return static::$_connections[$name];
    }

    protected static function connect($config)
    {
        if (!extension_loaded('redis')) {
            throw new \RuntimeException('Please make sure the PHP Redis extension is installed and enabled.');
        }

        $redis = new RedisConnection();
        $redis->connectWithConfig($config);
        return $redis;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::connection('default')->{$name}(... $arguments);
    }
}
