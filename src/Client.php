<?php

namespace Majie721\CustomRedisQueue;

use support\Log;

class Client
{
    /**
     * @var
     */
    protected static $_connections = null;

    /**
     * @param string $name
     * @return RedisClient
     */
    public static function connection($name = 'default')
    {
        if (!isset(static::$_connections[$name])) {
            $config = config('redis_queue', config('plugin.majie721.customredisqueue.redis', []));
            if (!isset($config[$name])) {
                throw new \RuntimeException("RedisQueue connection $name not found");
            }
            $host = "redis://{$config[$name]['host']}:{$config[$name]['port']}";
            $options = [
                'auth'   => $config[$name]['auth'] ?? '',
                'db'     => $config[$name]['db'] ?? 0,
                'prefix' => $config[$name]['prefix'] ?? '',
            ];
            $client = new RedisClient($host, $options);
            if (method_exists($client, 'logger')) {
                $client->logger(Log::channel('plugin.majie721.customredisqueue.default'));
            }
            static::$_connections[$name] = $client;
        }
        return static::$_connections[$name];
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
