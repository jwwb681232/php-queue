# php-queue
> 一个简单的PHP队列系统实现。基于[php-resque](https://github.com/chrisboulton/php-resque)，鉴于网上关于此扩展包的资料稀少，在此进行总结。

## 环境
1. Linux CentOs 7
2. PHP 5.3+
3. composer 1.4.2
4. Redis 3.2.9
5. ruby 2.4.1
6. gem 2.6.11
7. RedisDesktopManager for Windows

前期准备工作还是蛮多的

## 1、安装PHP
为了方便，本地测试机直接安装lnmp,以供后续整个web环境使用
```shell
$ wget -c http://soft.vpser.net/lnmp/lnmp1.4.tar.gz
$ tar zxf lnmp1.4.tar.gz
$ cd lnmp1.4
$ ./install.sh lnmp
```

## 2、安装composer
早期版本的lnmp安装包里默认是不带composer的，所以需要单独安装。

#### 检测是否已经安装composer
```shell
$ composer --version
Composer version 1.4.2 2017-05-17 08:17:52
```
说明本地已经安装了

#### 安装
打开命令行并依次执行下列命令安装最新版本的 Composer
```shell
$ php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');"
$ php composer-setup.php
$ php -r "unlink('composer-setup.php');"
```
令将前面下载的 composer.phar 文件移动到 /usr/local/bin/ 目录下面
```shell
$ mv composer.phar /usr/local/bin/composer
```

## 3、安装redis
```shell
$ mkdir /usr/local/redis
$ cd /usr/local/redis
$ wget http://download.redis.io/releases/redis-3.2.9.tar.gz
$ tar xzf redis-3.2.9.tar.gz
$ cd redis-3.2.9
$ make && make install
$ mv redis-3.2.9 redis
```
#### 配置redis
```shell
$ cd redis
$ vim redis.conf
```
```shell
# daemonize 设置是否以守护进程模式运行，默认no，需要改为yes
daemonize yes

# 默认只bind本地IP，如果需要bind多个IP，则在后面加入需要bind的IP，以空格分隔。我这里注释掉此配置，表示任何主机都可以bind
#bind 127.0.0.1

# 保护模式开关，默认为yes，这里我们需要远程连接则设置为no
protected-mode no
```

## 4、安装ruby
安装ruby是为了在主机上搭建一个队列（queue）监控系统（resque-web），可以直观的对队列进行操作。