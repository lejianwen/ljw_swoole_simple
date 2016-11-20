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
        <label class="btn btn-primary change_ch <?= $_GET['channel'] == 1 ? 'active':'' ?>" value="1" >
            <input type="radio" name="options" id="option1"> 频道 1
        </label>
        <label class="btn btn-primary change_ch <?= $_GET['channel'] == 2 ? 'active':'' ?>" value="2">
            <input type="radio" name="options" id="option2"> 频道 2
        </label>
        <label class="btn btn-primary change_ch <?= $_GET['channel'] == 3 ? 'active':'' ?>" value="3">
            <input type="radio" name="options" id="option3"> 频道 3
        </label>
    </div>
</div>

<div class="container " style="">
    <div class="row clearfix " style="height: 800px;">
        <div class="col-sm-2 column bg-primary" style="width:20%;height: 100%" id="users">
            <?php if(!empty($users)):?>
                <?php foreach ($users as $_user):?>
                    <h3 id="<?php echo $_user?>"><?php echo $_user?></h3>
                <?php endforeach;?>
            <?php endif;?>
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
    $(function(){
        $('#send_btn').on('click',function() {
         var $btn = $(this).button('loading');
         var msg = $('#send_msg').val();
         chat.sendUserMessage(':all',msg);
         //$btn.button('reset');
        });
        $('.change_ch').on('click',function () {
            var channel = $(this).attr('value');
            chat.changeChannel('swoole_channel_'+channel);
            //清空聊天面板
            $('#show_msg').html('');
            $('#users').html('');
            //频道用户拉取用ajax算了
            $.ajax({
                'url':'/users/'+channel,
                'data':{},
                'dataType':'json',
                'type':'get',
                'success':function (re) {
                    $(re).each(function(k,v){
                        $('#users').append('<h3 id="'+v+'">'+v+'</h3>');
                    });
                }
            })
        })
    })
</script>
<script>
    var chat = new chat('121.42.12.251','9501','<?php echo $user ? : '99999'?>','<?php echo $token?>','<?php echo $channel?>');
    //改写展示消息
    chat.showMessage = function(data){
        console.log(data);
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
            if(arr[1] == '上线了' || arr[1] == '加入了该频道')
            {
                if($('#'+arr[0])[0] == 'undefined')
                {
                    $('#users').append('<h3 id="'+arr[0]+'">'+arr[0]+'</h3>');
                }

            }

            else if(arr[1] == '下线了' || arr[1] == '离开了该频道')
            {
                $('#'+arr[0]).remove();
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
    function change() {
        chat.ws.send(JSON.stringify({
            'type' : 'cmd',
            'command':"change_channel"
        }));
    }
</script>
</body>
</html>