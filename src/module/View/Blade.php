<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/13
 * Time：22:06
 **/


namespace FireRabbit\Module\View;

use Xiaoler\Blade\Compilers\BladeCompiler;
use Xiaoler\Blade\Engines\CompilerEngine;
use Xiaoler\Blade\Engines\EngineResolver;
use Xiaoler\Blade\Factory;
use Xiaoler\Blade\Filesystem;
use Xiaoler\Blade\FileViewFinder;

class Blade
{
    protected static $viewPath, $cachePath;

    /**
     * 设置模板文件目录
     * @param $viewPath
     * @param $cachePath
     */
    public static function setConfig($config)
    {
        self::$viewPath = $config['path'];
        self::$cachePath = $config['cache_path'];
    }

    /**
     * 获取模板引擎返回的html代码
     * @param $blade
     * @param $params
     * @return string
     */
    public static function view($blade, $params)
    {
        $file = new Filesystem;
        $compiler = new BladeCompiler($file, self::$cachePath);

        $resolver = new EngineResolver;
        $resolver->register('blade', function () use ($compiler) {
            return new CompilerEngine($compiler);
        });

        $factory = new Factory($resolver, new FileViewFinder($file, [self::$viewPath]));

        try {
            return $factory->make($blade, $params)->render();
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
}
