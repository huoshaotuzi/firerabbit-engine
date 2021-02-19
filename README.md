# 火兔引擎
基于 swoole 的个人框架。

此框架为本人练习技术所用，切勿用于生产环境。

此框架适合初学者入门 swoole（本人也是初学者），封装了基本的路由、中间件、控制器和任务等。

由于本人十分喜欢 Laravel，但是 Laravel 的性能感人，因此此框架在很大程度上模仿了 Laravel 的习惯。

如果你之前用过 Laravel，再使用此框架基本可以立即上手。

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
## 快速开始
新建一个空文件夹，即你的项目名字，如：blog。

进入 blog，执行：`composer require firerabbit/engine`

然后在 blog 文件夹下创建一个 http_server.php，用于启动 swoole：

```

```

## 路由模块
