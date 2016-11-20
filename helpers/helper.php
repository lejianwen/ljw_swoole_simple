<?php
/**
 * Created by PhpStorm.
 * User: lejianwen
 * Date: 2016/11/10
 * Time: 14:29
 */
namespace helpers;
class helper
{
    protected $store;
    const TYPE_SYSTEM = 1; //系统消息

    const TYPE_USER_MSG = 2; //用户消息

    const TYPE_COMMAND = 3; //操作命令

    public function __construct($config)
    {
        //驱动选择
        $store = $config['store'];
        $store_class = 'store\\drivers\\'.$store;
        $this->store = new $store_class();
    }

    //登录验证
    public function checkLogin($user_id, $token)
    {
        return true;
    }

    public function sendToUser(\swoole_websocket_server $server, $to_user, $data)
    {
        $fd = $this->store->getFdByUser($to_user);
        $server->push($fd, $data);
    }

    public function sendAll(\swoole_websocket_server $server, $data)
    {
        $all = $this->store->getAllFds();
        if(!empty($all))
            foreach ($all as $fds)
                foreach ($fds as $fd)
                    $server->push($fd, $data);

    }

    public function sendToChannel(\swoole_websocket_server $server, $channel, $data)
    {
        $fds = $this->store->getChannelFds($channel);
        if(!empty($fds))
            foreach ($fds as $fd)
            {
                $server->push($fd, $data);
            }
    }

    public function closeAll(\swoole_websocket_server $server)
    {
        $all = $this->store->getAllUser();
        if(!empty($all))
            foreach ($all as $fd)
            {
                $this->store->removeFdUser($fd);
                $server->close($fd);
            }
    }

    //构建消息json
    public function buildMsg($msg = '', $type = self::TYPE_USER_MSG, $user = '', $to = '')
    {

        return json_encode(['type' => $type, 'msg' => $msg, 'from' => $user, 'to' => $to, 'time' => date('Y-m-d H:i:s')]);
    }
    //系统消息
    public function buildSysMsg($msg)
    {
        return json_encode([
            'type' => self::TYPE_SYSTEM,
            'msg' => $msg,
            'from' => 'system',
            'time' => date('Y-m-d H:i:s')
        ]);
    }
    //聊天消息
    public function buildUserMsg($from, $to_user, $msg)
    {
        return json_encode([
            'type' => self::TYPE_USER_MSG,
            'msg' => $msg,
            'from' => $from,
            'to' => $to_user,
            'time' => date('Y-m-d H:i:s')
        ]);
    }

    //解析消息json
    public function explainMsg($json)
    {
        return json_decode($json, true);
    }


    public function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {
        $get = $request->get;
        $user = $get['user'];
        $channel = $get['channel'];
        //登录验证
        if (empty($get) || !$this->checkLogin($user, $get['token'])) {
            $server->close($request->fd);
            return;
        }

        //是否重复登录
        if ($old_fd = $this->store->getFdByUser($user)) {
            //重新绑定
            $this->store->updateFdUser($old_fd, $request->fd, $user);
            $server->push($old_fd, $this->buildSysMsg('已在别处登录'));
            $server->close($old_fd);
        }
        // 用户跟fd绑定
        else {
            $this->store->bindFdUser($request->fd, $user, $channel);
        }
        //提示所有用户 该用户上线了
        $this->sendToChannel($server, $channel, $this->buildSysMsg($user . '#上线了'));
        echo "Client:Connect. fd: {$request->fd}, user: {$user} \n";
    }

    public function onMessage(\swoole_websocket_server $server, \swoole_websocket_frame $frame)
    {
        $data = $this->explainMsg($frame->data);
        $user = $this->store->getUserByFd($frame->fd);
        if($data['type'] == self::TYPE_COMMAND && $data['command'])
        {
            //更换频道
            if($data['command'] == 'change_channel')
            {
                $new_channel = $data['new_channel'];
                if(!$this->store->channelExists($new_channel))
                {
                    $this->sendToUser($server, $user, $this->buildSysMsg('频道不存在'));
                    return;
                }

                $old_channel = $this->store->getChannelByFd($frame->fd);

                $this->store->changeFdChannel($frame->fd, $new_channel);

                $send2 = $this->buildSysMsg($user.'#离开了该频道');
                $this->sendToChannel($server, $old_channel, $send2);

                $send = $this->buildSysMsg($user.'#加入了该频道');
                $this->sendToChannel($server, $new_channel, $send);


            }
        }
        //普通用户信息
        elseif($data['type'] == self::TYPE_USER_MSG)
        {
            //群聊
            if($data['to'] == ':all')
            {
                $channel = $this->store->getChannelByFd($frame->fd);
                $send = $this->buildUserMsg($user, 'all' ,$data['msg']);
                $this->sendToChannel($server, $channel , $send);
            }
            //私聊
            elseif($data['to'])
            {
                $this->buildUserMsg($user, $data['to'], $data['msg']);
                $this->sendToUser($server, $data['to']);
            }

           // $this->sendAll($server, $send);
        }

    }

    public function onClose(\swoole_websocket_server $server, $fd)
    {
        $user_id = $this->store->getUserByFd($fd);
        $channel = $this->store->getChannelByFd($fd);
        if($user_id)
        {
            $this->store->removeFdUser($fd);
            //通知所有用户 该用户已下线
            $send = $this->buildMsg($user_id.'#下线了', self::TYPE_SYSTEM);
            $this->sendToChannel($server, $channel, $send);
        }

        echo "Client: Close." . $fd . "\n";
    }
}