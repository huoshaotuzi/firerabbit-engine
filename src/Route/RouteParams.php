<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/9/21
 * Time：12:48 PM
 **/

namespace FireRabbit\Engine\Route;

use FireRabbit\Engine\Http\Kernel as HttpKernel;
use FireRabbit\Engine\Http\Pipeline;
use FireRabbit\Engine\Http\Request;
use FireRabbit\Engine\Http\Response;
use FireRabbit\Engine\Route\Exception\RouteParamException;
use FireRabbit\Engine\Route\Response\ActionNotFoundResponse;
use FireRabbit\Engine\Route\Response\MethodErrorResponse;
use FireRabbit\Engine\Route\Response\ClassNotFoundResponse;
use Swoole\Http\Server;

class RouteParams
{
    /**
     * 路由名称
     * @var string
     */
    public $name;

    /**
     * 路由匹配规则
     * @var string
     */
    public $route;

    /**
     * 命名空间
     * @var string
     */
    public $namespace;

    /**
     * 控制器名称
     * @var string
     */
    public $controller;

    /**
     * 调用的控制器方法名称
     * @var string
     */
    public $action;

    /**
     * 请求方法
     * @var string
     */
    public $method;

    /**
     * 正则表达式匹配规则
     * @var string
     */
    public $pattern;

    /**
     * 中间件
     * @var array
     */
    public $middlewares = [];

    private $uri;

    /**
     * 生成链接
     * @param $params
     * @return string
     * @throws RouteParamException
     */
    public function createLink($params)
    {
        if ($this->route == '/') {
            return $this->buildQuery($this->route, $params);
        }

        // 取出自定义规则
        $pattern = '/.*?\/(\{.*?\})/';
        preg_match_all($pattern, $this->route, $result);

        // 如果匹配不到自定义参数则直接返回路由规则
        if (count($result) == 0) {
            return $this->buildQuery($this->route, $params);
        }

        // 获取自定义匹配规则
        $patterns = [];
        $paramNames = [];

        for ($i = 1, $count = count($result[1]); $i <= $count; $i++) {

            // 此处得到自定义规则的参数，如：{id}
            $rule = $result[1][$i - 1];

            /**
             * 花括号是正则表达式的符号，必须加上反斜杠转转义
             * 最后，在前后加上斜杠才是一个完整的正则表达式
             */
            $patterns[] = '/' . str_replace(['{', '}'], ['\{', '\}'], $rule) . '/';

            /**
             * 截取中间的变量名
             */
            $paramNames[] = substr($rule, 1, strlen($rule) - 2);
        }

        /**
         * 生成要替换的数组结构，根据规则与传入的参数一一对应
         * 假设路由规则是 /article/{id}
         * 那么$params传入的参数就应该是：['id'=>1]
         */
        $replacements = [];
        foreach ($paramNames as $key) {

            if (!isset($params[$key])) {
                throw new RouteParamException('路由缺失参数[' . $key . ']');
            }

            $replacements[] = $params[$key];

            // 移除路径参数
            unset($params[$key]);
        }

        // 然后将替换值根据规则进行置换
        $res = preg_replace($patterns, $replacements, $this->route);

        return $this->buildQuery($res, $params);
    }

    /**
     * 构建query参数的地址
     * @param $route
     * @param $query
     * @return string
     */
    private function buildQuery($route, $query)
    {
        if (empty($query)) {
            return $route;
        }

        return $route . '?' . http_build_query($query);
    }

    /**
     * 路由响应
     * @param Server $server
     * @param $request
     * @param $response
     */
    public function createResponse(Server $server, $request, $response)
    {
        // 判断请求方法是否正确
        if ($this->method != Router::ANY && $request->server['request_method'] != $this->method) {
            (new MethodErrorResponse())->response($request, $response, $this);
            return;
        }

        // 判断方法是否存在
        $controllerName = $this->getFullControllerName();
        if (!class_exists($controllerName)) {
            (new ClassNotFoundResponse())->response($request, $response, $this);
        }

        $this->server = $server;
        $action = $this->action;

        // 不存在方法则返回404
        if (!method_exists($controllerName, $action)) {
            (new ActionNotFoundResponse())->response($request, $response, $this);
            return;
        }

        $pipeline = new Pipeline();
        $kernel = new HttpKernel($server, new Request($request, $this), new Response($response));

        $routeResponse = $pipeline->send($kernel)
            ->through(array_reverse($this->middlewares))
            ->then($this->routeResponse());

        $routeResponse($kernel);
    }

    /**
     * 执行路由响应
     * @return \Closure
     */
    protected function routeResponse()
    {
        return function (HttpKernel $kernel) {

            $request = $kernel->getRequest();

            // 实例化类
            $controllerName = $this->getFullControllerName();
            $controllerObject = new $controllerName($kernel, $this->server);
            $this->uri = rtrim($request->server['request_uri'], '/');

            $params = $this->getRouteParams();

            // 执行方法时，路径参数作为方法的参数
            call_user_func_array([$controllerObject, $this->action], $params);
        };
    }

    /**
     * 获取路由参数
     * @return array
     */
    public function getRouteParams()
    {
        if ($this->uri == '') {
            return [];
        }

        preg_match_all($this->pattern, $this->uri, $result);

        if (count($result[0]) == 0) {
            return [];
        }

        $params = [];

        for ($i = 1; $i < count($result); $i++) {
            $params[] = $result[$i][0];
        }

        return $params;
    }

    public function getFullControllerName()
    {
        return $this->namespace . $this->controller;
    }
}
