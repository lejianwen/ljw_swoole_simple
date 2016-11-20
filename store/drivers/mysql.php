<?php
/**
 * Created by PhpStorm.
 * User: lejianwen
 * Date: 2016/11/8
 * Time: 21:49
 */
namespace store\drivers;

use store;

class mysql implements store\IDriver
{
    //绑定fd_id跟用户id
    public function bindFdUser($fd, $user)
    {

    }

    //通过用户获取fd_id
    public function getFdByUser($user)
    {

    }

    //通过fd_id获取用户
    public function getUserByFd($user)
    {
    }

    //取消fd_id跟用户的关联信息
    public function removeFdUser($fd)
    {

    }

    //重新绑定fd和用户id
    public function updateFdUser($old_fd, $fd, $user)
    {

    }
    //获取所有用户
    public function getAllUser()
    {

    }

    public function getFdDetailUser($user)
    {

    }
}