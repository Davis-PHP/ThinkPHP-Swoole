<?php

use think\Container;
use think\Facade;
use think\Loader;
use task\Task;

class http
{
    protected $host = '127.0.0.1';
    protected $port = 9502;
    protected $http = null;

    public function __construct()
    {
        $this->http = new Swoole\Http\Server($this->host, $this->port);
        $this->http->set([
            'enable_static_handler' => true, //开启静态文件请求处理功能，需配合 document_root 使用 默认 false
            'document_root' => '/Users/davis/PHP/tp5-swoole-demo/public/static',
            'worker_num' => 4,
            'task_worker_num' => 4
        ]);
        $this->http->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->http->on('request', [$this, 'onRequest']);
        $this->http->on('task', [$this, 'onTask']);
        $this->http->start();
    }

    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id)
    {
        require __DIR__ . '/../thinkphp/base.php';
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

        $_POST['http_server'] = $this->http;

        ob_start();
        try {
            Container::get('app')->run()->send();
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
        $this->http->close();
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
        $task = new Task();
        $method = $data['method'];
        $data = $data['data'];
        $res = $task->$method($data);
        return $res;
    }
}


new http();