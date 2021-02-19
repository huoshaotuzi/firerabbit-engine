<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/14
 * Time：13:38
 **/


namespace FireRabbit\Module\Logger;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;

class Log
{
    protected static $path;
    protected static $level;
    protected static $channel;

    /**
     * 日志对象实例
     * @var Logger
     */
    protected static $instance = null;

    public static function setConfig($config)
    {
        self::$path = $config['path'];
        self::$channel = $config['channel'];

        switch ($config['level']) {
            case 'info':
                self::$level = Logger::INFO;
                break;
            case 'warning':
                self::$level = Logger::WARNING;
                break;
            case 'error':
                self::$level = Logger::ERROR;
                break;
            case 'alert':
                self::$level = Logger::ALERT;
                break;
        }
    }

    public static function getLogger()
    {
        if (self::$instance == null) {
            self::$instance = new Logger(self::$config['channel']);

            if (!file_exists(self::$config['path'])) {
                $file = fopen(self::$config['path'], 'w');
                fwrite($file, '');
                fclose($file);
            }

            $streamHandler = new StreamHandler(self::$config['path'], self::$config['level']);
//            $streamHandler->setFormatter(new JsonFormatter());

            self::$instance->pushHandler($streamHandler);
        }

        return self::$instance;
    }
}
