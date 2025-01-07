<?php
namespace Majie721\CustomRedisQueue\Process;

use support\Container;
use Majie721\CustomRedisQueue\Client;

class Consumer
{
    /**
     * @var string
     */
    protected $_consumerDir = '';

    /**
     * @var array
     */
    protected $_consumers = [];

    /**
     * StompConsumer constructor.
     * @param string $consumer_dir
     */
    public function __construct($consumer_dir = '')
    {
        $this->_consumerDir = $consumer_dir;
    }

    /**
     * onWorkerStart.
     */
    public function onWorkerStart()
    {
        if (!is_dir($this->_consumerDir)) {
            echo "Consumer directory {$this->_consumerDir} not exists\r\n";
            return;
        }
        $dir_iterator = new \RecursiveDirectoryIterator($this->_consumerDir);
        $iterator = new \RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            if (is_dir($file)) {
                continue;
            }
            $fileinfo = new \SplFileInfo($file);
            $ext = $fileinfo->getExtension();
            if ($ext === 'php') {
                $class = str_replace('/', "\\", substr(substr($file, strlen(base_path())), 0, -4));
                if (is_a($class, 'Majie721\CustomRedisQueue\WbConsumer', true)) {
                    $consumer = Container::get($class);
                    $connection_name = $class::$connection ?? 'default';
                    $queue = $class::getQueueName();
                    if (!$queue) {
                        echo "Consumer {$class} queue not exists\r\n";
                        continue;
                    }
                    $this->_consumers[$queue] = $consumer;
                    $connection = Client::connection($connection_name);
                    $connection->subscribe($queue, [$consumer, 'consume']);
                    if (method_exists($connection, 'onConsumeFailure')) {
                        $connection->onConsumeFailure(function ($exeption, $package) {
                            $consumer = $this->_consumers[$package['queue']] ?? null;
                            if ($consumer && method_exists($consumer, 'onConsumeFailure')) {
                                return call_user_func([$consumer, 'onConsumeFailure'], $exeption, $package);
                            }
                        });
                    }
                }
            }
        }
    }
}
