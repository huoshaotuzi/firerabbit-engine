<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/18
 * Time：20:29
 **/


namespace FireRabbit\Engine\Task;

use Swoole\Http\Server;
use Swoole\Timer;

class Task
{
    /**
     * 分发一个任务
     * @param Server $server
     * @param TaskInterface $task
     * @param array $data
     * @return int
     */
    public static function dispatch(Server $server, string $task, array $data = []): int
    {
        $params = [
            'task' => $task,
            'data' => $data,
        ];

        return $server->task($params);
    }

    /**
     * 延迟分发任务
     * @param Server $server
     * @param int $ms
     * @param string $task
     * @param array $data
     */
    public static function delay(Server $server, int $ms, string $task, array $data = []): int
    {
        $params = [
            'task' => $task,
            'data' => $data,
        ];

        return Timer::after($ms, function () use ($server, $params) {
            $server->task($params);
        });
    }

    public static function tick(Server $server, int $ms, string $task, array $data = []): int
    {
        $params = [
            'task' => $task,
            'data' => $data,
        ];

        return Timer::tick(1000, function () use ($server, $params) {
            $server->task($params);
        });
    }

    public static function clear(int $timerID): bool
    {
        return Timer::clear($timerID);
    }
}
