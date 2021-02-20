<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/18
 * Time：10:54
 **/


namespace FireRabbit\Engine\Cache;


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
     * 获取键值对缓存
     * @param string $key
     * @return string
     */
    public function get(string $key): string;

    /**
     * 设置键值对缓存（不过期）
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function set(string $key, string $value);

    /**
     * 设置带有过期时间的键值对缓存
     * @param string $key
     * @param string $value
     * @param int $ttl
     * @return mixed
     */
    public function setEx(string $key, string $value, int $ttl);

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
