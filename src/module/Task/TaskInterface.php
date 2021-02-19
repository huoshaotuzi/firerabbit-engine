<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/18
 * Time：20:23
 **/


namespace FireRabbit\Module\Task;


interface TaskInterface
{
    /**
     * 处理逻辑
     * @param $params
     * @return mixed
     */
    public function handle($params);

    /**
     * 处理完成回调
     * @param $params
     * @return mixed
     */
    public function finish($result);
}
