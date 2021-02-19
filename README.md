# 火兔引擎
基于 swoole 的个人框架。

此框架为本人练习技术所用，切勿用于生产环境。

此框架适合初学者入门 swoole（本人也是初学者），封装了基本的路由、中间件、控制器和任务等。

由于本人十分喜欢 Laravel，但是 Laravel 的性能感人，因此此框架在很大程度上模仿了 Laravel 的习惯。

如果你之前用过 Laravel，再使用此框架基本可以立即上手。

我的博客记录了框架的开发过程，如有兴趣可前往了解。

博客地址：[http://huotublog.com](http://huotublog.com)

## 环境要求

- PHP：7.4 以上
- 安装了 swoole 扩展
- 安装了 redis 扩展

## Nginx 配置
本框架需要配合 nginx 使用，swoole 处理动态文件，nginx 处理静态资源。

```
server {
    listen 80;
    server_name firerabbit-engine.ht;

    location ~* \.(gif|jpg|jpeg|png|css|js|ico|ttf|woff|woff2|svg|map)$ {
        root /www/firerabbit-engine/public;
    }

    location / {
        proxy_http_version 1.1;
        proxy_set_header Connection "keep-alive";
        proxy_set_header X-Real-IP $remote_addr;
        
        if (!-e $request_filename){
            proxy_pass http://php-fpm74:9527; # 注意
        }
    }
}
```

如果你用的不是 docker 环境，`proxy_pass` 应该改为：

```
proxy_pass http://127.0.0.1:9527; # 你的端口
```

## 安装框架
新建一个空文件夹，即你的项目名字，如：blog。

进入 blog，执行：`composer require firerabbit/engine` 即可完成框架安装。

## 项目结构
项目文件的结构完全由你自定义，例如下面这个样子（需要你自己创建文件夹）：

![QQ20210219-134621.jpg](https://i.loli.net/2021/02/19/Vlf8WTNdrsQC3cP.jpg)

对应的配置文件及代码，如下说明。

### 自动加载
给你的项目添加自动加载，修改 composer.json：

```
{
  "require": {
    "firerabbit/engine": "^1.0"
  },
  "autoload": {
    "psr-4": {
      "App\\": "app/"
    }
  }
}

```

加入 autoload 字段，然后执行 `composer dump-autoload` 重新生成自动加载文件即可。

### 快速开始
只要仿照本文例子的文件结构及代码，即可直接启动框架实现简单页面的展示。

### app.php
这是项目的基础配置文件，包括框架配置以及你的个人项目配置。

```
<?php

use FireRabbit\Engine\Constant;

return [
    'framework' => [
        Constant::DATABASE_CONFIG => [
            'driver' => 'mysql',
            'host' => 'mysql',
            'port' => '3306',
            'database' => 'blog',
            'username' => 'root',
            'password' => 'xxoo',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ],
        Constant::CACHE_CONFIG => [
            'driver' => 'redis',
            'redis' => [
                'host' => 'redis',
                'port' => '6379',
                'password' => 'huotu666',
            ],
        ],
        Constant::JWT_CONFIG => [
            'key' => 'password',
            'alg' => 'HS256',
            'leeway' => 60,
        ],
        Constant::LOGGER_CONFIG => [
            'path' => __DIR__ . '/../storage/logs/log.log',
            'level' => 'info',
            'channel' => 'channel-name',
        ],
        Constant::VIEW_CONFIG => [
            'path' => __DIR__ . '/../view',
            'cache_path' => __DIR__ . '/../storage/cache/view_cache',
        ],
        Constant::MAIL_CONFIG => [
            'debug' => 0,
            'html' => true,
            'secure' => 'ssl',
            'pool' => [
                [
                    'host' => 'smtp.163.com',
                    'port' => 465,
                    'user' => 'xxoo@163.com',
                    'name' => '火兔博客',
                    'password' => 'xxoo',
                ]
            ],
        ],
    ],
];
```

`framework` 字段定义了框架的配置。

### middleware.php
中间件的配置，用来建立中间件名称与对应的中间件类的映射关系，现在没有中间件，直接返回空数组即可。

```
<?php 
return [];
```

### IndexController
控制器类，路由最终会解析成为某个控制器的方法。

```
<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/19
 * Time：12:39
 **/

namespace App\Http\Controller;

use FireRabbit\Engine\Controller\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->showMessage('hello world!');
    }
}
```

现在只需要一个简单的输出即可。

### web.php
路由配置文件，路由规则与 Laravel 相似。

```
<?php

$router = new \FireRabbit\Engine\Route\Router();

$router->setConfig([

    'namespace' => 'App\\Http\\Controller\\',

])->group(function () use ($router) {

    $router->get('/', 'IndexController@index');

});

return $router;
```

上述定义了一个路由 "/"，解析到 IndexController 的 index 方法。

除此之外还可以使用下面这种带路径参数的：

```
# 带有路径参数的，例如：/article/1
# 该路由会解析到 IndexController 的 show($id) 方法
$router->get('/article/{id}', 'IndexController@show');

# post 请求
$router->post('/article/{id}/update', 'IndexController@update');

# get 和 post 都允许的路由
$router->any('/test', 'IndexController@test');
```

group 方法可以给路由分组，在同一组的路由具有相同配置，如命名空间，中间件等等。

### 启动文件
在项目根目录创建一个 http_server.php：

```
<?php

use FireRabbit\Engine\Http\HttpServer;

date_default_timezone_set("Asia/Shanghai");
define('ROOT_PATH', __DIR__);

require './vendor/autoload.php';

$config = require './app/config/app.php';

$server = new HttpServer('0.0.0.0', 9527, [
    'worker_num' => 4,
    'task_worker_num' => 2,
]);

$router = require './app/route/web.php';
$middleware = require './app/config/middleware.php';

$server->task();
$server->finish();

$server->bootstrap($config['framework'])
    ->loadMiddleware($middleware)
    ->loadRouter($router)
    ->start();
```

然后在控制台执行代码：`php http_server.php` 即可启动 swoole 程序。

此处设定端口为 9527，因此只要访问 127.0.0.1:9527 即可看到网页输出了“hello world!”

至此，一个简单的路由+控制器就完成了。

## Model
数据库采用与 Laravel 完全相同的 ORM。

在项目的 Model 目录新建一个测试类：

```
<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/19
 * Time：14:00
 **/


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];
}

```

所有的 Model 只要继承 `Illuminate\Database\Eloquent\Model` 就拥有了增删改查等等功能。

示例代码：

```
$user = User::find(1);
var_dump($user);
```

## Cache
框架实现了简单的缓存系统，目前只有 redis 驱动可用。

示例代码：

```
public function test()
{
    $value = Cache::driver()->remember('test', 5, function () {
        return 'aaa';
    });

    $this->showMessage(json_encode($value));
}
```

## 邮件系统
框架集成了 PHPmailer，可以实现非常简单的发送邮件。

示例代码：

```
$mail = new Mailer();
$mail->subject('测测')
    ->body('bbb')
    ->altBody('xxxx')
    ->address('874811226@qq.com')
    ->send();
```

邮件需要自行申请并且开通 STMP 服务。

## JWT 用户认证
框架提供 JWT token 生成和解析服务，JWT 可用于用户身份认证。

示例代码：

```
$token = Auth::encode([
            'test' => 123,
        ], 60);

var_dump($token, base64_decode($token));

$value = Auth::decode($token);

var_dump($value);
```

直接调用 Auth 类即可。

## 中间件
中间件可以用来拦截某些不符合要求的请求，例如表单验证，用户身份验证(未登录则跳转到登录页)，诸如此类。

示例代码：

首先定义一个中间件，在 Middleware 文件夹新建：

```
<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/12/21
 * Time：9:17 PM
 **/

namespace App\Http\Middleware;

use Closure;
use FireRabbit\Engine\Http\Kernel;
use FireRabbit\Engine\Http\Middleware\Middleware;

class TestMiddlewareA extends Middleware
{
    public function handle(Kernel $kernel, Closure $next)
    {
        $request = $kernel->getHttpRequest();

        if ($request->getQueryParams('a') == 1) {
            $kernel->getHttpResponse()->showMessage('aa');
            return null;
        }

        return $next($kernel);
    }
}
```

上述中间件将会阻止路径上带有 a 参数且参数值等于 1 的路由。

定义好中间件后，需要将中间件加入到映射关系，编辑 app/config/middleware.php：

```
return [
    'a' => App\Http\Middleware\TestMiddlewareA::class,
];
```

将这个中间件命名为 a。

然后只需要在路由配置处加上中间件即可：

```
<?php

$router = new \FireRabbit\Engine\Route\Router();

$router->setConfig([

    'namespace' => 'App\\Http\\Controller\\Home\\',
    
    // 方法一，在分组配置里添加
    'middleware' => ['a'],

])->group(function () use ($router) {

    // 方法二，在单独的路由里添加
    $router->get('/test', 'IndexController@test')->middleware(['a']);

});

return $router;
```

## 异步任务
框架实现了 swoole 异步任务的封装，并且集成到了 Controller 里。

在 Controller 可以非常简单的分发一个任务。

如果需要执行任务，需要创建一个任务类，在 app/Http/Task 下新建一个任务类：

```
<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/18
 * Time：21:46
 **/

namespace App\Http\Task;

use FireRabbit\Engine\Mail\Mailer;
use FireRabbit\Engine\Task\TaskInterface;

class MailTask implements TaskInterface
{
    public function handle($params)
    {
        var_dump('调用handle处理任务');

        $mailer = new Mailer();
        $mailer->subject('测试异步任务发送邮件')
            ->body('这是邮件内容')
            ->address($params['email'])
            ->send();

        return '发送成功';
    }

    public function finish($result)
    {
        var_dump($result);
    }
}
```

这是一个发送邮件的任务类，任务是异步调用的，所以程序会直接返回，不会因为发送邮件缓慢而卡住。

然后回到 IndexController，调用任务的方法：

```
<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2/9/21
 * Time：1:17 PM
 **/

namespace App\Http\Controller\Home;

use App\Http\Task\MailTask;
use FireRabbit\Engine\Controller\Controller;

class IndexController extends Controller
{
    public function test()
    {
        $this->dispatch(MailTask::class, ['email' => '123456@qq.com']);
        $this->showMessage('ok');
    }
}
```

## 最后的话
框架现在只有基本功能，很多地方都还有改进空间。

后续有时间也会不断更新。

如有疑问可联系：QQ874811226

火兔博客：[http://huotublog.com](http://huotublog.com)
