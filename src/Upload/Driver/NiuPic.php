<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/21
 * Time：15:04
 **/

namespace FireRabbit\Engine\Upload\Driver;

use FireRabbit\Engine\Logger\Log;
use FireRabbit\Engine\Upload\UploadDriver;
use GuzzleHttp\Client;

class NiuPic implements UploadDriver
{
    public function upload($file)
    {
        $api = 'https://www.niupic.com/index/upload/process';
        $client = new Client();

        $fileStream = fopen($file, 'r');

        try {
            $res = $client->post($api, [
                'headers' => [
                    'referer' => 'https://www.niupic.com/',
                    'origin' => 'https://www.niupic.com',
                ],
                'multipart' => [
                    [
                        'name' => 'image_field',
                        'contents' => $fileStream,
                    ]
                ]
            ]);

            $result = json_decode($res->getBody()->getContents(), true);

            if ($result['status'] == 'success') {
                return $result['data'];
            }

            return null;
        } catch (\Exception $exception) {
            Log::getLogger()->error($exception->getFile() . ':' . $exception->getMessage());
            return null;
        }
    }
}
