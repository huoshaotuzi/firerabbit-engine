<?php

/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/12/21
 * Time：11:34 AM
 **/


namespace FireRabbit\Module\Http;

use Closure;
use FireRabbit\Module\Http\Exception\MiddlewareNotFoundException;
use FireRabbit\Module\Http\Middleware\Kernel as MiddlewareKernel;

class Pipeline
{
    protected $pipes, $kernel;

    public function send(Kernel $kernel)
    {
        $this->kernel = $kernel;

        return $this;
    }

    public function through($pipes)
    {
        $this->pipes = $pipes;

        return $this;
    }

    public function then(Closure $destination)
    {
        return array_reduce($this->pipes, $this->carry(), $this->dispatchRouter($destination));
    }

    function carry()
    {
        return function ($stack, $pipe) {

            return function ($passable) use ($stack, $pipe) {

                if ($pipe instanceof Closure) {
                    return $pipe($passable, $stack);
                } elseif (!is_object($pipe)) {
                    $pipe = MiddlewareKernel::getMiddlewareInstance($pipe);

                    if (empty($pipe)) {
                        throw new MiddlewareNotFoundException('middleware [' . $pipe .'] not found!');
                    }
                }

                return $pipe->handle($passable, $stack);
            };
        };
    }

    function dispatchRouter($destination)
    {
        return function ($passable) use ($destination) {
            $destination($passable);
        };
    }
}
