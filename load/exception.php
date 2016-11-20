<?php
/**
 * Created by PhpStorm.
 * User: lejianwen
 * Date: 2016/11/18
 * Time: 10:39
 */
//********报错插件********//
$whoops = new \Whoops\Run;

$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

$whoops->register();
//****************//