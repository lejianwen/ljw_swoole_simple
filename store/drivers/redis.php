<?php
/**
 * Created by PhpStorm.
 * User: lejianwen
 * Date: 2016/11/8
 * Time: 21:49
 */
namespace store\Drivers;
use store;
use Predis;
class redis implements store\IDriver
{
    public $redis ;
    //redis连接等都在外面实现
    public function __construct()
    {
        $this->redis = require BASE_PATH.'/load/predis.php';
        return $this;
    }

    //绑定fd跟用户id
    public function bindFdUser($fd, $user, $channel)
    {
        //设置string类型用户查找用户
        $this->redis->set($fd, $user);
        //hash用户查找fd
        $this->redis->hmset($user, ['fd' => $fd, 'channel' => $channel, 'time' => date('Y-m-d H:i:s')]);
        //fd存到集合中，用来广播
        $this->redis->sadd($channel, $fd);
    }

    //通过用户获取fd
    public function getFdByUser($user)
    {
        $fd = $this->redis->hget($user, 'fd');
        return $fd ? : false;
    }

    //根据用户获取连接详细信息 ['fd' => '标识' , 'channel' => '频道']
    public function getUserDetail($user)
    {
        $detail = $this->redis->hgetall($user);
        return $detail ? : false;
    }

    //通过fd获取用户
    public function getUserByFd($fd)
    {
        $user = $this->redis->get($fd);
        return $user ? : false;
    }

    //取消fd跟用户的关联信息
    public function removeFdUser($fd)
    {
        if($this->redis->keys($fd))
        {
            $user = $this->getUserByFd($fd);
            $channel = $this->redis->hget($user, 'channel');
            $this->redis->del($fd);
            $this->redis->del($user);
            $this->redis->srem($channel, $fd);
        }

    }

    public function updateFdUser($old_fd, $fd, $user)
    {
        $channel = $this->redis->hget($user, 'channel');
        $this->redis->del($old_fd);
        $this->redis->srem($channel, $old_fd);
        //设置string类型用户查找用户
        $this->redis->set($fd, $user);
        $this->redis->hmset($user, ['fd' => $fd, 'channel' => $channel, 'time' => date('Y-m-d H:i:s')]);
        $this->redis->sadd($channel, $fd);
    }

    public function getChannelUsers($channel)
    {
        $fds = $this->redis->smembers($channel);
        $users = [];
        foreach ($fds as $fd)
            $users[] = $this->getUserByFd($fd);
        return $users;
    }

    public function getChannelFds($channel)
    {
        return $this->redis->smembers($channel);
    }

    public function getAllChannels()
    {
        return $this->redis->smembers('channels');
    }

    public function getAllFds()
    {
        $channels = $this->getAllChannels();
        $fd_arr = [];
        if($channels)
            foreach ($channels as $channel)
                $fd_arr[$channel] = $this->getChannelFds($channel);
        return $fd_arr;
    }

    public function getChannelByFd($fd)
    {
        $user = $this->getUserByFd($fd);
        return $this->getChannelByUser($user);
    }

    public function getChannelByUser($user)
    {
        return $this->redis->hget($user, 'channel');
    }

    public function ChannelExists($channel)
    {
        $channels = $this->redis->smembers('channels');
        return in_array($channel, $channels) ? true : false;
    }

    public function changeUserChannel($user, $channel)
    {
        $old_channel = $this->getChannelByUser($user);
        $fd = $this->getFdByUser($user);
        $this->_changeFdUserChannel($old_channel, $channel, $fd, $user);
    }

    public function changeFdChannel($fd, $channel)
    {
        $old_channel = $this->getChannelByFd($fd);
        $user = $this->getUserByFd($fd);
        $this->_changeFdUserChannel($old_channel, $channel, $fd, $user);
    }

    public function _changeFdUserChannel($old_ch, $new_ch, $fd, $user)
    {
        $this->redis->hmset($user, ['fd' => $fd, 'channel' => $new_ch, 'time' => date('Y-m-d H:i:s')]);
        $this->redis->srem($old_ch, $fd);
        $this->redis->sadd($new_ch, $fd);
    }
}