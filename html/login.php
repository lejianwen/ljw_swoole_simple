<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../static/dist/css/bootstrap.min.css">
    <script src="../static/jquery-1.10.2.min.js"></script>
    <script src="../static/dist/js/bootstrap.min.js"></script>
    <title>chart</title>
</head>
<body>
<div class="container">
    <div class="row clearfix">
        <div class="col-md-12 column">
            <h3>
               我的聊天室
            </h3>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-md-2 column">
        </div>
        <div class="col-md-6 column">
            <form class="form-horizontal" role="form" action="/login" method="post">
                <div class="form-group">
                    <label for="username" class="col-md-2 control-label">用户名</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="username" name="username"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="col-md-2 control-label">密码</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password" name="password"/>
                    </div>
                </div>
                <!--<div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label><input type="checkbox" />Remember me</label>
                        </div>
                    </div>
                </div>-->
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default">登录</button>
                        <button type="button" class="btn btn-default">注册</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-2 column">
        </div>
    </div>
</div>
</body>
</html>
