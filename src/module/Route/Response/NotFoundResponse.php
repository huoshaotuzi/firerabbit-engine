<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/9/21
 * Time：2:25 PM
 **/


namespace FireRabbit\Module\Route\Response;


class NotFoundResponse extends RouteResponse
{
    public function response($request, $response, $route)
    {
        $response->header("Content-Type", "text/html; charset=utf-8");
        $response->end('不存在页面，404');
    }
}
