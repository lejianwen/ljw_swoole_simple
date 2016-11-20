<?php
/**
 * Created by PhpStorm.
 * User: lejianwen
 * Date: 2016/11/18
 * Time: 10:44
 */
$config = require BASE_PATH.'/config/redis.php';
static $redis_client;
if(!$redis_client)
    $redis_client = new Predis\Client($config);
$redis_client->select(2);
return $redis_client;