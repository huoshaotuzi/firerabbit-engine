<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/9/21
 * Time：2:22 PM
 **/


namespace FireRabbit\Module\Route\Response;


class ActionNotFoundResponse extends RouteResponse
{
    public function response($request, $response, $route)
    {
        $response->header("Content-Type", "text/html; charset=utf-8");
        $response->end('找不到控制器的方法：' . $route->getFullControllerName() . '->' . $route->action);
    }
}
