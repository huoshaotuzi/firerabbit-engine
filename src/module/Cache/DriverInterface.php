<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/18
 * Time：10:54
 **/


namespace FireRabbit\Module\Cache;


use Closure;

interface DriverInterface
{
    /**
     * 载入参数
     * @param $config
     * @return mixed
     */
    public function load($config);

    /**
     * 含有过期时间的键值对
     * @param string $key
     * @param int $ttl
     * @param Closure $initFun
     * @return string
     */
    public function remember(string $key, int $ttl, Closure $initFun): string;

    /**
     * 没有过期时间的键值对
     * @param string $key
     * @param Closure $initFun
     * @return string
     */
    public function rememberForever(string $key, Closure $initFun): string;
}
