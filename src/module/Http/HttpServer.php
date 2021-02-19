<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/15
 * Time：16:26
 **/

namespace FireRabbit\Module\Http;

use FireRabbit\Module\Auth\Auth;
use FireRabbit\Module\Cache\Cache;
use FireRabbit\Module\Constant;
use FireRabbit\Module\Database\Manager as DatabaseManager;
use FireRabbit\Module\Logger\Log as Logger;
use FireRabbit\Module\Mail\Mailer;
use FireRabbit\Module\Route\Router;
use FireRabbit\Module\Task\TaskInterface;
use FireRabbit\Module\View\Blade;
use Swoole\Http\Server;

class HttpServer
{
    public $server;
    public $router;

    public function __construct($host, $port, $config = [])
    {
        $this->server = new Server($host, $port);
        $this->server->set($config);
    }

    public function loadRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

    public function loadMiddleware($middleware)
    {
        \FireRabbit\Module\Http\Middleware\Kernel::setConfig($middleware);
        return $this;
    }

    public function bootstrap($config)
    {
        // 视图
        Blade::setConfig($config[Constant::VIEW_CONFIG]);
        // 数据库ORM
        DatabaseManager::setConfig($config[Constant::DATABASE_CONFIG]);
        // 日志
        Logger::setConfig($config[Constant::LOGGER_CONFIG]);
        // 缓存
        $cache = $config[Constant::CACHE_CONFIG];
        Cache::setConfig($cache['driver'], $cache[$cache['driver']]);
        // JWT
        Auth::setConfig($config[Constant::JWT_CONFIG]);
        // 邮件
        Mailer::setConfig($config[Constant::MAIL_CONFIG]);

        return $this;
    }

    public function request($request, $response)
    {
        $this->registerError($response);
        $this->router->handle($this->server, $request, $response);
    }

    private function registerError($response)
    {
        register_shutdown_function(function () use ($response) {
            $error = error_get_last();
            switch ($error['type'] ?? null) {
                case E_ERROR :
                case E_PARSE :
                case E_CORE_ERROR :
                case E_COMPILE_ERROR :
                    $response->status(500);
                    $response->end($error['message']);
                    break;
            }
        });
    }

    public function task()
    {
        $this->server->on('task', function ($server, $taskID, $reactorID, $data) {
            if (isset($data['task']) && class_exists($data['task'])) {

                $task = new $data['task'];

                if ($task instanceof TaskInterface) {
                    $resultData = $task->handle($data['data']);
                    $result = [
                        'task' => $data['task'],
                        'result' => $resultData ?? null,
                    ];
                    $server->finish($result);
                }
            }
        });
    }

    public function finish()
    {
        $this->server->on('finish', function ($server, $taskID, $data) {
            if (isset($data['task']) && class_exists($data['task'])) {

                $task = new $data['task'];

                if ($task instanceof TaskInterface) {
                    $task->finish($data['result']);
                }
            }
        });
    }

    public function start()
    {
        $this->server->on('request', [$this, 'request']);
        $this->server->start();
    }
}
