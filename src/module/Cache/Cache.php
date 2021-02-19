<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/18
 * Time：10:53
 **/


namespace FireRabbit\Module\Cache;


use FireRabbit\Module\Cache\Driver\RedisDriver;

class Cache
{
    protected static DriverInterface $driver;

    public static function setConfig($cache, $config)
    {
        switch ($cache) {
            case 'redis':
                self::redisDriver($config);
                break;
        }
    }

    protected static function redisDriver($config)
    {
        self::$driver = new RedisDriver();
        self::$driver->load($config);
    }

    public static function driver(): DriverInterface
    {
        return self::$driver;
    }
}
