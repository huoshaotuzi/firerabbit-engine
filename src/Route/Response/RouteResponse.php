<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/9/21
 * Time：1:44 PM
 **/


namespace FireRabbit\Engine\Route\Response;


abstract class RouteResponse
{
    public abstract function response($request, $response, $route);
}
