<?php
namespace Majie721\CustomRedisQueue;

abstract class WbConsumer implements Consumer
{
    // 要消费的队列名
    protected static $queue_name;

    // 连接名，对应 customredisqueue/redis.php 里的连接`
    public static string $connection = 'default';

    // 最大尝试次数
    public static int $max_attempts = 5;

    // 重试间隔时间(秒)
    public static int $retry_seconds = 5;


    abstract public function consume($data);


    public static function getQueueName(): string
    {
        if (!isset(static::$queue_name)) {
            throw new \RuntimeException('Queue name not set');
        }
        return static::$queue_name;
    }

}
