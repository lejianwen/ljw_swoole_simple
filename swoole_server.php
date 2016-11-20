<?php
define('BASE_PATH', __DIR__);
require BASE_PATH.'/vendor/autoload.php';
use helpers\helper;
$config = require_once BASE_PATH . '/config/swoole_websocket.php';
$helper = new helper($config);
$server = new \swoole_websocket_server($config['server']['host'], $config['server']['port']);
echo 'SERVER : 121.42.12.251:9501' . PHP_EOL;
//server设置
/*$serv->set([
	'worker_num' => 2,
	'max_request' => 10,
	'daemonize' => 0
]);*/

$server->on('open', [$helper,'onOpen']);
$server->on('message', [$helper, 'onMessage']);
$server->on('close', [$helper, 'onClose']);

$helper->server = $server;
$server->start();
