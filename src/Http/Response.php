<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/12/21
 * Time：11:31 AM
 **/


namespace FireRabbit\Engine\Http;


class Response
{
    protected $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function showMessage($message)
    {
        $this->response->header("Content-Type", "text/html; charset=utf-8");
        $this->response->end($message);
    }
}
