<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/12/21
 * Time：9:15 PM
 **/


namespace FireRabbit\Engine\Http\Middleware;


use Closure;
use FireRabbit\Engine\Http\Kernel;

abstract class Middleware
{
    abstract public function handle(Kernel $kernel, Closure $next);
}
