<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
	<title>FunChat</title>
	<link rel="stylesheet" href="static/css/chat.css">
	<script src="static/js/main.js" type="text/javascript" charset="utf-8"></script>
	<script src="static/js/jquery.min.js"></script>
	<script src="static/js/chat.js"></script>
</head>
<body>
	<div class="main">
		<div class="login-container">
			<div class="login" id="LoginBox">
				<div class="login-hd">
					<span class="fun">Fun</span>
					<span class="chat">Chat</span>
				</div>
				<div class="user-avatar">
					<img src="http://n.sinaimg.cn/translate/w1280h1280/20171211/hsEC-fypnsip6872500.jpg" alt="">
				</div>
				<div class="user-name">
					<input type="" name="" placeholder="Enter your nickname" id="Account"/>
				</div>
				<div class="login-btn">
					<div class="button" id="loginbtn" onclick="login()">Login</div>
				</div>
			</div>
		</div>
		<div class="main-container" id="chatBox">
			<div class="container" id="container">
				<div class="panel">
					<div class="header">
						<div  class="image"><img src="http://n.sinaimg.cn/translate/w1280h1280/20171211/hsEC-fypnsip6872500.jpg" alt=""></div>
						<div class="nickname" id="currentUser"></div>
					</div>
					<div class="group-name" onclick="backToGroup()" id="group-name">FunChat</div>
					<div class="userList" id="userList"></div>
				</div>
				<div class="chat-div" id="group-chat">
					<div class="chat-hd" id="group-chat-title">FunChat</div>
					<div class="chat-content" id="chat"></div>
					<div class="chat-ft">
						<div class="toolbar">
							<div class="emoji">
								<!-- <img src="http://bpic.588ku.com/element_origin_min_pic/00/93/91/4056f2b13a70c32.jpg" alt=""> -->
							</div>
							<div class="empty" onclick="empty()">清空</div>
						</div>
						<div class="input">
							<textarea placeholder="say something..." id="content" name="content"></textarea>
						</div>
						<div class="send">
							<div class="single-chat-button" id="sendMsg" onclick="sendMsg(2)">发送</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="footer">
	        <div class="copyright">
	          ©<a href="http://www.funsoul.org/">funsoul.org</a> 2018 备案号：粤ICP备17095160号  | 开发者<a href="http://www.funsoul.org">funsoul</a>
	          <a href="http://www.fogcrane.org/">fogcrane</a> | 当前版本 <a href="https://github.com/funsoul/funchat/releases/tag/1.0">funchat-1.0</a>
	        </div>
	    </div>
	</div>
	<script>
      <?php
      $websocketConfig = require 'config/server.php';
      $wsServer = 'ws://'.$websocketConfig['WEBSOCKET']['HOST'].':'.$websocketConfig['WEBSOCKET']['PORT'];
      ?>
      var wsServer = "<?php echo $wsServer;?>";
      var userList = document.getElementById('userList');
      //调用websocket对象建立连接：
      //参数：ws/wss(加密)：//ip:port （字符串）
      var websocket = new WebSocket(wsServer);
      //onopen监听连接打开
      websocket.onopen = function (evt) {
        //websocket.readyState 属性：
          /*
           CONNECTING    0    The connection is not yet open.
           OPEN          1    The connection is open and ready to communicate.
           CLOSING       2    The connection is in the process of closing.
           CLOSED        3    The connection is closed or couldn't be opened.
           */
        if(websocket.readyState == 1) {
          $('.warning').css('display','none');
          console.log('conn!');
        }
      };
      //监听连接关闭
      websocket.onclose = function (evt) {
        $('.warning').css('display','block');
        console.log("Disconnected");
      };
      //onmessage 监听服务器数据推送
      websocket.onmessage = function (evt) {
        receive(evt);
      };
      //监听连接错误信息
      websocket.onerror = function (evt, e) {
        $('.warning').css('display','block');
        console.log('Error occured: ' + evt.data);
      };

    </script>
</body>
</html>
