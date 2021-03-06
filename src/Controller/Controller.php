<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/9/21
 * Time：1:16 PM
 **/


namespace FireRabbit\Engine\Controller;

use FireRabbit\Engine\Task\Task;
use FireRabbit\Engine\Http\Kernel as HttpKernel;
use FireRabbit\Engine\View\Blade;

class Controller
{
    protected HttpKernel $httpKernel;

    public function __construct(HttpKernel $httpKernel)
    {
        $this->httpKernel = $httpKernel;
    }

    /**
     * 分发任务
     * @param $task
     * @param $data
     * @return int
     */
    public function dispatch($task, $data): int
    {
        $server = $this->httpKernel->getServer();
        return Task::dispatch($server, $task, $data);
    }

    /**
     * 延迟分发任务
     * @param $ms
     * @param $task
     * @param $data
     * @return int
     */
    public function delay($ms, $task, $data): int
    {
        $server = $this->httpKernel->getServer();
        return Task::delay($server, $ms, $task, $data);
    }

    /**
     * 重复执行任务
     * @param $ms
     * @param $task
     * @param $data
     * @return int
     */
    public function tick($ms, $task, $data): int
    {
        $server = $this->httpKernel->getServer();
        return Task::tick($server, $ms, $task, $data);
    }

    public function clear(int $timerID): bool
    {
        return Task::clear($timerID);
    }

    /**
     * 输出html
     * @param $blade
     * @param $params
     */
    public function html($blade, $params = [])
    {
        $this->httpKernel->html(Blade::view($blade, $params));
    }

    /**
     * 返回api响应（json）
     * @param array $data
     */
    public function response(array $data)
    {
        $this->httpKernel->response($data);
    }

    /**
     * 重定向到指定路径
     * @param $path
     */
    public function redirect($path)
    {
        $this->httpKernel->getResponse()->redirect($path);
    }

    public function getRequest()
    {
        return $this->httpKernel->getRequest();
    }

    public function getResponse()
    {
        return $this->httpKernel->getResponse();
    }
}
