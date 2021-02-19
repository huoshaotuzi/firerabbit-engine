<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/18
 * Time：10:53
 **/


namespace FireRabbit\Engine\Cache\Driver;


use Closure;
use FireRabbit\Engine\Cache\DriverInterface;

class RedisDriver implements DriverInterface
{
    protected $instance;

    public function load($config)
    {
        $this->instance = new \Redis();
        $this->instance->connect($config['host'], $config['port']);
        $this->instance->auth($config['password']);
    }

    public function remember($key, int $ttl, Closure $initFun): string
    {
        $value = $this->instance->get($key);

        if ($value !== false) {
            return $value;
        }

        $value = $initFun();
        $this->instance->setEx($key, $ttl, $value);

        return $value;
    }

    public function rememberForever($key, Closure $initFun): string
    {
        $value = $this->instance->get($key);

        if ($value !== false) {
            return $value;
        }

        $value = $initFun();
        $this->instance->set($key, $value);

        return $value;
    }
}
