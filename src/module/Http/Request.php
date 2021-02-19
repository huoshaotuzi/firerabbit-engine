<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/11/21
 * Time：11:37 AM
 **/

namespace FireRabbit\Module\Http;

use FireRabbit\Module\Route\RouteParams;

class Request
{
    protected $request, $route;

    public function __construct($request, $route)
    {
        $this->request = $request;
        $this->route = $route;
    }

    public function getRequest()
    {
        return $this->request;
    }

    /**
     * 获取路由
     * @return mixed
     */
    public function getRoute(): RouteParams
    {
        return $this->route;
    }

    /**
     * 判断该请求是否ajax
     * @return bool
     */
    public function isAjax()
    {
        return 'XMLHttpRequest' == $this->request->header['x-requested-with'];
    }

    /**
     * 获取get参数
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getQueryParams($key = null, $default = null)
    {
        if ($key == null) {
            return $this->request->get;
        }

        return isset($this->request->get[$key]) ? $this->request->get[$key] : $default;
    }

    /**
     * 获取post参数
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getPostParams($key = null, $default = null)
    {
        if ($key == null) {
            return $this->request->post;
        }

        return isset($this->request->post[$key]) ? $this->request->post[$key] : $default;
    }

    /**
     * 获取请求方法
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->request->server['request_method'];
    }

    /**
     * 获取请求IP地址
     * @return string | null
     */
    public function getRequestIP()
    {
        return $this->request->header['x-real-ip'] ?? null;
    }

    /**
     * 获取请求头
     * @param $key
     * @return string | null
     */
    public function getHeaders($key = null)
    {
        if ($key == null) {
            return $this->request->header;
        }

        return $this->request->header[$key] ?? null;
    }

    /**
     * 获取cookie
     * @param $key
     * @return string | null
     */
    public function getCookies($key = null)
    {
        if ($key == null) {
            return $this->request->cookie;
        }

        return $this->request->cookie[$key] ?? null;
    }

    /**
     * 获取请求URI
     * @return mixed
     */
    public function getRequestURI()
    {
        return rtrim($this->request->server['request_uri'], '/');
    }
}
