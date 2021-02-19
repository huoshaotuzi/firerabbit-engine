<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/14
 * Time：13:02
 **/


namespace FireRabbit\Engine\Database;


class Manager
{
    protected static $config;

    public static function setConfig($config)
    {
        $db = new \Illuminate\Database\Capsule\Manager();
        $db->addConnection($config);
        $db->setAsGlobal();
        $db->bootEloquent();
    }

    public static function getConfig()
    {
        return self::$config;
    }

}
