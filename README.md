## swoole聊天项目


* 自己尝试的swoole聊天项目
* 暂时只做了用redis保存链接信息
* 顺便熟练了下composer:`有路由插件、错误插件、predis插件`
* 聊天记录并没有保存


## 
* 登录验证没有判断，随便什么都可以，但用户名请不要使用纯数字
* 频道的选择没有做全
## 使用
* 请先在redis里面 添加一个频道集合 
<pre class="brush:bash;">redis> zadd channels swoole_channel_1 swoole_channel_2 swoole_channel_3</pre>
* 配置文件在config文件夹中