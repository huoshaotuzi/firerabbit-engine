<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/21
 * Time：15:04
 **/

namespace FireRabbit\Engine\Upload;

use FireRabbit\Engine\Upload\Driver\NiuPic;

class Uploader
{
    public static $driver = 'niupic';

    public function driver(): UploadDriver
    {
        $driver = null;

        switch (self::$driver) {
            case 'niupic':
                $driver = new NiuPic();
                break;
        }

        return $driver;
    }
}
