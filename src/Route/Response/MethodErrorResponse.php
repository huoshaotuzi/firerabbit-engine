<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/9/21
 * Time：1:56 PM
 **/


namespace FireRabbit\Engine\Route\Response;


class MethodErrorResponse extends RouteResponse
{
    public function response($request, $response, $route)
    {
        $response->header("Content-Type", "text/html; charset=utf-8");
        $response->end('请求方法错误，正确方法应该为：' . $route->method);
    }
}
