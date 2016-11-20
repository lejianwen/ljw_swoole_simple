<?php
/**
 * Created by PhpStorm.
 * User: lejianwen
 * Date: 2016/11/17
 * Time: 17:34
 */
define('BASE_PATH', __DIR__);
require BASE_PATH.'/vendor/autoload.php';

//********报错插件********//
require BASE_PATH.'/load/exception.php';
//****************//

//********路由规则********//
require BASE_PATH.'/load/routes.php';

