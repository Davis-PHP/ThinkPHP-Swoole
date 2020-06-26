<?php


class ws
{
    const host = '127.0.0.1';
    const port = 9502;
    public $ws = null;

    protected $host;
    protected $password;
    protected $port;
    protected $select;
    protected $redis;

    public function __construct()
    {
        $this->host = '127.0.0.1';
        $this->password = '';
        $this->port = 6379;
        $this->select = 1;
        $this->redis = new \Redis();
        $this->redis->pconnect($this->host, $this->port, 5);
        $this->redis->auth($this->password);
        $this->redis->select($this->select);
        $fds = $this->redis->sMembers('Room:Fds');
        if (count($fds) > 0) {
            $this->redis->del('Room:Fds');
        }

        $this->ws = new Swoole\WebSocket\Server(self::host, self::port);
        $this->ws->set([
            'enable_static_handler' => true, //开启静态文件请求处理功能，需配合 document_root 使用 默认 false
            'document_root' => '/Users/davis/PHP/tp5-swoole-demo/public/static',
            'worker_num' => 4,
            'task_worker_num' => 4
        ]);
        $this->ws->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->ws->on('request', [$this, 'onRequest']);
        $this->ws->on('open', [$this, 'onOpen']);
        $this->ws->on('message', [$this, 'onMessage']);
        $this->ws->on('task', [$this, 'onTask']);
        $this->ws->on('close', [$this, 'onClose']);
        $this->ws->start();
    }

    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id)
    {
        require __DIR__ . '/../thinkphp/base.php';
        $_POST['ws_server'] = $this->ws;
    }

    /**
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            $response->end();
            return;
        }
        if (isset($request->server)) {
            foreach ($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if (isset($request->header)) {
            foreach ($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        $_FILES = [];
        if (isset($request->files)) {
            foreach ($request->files as $k => $v) {
                $_FILES[$k] = $v;
            }
        }
        $_GET = [];
        if (isset($request->get)) {
            foreach ($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }
        $_POST = [];
        if (isset($request->post)) {
            foreach ($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }
        $_POST['ws_server'] = $this->ws;
        ob_start();
        try {
            \think\Container::get('app')->run()->send();
        } catch (\Exception $e) {
            $err = "[ERROR] {$e->getMessage()}, {$e->getFile()}, {$e->getLine()}";
            echo $err . PHP_EOL;
        }
        $res = ob_get_contents();
        if (ob_get_length()) {
            ob_end_clean();
        }
        $response->header('content-type', 'text/html;charset=utf-8', true);
        $response->end($res);
    }

    /**
     * @param $ws
     * @param $task_id
     * @param $worker_id
     * @param $data
     * @return string
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function onTask($ws, $task_id, $worker_id, $data)
    {
        $task = new \task\Task();
        $method = $data['method'];
        $data = $data['data'];
        $res = $task->$method($data);
        return $res;
    }


    /**
     * 监听wd连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request)
    {
        $this->redis->sAdd('Room:Fds', $request->fd);
        echo '连接: ' . $request->fd . PHP_EOL;
    }

    /**
     * 监听ws消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame)
    {
        echo '接收到消息: ' . $frame->data . PHP_EOL;
        $ws->push($frame->fd, "发送消息: " . date('Y-m-d H:i:s'));
    }

    /**
     * 监听ws关闭事件
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd)
    {
        $result_1 = $this->redis->exists('Room:Fds');
        if ($result_1) {
            $result_2 = $this->redis->sismember('Room:Fds', $fd);
            if ($result_2) {
                $this->redis->sRem('Room:Fds', $fd);
            }
        }
        echo '关闭: ' . $fd . PHP_EOL;
    }
}

new ws();