<?php
require_once ('./base/WebSocket.php');
require_once ('./lib/function/Utils.php');


const MSG_TYPE_LOGIN = 1;
const MSG_TYPE_DISPATCH = 2;
const MSG_TYPE_SINGLE = 3;

class WebSocketServer extends WebSocket
{
    private $_server_config = [];
    private $_db_config = [];
    private $redis;
    public function __construct()
    {
        $this->_server_config = require 'config/server.php';
        $this->_db_config = require 'config/database.php';

        $this->redis = new Redis();
        $this->redis->connect(
            $this->_db_config['REDIS']['HOST'],
            $this->_db_config['REDIS']['PORT']
        );
        $this->redis->set("fd", "[]");

        parent::__construct(
            $this->_server_config['LOCALHOST']['HOST'],
            $this->_server_config['LOCALHOST']['PORT'],
            $this->_server_config['LOCALHOST']['WORKER_NUM'],
            $this->_server_config['LOCALHOST']['IS_DAEMON']
        );
    }

    public function open(swoole_websocket_server $server, swoole_http_request $req)
    {
        echo "\n connection open: " . $req->fd . "\n";
        echo $this->redis->ping();
        $depkg = json_decode($this->redis->get("fd"), true);
        if ($depkg == "") $depkg = [];
        if (!isset($depkg[$req->fd])) {
            $depkg[$req->fd] = [];
            $enpkg = json_encode($depkg);
            $this->redis->set("fd", $enpkg);
        }
    }

    public function message(swoole_websocket_server $server, swoole_websocket_frame $frame)
    {
        echo "\n message: " . $frame->data . "\n";

        $data = json_decode($frame->data,true);
        $depkg = json_decode($this->redis->get("fd"), true);
        $depkg[$frame->fd] = $data['fromWho'];

        $enpkg = json_encode($depkg);
        $this->redis->set("fd", $enpkg);

        switch ($data['type']){
            case MSG_TYPE_LOGIN:
                $userList = [];
                foreach ($depkg as $fd => $username){
                    $userList[] = [
                        'fd' => $fd,
                        'username' => $username
                    ];
                }
                $res = json_encode(['userList' => $userList]);
                $server->push($fd, $res);
                break;
            case MSG_TYPE_DISPATCH:
                foreach ($depkg as $fd => $value){
                    $server->push($fd, $frame->data);
                }
                break;
            case MSG_TYPE_SINGLE:
                $server->push($data['toWho'], $frame->data);
                break;
        }
    }

    public function close(swoole_websocket_server $server, $fd)
    {
        echo "\n 连接关闭: \n" . $fd;
    }
}

new WebSocketServer();