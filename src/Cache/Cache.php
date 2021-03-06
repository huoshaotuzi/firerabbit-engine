<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/18
 * Time：10:53
 **/


namespace FireRabbit\Engine\Cache;


use FireRabbit\Engine\Cache\Driver\RedisDriver;

class Cache
{
    protected static $cacheType;
    protected static $config;

    public static function setConfig($cacheType, $config)
    {
        self::$cacheType = $cacheType;
        self::$config = $config;
    }

    protected static function redisDriver($config)
    {
        $driver = new RedisDriver();
        $driver->load($config);
        return $driver;
    }

    public static function driver(): DriverInterface
    {
        $instance = null;

        switch (self::$cacheType) {
            case 'redis':
                $instance = self::redisDriver(self::$config);
                break;
        }

        return $instance;
    }
}
