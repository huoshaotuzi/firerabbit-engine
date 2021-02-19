<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/9/21
 * Time：1:44 PM
 **/


namespace FireRabbit\Module\Route\Response;


class ClassNotFoundResponse extends RouteResponse
{
    public function response($request, $response, $route)
    {
        $response->header("Content-Type", "text/html; charset=utf-8");
        $response->end('找不到类：' . $route->getFullControllerName());
    }
}
