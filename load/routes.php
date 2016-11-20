<?php
/**
 * Created by PhpStorm.
 * User: lejianwen
 * Date: 2016/11/17
 * Time: 17:35
 */
use NoahBuscher\Macaw\Macaw as Route;

//首页
Route::get('/', 'app\controllers\user@login');

Route::get('login', 'app\controllers\user@login');
Route::get('chat', 'app\controllers\user@chat');
Route::get('users/(:any)', 'app\controllers\user@users');
Route::post('login', 'app\controllers\user@login_post');
Route::post('changeChannel', 'app\controllers\user@changeChannel');
Route::error(function() {
    echo '未匹配到路由<br>';
});
Route::dispatch();