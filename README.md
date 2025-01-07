# customredisqueue
webman 自定义redis队列

## 功能特点

- 基于 webman 官方 redis 队列更改
- 队列的最大尝试次数和重试间隔时间再消费者中定义,不是统一在配置文件中定义



## 配置说明

在 `config/plugin/vendor/customredisqueue/redis.php` 中配置 Redis 连接信息

## 生成消费者命令

```
php webman custom-redis-queue:consumer SendTest
```

## 生成消费者属性说明
```
    // 要消费的队列名
    protected static $queue_name = 'send-test';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public static string $connection = 'default';

    // 最大尝试次数
    public static int $max_attempts = 5;

    // 重试间隔时间(秒)
    public static int $retry_seconds = 10;
 ```   
- 重试间隔时间 = 重试次数 * 重试间隔时间

