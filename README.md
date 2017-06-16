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
```shell
$ mkdir /usr/local/ruby
$ cd /usr/local/ruby
$ wget https://cache.ruby-lang.org/pub/ruby/2.4/ruby-2.4.1.tar.gz
$ tar xzf ruby-2.4.1.tar.gz
$ cd ruby-2.4.1
$ ./configure
$ make && make install
```
ruby也有一个类似于PHP的composer包管理工具gem，我们安装ruby后就会默认安装gem

#### 检测是否有gem
```shell
$ gem -v
```
如果没有找到该命令则需要创建一个软连接
```shell
$ ln -s /usr/local/bin/gem /usr/bin/gem
```

#### 安装resque-web
```shell
$ gem install resque
```
执行以下命令则即可运行在 3000 端口。
```shell
$ resque-web -p 3000
```

## 5、Redis和[php-resque](https://github.com/chrisboulton/php-resque)使用
> php-resque是来自Ruby的项目Resque的一个PHP扩展，正是由于Resque清晰简单的解决了后台任务带来的一系列问题。

#### 在Resque中后台任务的角色划分 
>* Job       （任务）     一个Job就是一个需要在后台完成的任务，比如发送邮件，就可以抽象为一个Job。在Resque中一个Job就是一个Class。
>* Queue     （队列）     也就是上文的消息队列，在Resque中，队列则是由Redis实现的。Resque还提供了一个简单的队列管理器，可以实现将Job插入/取出队列等功能。
>* Worker    （执行者）   负责从队列中取出Job并执行，可以以守护进程的方式运行在后台。

#### 那么基于这个划分，一个后台任务在Resque下的基本流程是这样的
>1. 将一个后台任务编写为一个独立的Class，这个Class就是一个Job。
>2. 在需要使用后台程序的地方，系统将Job Class的名称以及所需参数放入队列。
>3. 以命令行方式开启一个Worker，并通过参数指定Worker所需要处理的队列。
>4. Worker作为守护进程运行，并且定时检查队列。
>5. 当队列中有Job时，Worker取出Job并运行，即实例化Job Class并执行Class中的方法。

至此就可以完整的运行完一个后台任务。