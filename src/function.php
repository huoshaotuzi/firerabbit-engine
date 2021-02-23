<?php
/**
 * 生成随机字符串
 * @param $length
 * @return false|string
 */
function str_random($length)
{
    $seeds = str_split('abcdefghijklmnopqrstuvwxyz1234567890');
    shuffle($seeds);

    $seeds = implode('', $seeds);
    $str = substr($seeds, 0, $length);

    return $str;
}

/**
 * 根据路由名称生成对应路由
 * @param $routeName
 * @param array $params
 * @return mixed|null
 * @throws \FireRabbit\Engine\Route\Exception\RouteNotFoundException
 */
function route($routeName, $params = [])
{
    $router = new \FireRabbit\Engine\Route\Router();
    $route = $router->findRouteFromName($routeName);

    if ($route == null) {
        throw new \FireRabbit\Engine\Route\Exception\RouteNotFoundException('不存在路由[' . $routeName . ']');
    }

    return $route->createLink($params);
}
