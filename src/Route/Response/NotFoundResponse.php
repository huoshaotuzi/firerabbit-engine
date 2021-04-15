<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/9/21
 * Time：2:25 PM
 **/

namespace FireRabbit\Engine\Route\Response;

use FireRabbit\Engine\View\Blade;

class NotFoundResponse extends RouteResponse
{
    public function response($request, $response, $route)
    {
        $response->header("Content-Type", "text/html; charset=utf-8");
        $content = Blade::view('error.404', []);
        $response->end($content);
    }
}
