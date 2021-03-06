<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/18
 * Time：10:53
 **/


namespace FireRabbit\Engine\Cache;


use FireRabbit\Engine\Cache\Driver\RedisDriver;
use FireRabbit\Engine\Logger\Log;

class Cache
{
    protected static $cacheType;
    protected static $config;
    protected static $driver;

    public static function setConfig($cacheType, $config)
    {
        self::$cacheType = $cacheType;
        self::$config = $config;
    }

    protected static function initInstance()
    {
        switch (self::$cacheType) {
            case 'redis':
                self::redisDriver(self::$config);
                break;
        }
    }

    protected
    static function redisDriver($config)
    {
        self::$driver = new RedisDriver();
        self::$driver->load($config);
    }

    public static function driver(): DriverInterface
    {
        try {
            // 获取实例前先检测是否连接正常
            if (isset(self::$driver) && self::$driver->ping()) {
                return self::$driver;
            }
        } catch (\Exception $exception) {
            Log::getLogger()->error('cache:error:' . $exception->getMessage());
        }

        // 重新实例化对象
        self::initInstance();

        return self::$driver;
    }
}
