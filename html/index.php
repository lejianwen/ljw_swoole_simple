<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../static/dist/css/bootstrap.min.css">
    <script src="../static/jquery-1.10.2.min.js"></script>
    <script src="../static/dist/js/bootstrap.min.js"></script>
    <script src="../static/dist/js/bootstrap.js"></script>
    <script src="../static/chat.js"></script>
    <title>chart</title>
</head>
<body>
<div class="well">
    <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-primary change_ch <?= $channel == 'swoole_channel_1' ? 'active':'' ?>" value="1" >
            <input type="radio" name="options" id="option1"> 频道 1
        </label>
        <label class="btn btn-primary change_ch <?= $channel == 'swoole_channel_2' ? 'active':'' ?>" value="2">
            <input type="radio" name="options" id="option2"> 频道 2
        </label>
        <label class="btn btn-primary change_ch <?= $channel == 'swoole_channel_3' ? 'active':'' ?>" value="3">
            <input type="radio" name="options" id="option3"> 频道 3
        </label>
    </div>
</div>

<div class="container " style="">
    <div class="row clearfix " style="height: 600px">
        <div class="col-sm-2 column bg-primary" style="width:20%;height: 100%" id="users">
        </div>

        <div class="col-sm-6 column" style="width:80%;height: 80%">
            <div class="well" id="show_msg" style="height: 100%;overflow-y: scroll">
            </div>
        </div>
        <div class="col-sm-6 column" style="width:80%;height: 20%">
            <div class="well" style="height:100%;">
                <div class="input-group input-group-lg" style="height: 100%;width: 100%">
                    <textarea class="form-control" style="height: 100%;width:90%" id="send_msg"></textarea>
                        <button id="send_btn" class="btn btn-lg btn-primary btn-block" type="button" style="height: 100%;width: 10%;">发送</button>
                </div>


            </div>
        </div>
            <!--<div class="col-md-6" style="padding-top: 10px;width:80%;height: 20%">
                <div class="input-group input-group-lg">
                    <input type="text" class="form-control">
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-block" type="button">Go!</button>
                    </span>
                </div>
            </div>-->

    </div>
</div>
<script>
    var chat = new chat('121.42.12.251','9501','<?php echo $user ? : '99999'?>','<?php echo $token?>','<?php echo $channel?>');
    //改写展示消息
    chat.showMessage = function(data){
        if(data.type == TYPE_SYSTEM)
        {
            var $msg = '<div class="panel panel-warning">'
                +'<div class="panel-heading">'
                +'<span class="glyphicon glyphicon-warning-sign"></span>'
                +'<span class="label label-default" style="margin-left: 12px">SYSTEM</span>'
                +'<span class="label label-default" style="margin-left: 12px">'+data.time+'</span>'
                +'</div>'
                +'<div class="panel-body">'+data.msg+'</div>'
                +'</div>';
            $('#show_msg').append($msg);
            var arr = data.msg.split('#');
            if(arr[1] == '上线了' || arr[1] == '加入了该频道'){
                users[arr[0]] = arr[0];
                updateUserList();
            }
            else if(arr[1] == '下线了' || arr[1] == '离开了该频道')
            {
                users[arr[0]] = null;
                updateUserList();
            }

        }else{
            var $msg = '<div class="panel panel-default">'
                +'<div class="panel-heading">'
                +'<span class="glyphicon glyphicon-user"></span>'
                +'<span class="label label-default" style="margin-left: 12px">'+data.from+'</span>'
                +'<span class="label label-default" style="margin-left: 12px">'+data.time+'</span>'
                +'</div>'
                +'<div class="panel-body">'+data.msg+'</div>'
                +'</div>';
            $('#show_msg').append($msg);
        }
        $("#show_msg").scrollTop($("#show_msg")[0].scrollHeight);
    };
</script>
<script>
    $(function(){
        updateUserList();
        $('#send_btn').on('click',function() {
         var $btn = $(this).button('loading');
         var msg = $('#send_msg').val();
         chat.sendUserMessage(':all',msg);
         $('#send_msg').val('');
         $btn.button('reset');
        });
        //重新选择频道
        $('.change_ch').on('click',function () {
            var channel = 'swoole_channel_'+$(this).attr('value');
            //清空聊天面板
            $('#show_msg').html('');
            //websocket 更换频道
            chat.changeChannel(channel);
            //清空频道users
            users = {};
            //web跟换频道，跟新session并拉取频道的用户
            changeChannel(channel);
        })
    })
</script>
<script>
    var users = {};
    <?php if(!empty($users)): ?>
        <?php foreach ($users as $user): ?>
            users['<?php echo $user?>'] = '<?php echo $user?>';
        <?php endforeach;?>
    <?php endif;?>
    function changeChannel(channel) {
        $.ajax({
            'url':'/changeChannel',
            'data':{'channel':channel},
            'dataType':'json',
            'type':'post',
            'success':function (re) {
                $(re.users).each(function(k,v){
                    users[v] = v;
                });
                updateUserList();
            }
        })
    }
    function updateUserList() {
        $('#users').html('');
        $.each(users,function(k,v){
            if(v)
                $('#users').append('<h3 id="'+v+'">'+v+'</h3>');
        });
    }
</script>
</body>
</html>