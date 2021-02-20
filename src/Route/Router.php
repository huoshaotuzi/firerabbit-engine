<?php

/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/6/21
 * Time：8:04 PM
 **/


namespace FireRabbit\Engine\Route;

use FireRabbit\Engine\Route\Exception\RouteParamException;
use FireRabbit\Engine\Route\Response\NotFoundResponse;
use Swoole\Http\Server;

class Router
{
    const GET = 'GET';
    const POST = 'POST';
    const ANY = 'ANY';

    protected static $routes = [];
    protected $namespace = '';
    protected $middlewares = [];

    /**
     * 保存最后一个操作的路由对象索引
     * @var null
     */
    private $lastHandleRouteIndex = null;

    /**
     * 处理路由
     * @param Server $server
     * @param $request
     * @param $response
     */
    public function handle(Server $server, $request, $response)
    {
        $route = $this->findRoute($request);

        if ($route == null) {
            (new NotFoundResponse)->response($request, $response, $route);
            return;
        }

        $route->createResponse($server, $request, $response);
    }

    /**
     * 根据路由名称寻找路由
     * @param $routeName
     * @return mixed|null
     */
    public function findRouteFromName($routeName)
    {
        foreach (self::$routes as $route) {

            if ($route->name == $routeName) {
                return $route;
            }
        }

        return null;
    }

    /**
     * 寻找路由
     * @param $request
     * @return mixed|null
     */
    public function findRoute($request)
    {
        $uri = rtrim($request->server['request_uri'], '/');

        foreach (self::$routes as $route) {

            if (empty($uri)) {

                if ($route->route != '/') {
                    continue;
                }

                return $route;
            } else if ($route->pattern != '' && preg_match($route->pattern, $uri) != 0) {

                return $route;
            }
        }

        return null;
    }

    /**
     * 定义一个 GET 请求路由
     * @param $route
     * @param $controller
     * @return Router
     * @throws RouteParamException
     */
    public function get($route, $controller)
    {
        return $this->addRoute(Router::GET, $route, $controller);
    }

    /**
     * 定义一个 POST 请求路由
     * @param $route
     * @param $controller
     * @return Router
     * @throws RouteParamException
     */
    public function post($route, $controller)
    {
        return $this->addRoute(Router::POST, $route, $controller);
    }

    /**
     * 定义一个任意请求皆可的路由
     * @param $route
     * @param $controller
     * @return Router
     * @throws RouteParamException
     */
    public function any($route, $controller)
    {
        return $this->addRoute(Router::ANY, $route, $controller);
    }

    /**
     * 路由添加中间件
     *
     * @param array $middlewares
     * @return Router
     */
    public function middleware(array $middlewares)
    {
        if ($this->lastHandleRouteIndex === null) {
            return $this;
        }

        // 合并中间件，优先级为：分组>单个路由自定义配置
        $middlewares = array_merge($this->middlewares, $middlewares);
        // 去除重复中间件
        $middlewares = array_unique($middlewares);
        // 找到最后一个添加的路由
        $route = self::$routes[$this->lastHandleRouteIndex];
        $route->middlewares = $middlewares;

        self::$routes[$this->lastHandleRouteIndex] = $route;
    }

    /**
     * 给路由命名
     * @param $routeName
     * @throws RouteParamException
     */
    public function name($routeName)
    {
        if ($this->lastHandleRouteIndex === null) {
            return $this;
        }

        // 判断路由是否存在同名
        foreach (self::$routes as $route) {
            if ($route->name == $routeName) {
                throw  new RouteParamException('路由名称重复[' . $routeName . ']');
            }
        }

        $route = self::$routes[$this->lastHandleRouteIndex];
        $route->name = $routeName;

        self::$routes[$this->lastHandleRouteIndex] = $route;

        return $this;
    }

    /**
     * 设置配置参数外部调用方法
     * @param $configs
     * @return $this
     */
    public function setConfig($configs)
    {
        foreach ($configs as $key => $value) {
            $this->createConfig($key, $value);
        }

        return $this;
    }

    /**
     * 设置参数
     * @param $key
     * @param $value
     */
    protected function createConfig($key, $value)
    {
        switch ($key) {
            case 'namespace':
                $this->namespace = $value;
                break;
            case 'middleware':
                $this->middlewares = $value;
                break;
        }
    }

    /**
     * 路由分组
     * @param $func
     */
    public function group($func)
    {
        $func();

        // 执行完成后将参数初始化
        $this->namespace = '';
        $this->middlewares = [];
    }

    /**
     * 将路由加入配置数组
     * @param $method
     * @param $route
     * @param $controller
     * @return Router
     * @throws RouteParamException
     */
    protected function addRoute($method, $route, $controller)
    {
        $param = new RouteParams();

        $param->method = $method;
        $param->route = $route;

        // 格式为：控制器@方法名
        $actions = explode('@', $controller);

        // 如果不按照规则设置控制器和方法名则抛出异常
        if (count($actions) != 2) {
            throw new RouteParamException('控制器和方法名称错误，应该为：控制器名称@方法名称');
        }

        $param->controller = $actions[0];
        $param->action = $actions[1];
        $param->namespace = $this->namespace;
        $param->middlewares = $this->middlewares;
        $param->pattern = $this->getPattern($route);

        self::$routes[] = $param;
        $this->lastHandleRouteIndex = count(self::$routes) - 1;

        return $this;
    }

    protected function getPattern($route)
    {
        if ($route == '/') {
            return '';
        }

        $pattern = '/.*?\/(\{.*?\})/';

        preg_match_all($pattern, $route, $result);

        // 如果第一个数组的个数为0，表示没有匹配到路径参数
        if (count($result[0]) == 0) {
            return '/' . str_replace('/', '\/', $route) . '/';
        }

        $transform = str_replace($result[1], '(.*?)', $route);
        $transform = '/' . str_replace('/', '\/', $transform) . '$/';

        return $transform;
    }
}
