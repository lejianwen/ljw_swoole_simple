<?php
/**
 * Created by PhpStorm.
 * User: lejianwen
 * Date: 2016/11/17
 * Time: 17:36
 */
namespace app\controllers;
class user extends base
{
    public function login()
    {
        require BASE_PATH.'/html/login.php';
    }

    public function login_post()
    {
        session_start();
        $_SESSION['user'] = $_POST['username'];
        $_SESSION['token'] = md5($_POST['password']);
        $_SESSION['channel'] = $_POST['channel'];
        header('Location:chat');
    }

    public function chat()
    {
        session_start();
        $user = $_SESSION['user'] ;
        $token = $_SESSION['token'];
        $redis = require BASE_PATH.'/load/predis.php';
        $channel = $_SESSION['channel'];
        $fds = $redis->smembers($channel);
        $users = [];
        foreach ($fds as $fd)
        {
            $users[] = $redis->get($fd);
        }
        require BASE_PATH.'/html/index.php';
    }

    public function users($channel)
    {
        $redis = require BASE_PATH.'/load/predis.php';
        $channel = 'swoole_channel_'.$channel;
        $fds = $redis->smembers($channel);
        $users = [];
        foreach ($fds as $fd)
        {
            $users[] = $redis->get($fd);
        }
        exit(json_encode($users));
    }
}