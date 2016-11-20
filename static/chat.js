/**
 * Created by Administrator on 2016/11/15.
 */
var TYPE_SYSTEM = 1; //系统消息
var TYPE_USER_MSG = 2; //用户消息
var TYPE_COMMAND = 3; //操作指令
function chat($host, $port, $user, $token, $channel)
{
    this.host = $host;
    this.port = $port;
    this.user = $user;
    this.token = $token;
    this.channel = $channel;
    this.ws = new WebSocket('ws://' + $host + ':' + $port + '/?user=' + $user + '&token=' + $token + '&channel=' + $channel);
    this.ws.onopen = function () {
       // $('#users').append('<h3>'+chat.user+'</h3>');
        chat.showMessage({type:TYPE_SYSTEM, msg:'成功进入聊天室','time':''});
    };
    this.ws.onmessage = function (re) {
        var _data = chat.explainMsg(re);
        chat.showMessage(_data);
    };
    this.ws.onclose = function () {
        chat.showMessage({type: TYPE_SYSTEM, msg: '谢谢使用', 'time' : ''});
    };
    //展示普通消息
    if (chat.prototype.explainMsg == undefined) {
        chat.prototype.explainMsg = function (data) {
            var _data = eval('(' + data.data + ')');
            return _data;
        }
    }
    //展示消息
    if (chat.prototype.showMessage == undefined) {
        chat.prototype.showMessage = function (data) {
            alert(data.msg);
        }
    }

    //发送消息
    if (chat.prototype.sendMessage == undefined) {
        chat.prototype.sendMessage = function (data) {
            this.ws.send(JSON.stringify(data));
        }
    }
    //更换频道
    chat.prototype.changeChannel = function ($channel) {
        var data = {};
        data.type = TYPE_COMMAND;
        data.command = 'change_channel';
        data.new_channel = $channel;
        console.log(data);
        this.sendMessage(data);
    };
    //发送聊天信息
    chat.prototype.sendUserMessage = function(to_user,msg) {
        var data = {};
        data.type = TYPE_USER_MSG;
        data.to = to_user == '' ? ':all' : to_user;
        data.msg = msg;
        this.sendMessage(data);
    };
    //发送系统消息
    chat.prototype.sendSysMessage = function (msg) {
        var data = {};
        data.type = TYPE_SYSTEM;
        data.msg = msg;
        this.sendMessage(data);
    }


}
