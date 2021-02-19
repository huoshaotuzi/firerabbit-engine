<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/13
 * Time：22:53
 **/


namespace FireRabbit\Module\Http\Middleware;


class Kernel
{
    /**
     * 实例化的中间件
     *
     * @var [Middleware]
     */
    protected static $instances;

    protected static $middlewares = [];

    /**
     * 读取配置文件
     * @param $middlewares
     */
    public static function setConfig($middlewares)
    {
        self::$middlewares = $middlewares;
    }

    public static function getMiddlewareInstance($name)
    {
        // 从已实例化的对象数组中取
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }

        // 未实例化的创建新对象
        $middlewareName = self::$middlewares[$name] ?? null;

        if ($middlewareName == null) {
            self::$instances[$name] = null;
        } else {
            self::$instances[$name] = new $middlewareName;
        }

        return self::$instances[$name];
    }
}
