<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/12/21
 * Time：10:14 PM
 **/

namespace FireRabbit\Engine\Http;

use Swoole\Http\Server;

class Kernel
{
    protected $request, $response;
    protected Server $server;

    public function __construct(Server $server, Request $request, Response $response)
    {
        $this->server = $server;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * 输出html
     * @param $html
     */
    public function html($html)
    {
        $response = $this->response->getResponse();
        $response->header("Content-Type", "text/html; charset=utf-8");
        $response->end($html);
    }

    /**
     * 输出JSON响应
     * @param $content
     */
    public function response($content)
    {
        $response = $this->response->getResponse();
        $response->header('content-type', 'application/json;charset=utf-8');
        $response->end(json_encode($content));
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function getRequest()
    {
        return $this->request->getRequest();
    }

    public function getResponse()
    {
        return $this->response->getResponse();
    }

    public function getHttpRequest()
    {
        return $this->request;
    }

    public function getHttpResponse()
    {
        return $this->response;
    }
}
