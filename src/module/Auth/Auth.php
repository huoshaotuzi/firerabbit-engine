<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/18
 * Time：12:19
 **/


namespace FireRabbit\Module\Auth;


use Firebase\JWT\JWT;

class Auth
{
    protected static $config;

    public static function setConfig($config)
    {
        self::$config = $config;
    }

    public static function decode($token)
    {
        try {
            JWT::$leeway = self::$config['leeway'];
            $decoded = JWT::decode($token, self::$config['key'], [self::$config['alg']]);
            $data = (array)$decoded;

            return $data['data'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function encode($data, $expired)
    {
        $currentTimestamp = time();
        $key = self::$config['key'];

        $token = [
            'iat' => $currentTimestamp,
            'nbf' => $currentTimestamp,
            'exp' => $currentTimestamp + $expired,
            'data' => $data,
        ];

        return JWT::encode($token, $key, self::$config['alg']);
    }
}
