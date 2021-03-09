<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/3/9
 * Time：19:45
 **/

namespace FireRabbit\Engine\Exception;


abstract class ExceptionCatcher
{
    protected $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    abstract public function handle(array $error);
}
