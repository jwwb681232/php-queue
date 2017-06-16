<?php
include '../vendor/autoload.php';
Resque::setBackend('127.0.0.1:6379');
$args=array(
    'name'=>'Chris'
);
Resque::enqueue('default',$argv[1],$args,true);