<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/21
 * Time：15:05
 **/


namespace FireRabbit\Engine\Upload;


interface UploadDriver
{
    public function upload($file);
}
