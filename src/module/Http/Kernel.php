<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/12/21
 * Time：10:14 PM
 **/

namespace FireRabbit\Module\Http;

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
