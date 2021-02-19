<?php
/**
 * 根据路由名称生成对应路由
 * @param $routeName
 * @param array $params
 * @return mixed|null
 * @throws \FireRabbit\Module\Route\Exception\RouteNotFoundException
 */
function route($routeName, $params = [])
{
    $router = new \FireRabbit\Module\Route\Router();

    $route = $router->findRouteFromName($routeName);

    if ($route == null) {
        throw new \FireRabbit\Module\Route\Exception\RouteNotFoundException('不存在路由[' . $routeName . ']');
    }

    return $route->createLink($params);
}
