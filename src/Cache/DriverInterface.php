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
     * 自增
     * @param $key
     * @param $value
     * @return mixed
     */
    public function incrBy($key, $value);

    /**
     * 自减
     * @param $key
     * @param $value
     * @return mixed
     */
    public function decrBy($key, $value);

    /**
     * 清空当前数据库
     * @return mixed
     */
    public function flushdb();

    /**
     * 判断是否存在
     * @param $key
     * @return mixed
     */
    public function exists($key);

    /**
     * 获取哈希指定字段的值
     * @param string $key
     * @param string $field
     * @return string
     */
    public function hGet(string $key, string $field): string;

    /**
     * 设置哈希指定字段的值
     * @param string $key
     * @param string $field
     * @param $value
     * @return mixed
     */
    public function hSet(string $key, string $field, $value);

    /**
     * 只有字段不存在时，设置哈希指定字段的值
     * @param string $key
     * @param string $field
     * @param $value
     * @return mixed
     */
    public function hSetNx(string $key, string $field, $value);

    /**
     * 删除指定的哈希
     * @param string $key
     * @return mixed
     */
    public function hDel(string $key);

    /**
     * 获取哈希所有字段
     * @param string $key
     * @return mixed
     */
    public function hGetAll(string $key);

    /**
     * 获取指定缓存的过期时间
     * @param string $key
     * @return mixed
     */
    public function ttl(string $key);

    /**
     * 设置指定缓存的过期时间
     * @param string $key
     * @param $ttl
     * @return mixed
     */
    public function expire(string $key, $ttl);

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
