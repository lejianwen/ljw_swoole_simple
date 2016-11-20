<?php
/**
 * Created by PhpStorm.
 * User: lejianwen
 * Date: 2016/11/8
 * Time: 21:02
 */
namespace store;
/*fd是指swoole
 * fd跟user的关系
*/
interface IDriver
{
    //绑定fd跟用户id
    public function bindFdUser($fd, $user, $channel);
    //通过用户获取fd
    public function getFdByUser($user);
    //根据用户获取连接详细信息 ['fd' => '标识' , 'channel' => '频道', 'time' =>'']
    public function getUserDetail($user);
    //通过fd获取用户
    public function getUserByFd($fd);
    //取消fd跟用户的关联信息
    public function removeFdUser($fd);
    //重新绑定fd和用户id
    public function updateFdUser($old_fd, $fd, $user);
    //获取频道中的所有用户
    public function getChannelUsers($channel);
    //获取该频道下的所有fd
    public function getChannelFds($channel);
    //获取所有频道
    public function getAllChannels();
    //判断频道是否存在
    public function ChannelExists($channel);
    //获取所有fd
    public function getAllFds();
    //根据fd获取频道
    public function getChannelByFd($fd);
    //根据用户获取频道
    public function getChannelByUser($user);
    //用户更换频道
    public function changeUserChannel($user, $channel);
    //fd更换频道
    public function changeFdChannel($fd, $channel);
}