<?php

use think\Container;

$http = new Swoole\Http\Server('127.0.0.1', 9502);

$http->set([
    'enable_static_handler' => true, //开启静态文件请求处理功能，需配合 document_root 使用 默认 false
    'document_root' => '/Users/davis/PHP/tp5-swoole-demo/public/static',
    'worker_num' => 5,
]);

//$http->on('WorkerStart', function (Swoole\Server $server, $worker_id) {
//});

$http->on('request', function ($request, $response) use ($http) {
    require __DIR__ . '/../thinkphp/base.php';

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
    ob_start();
    try {
        Container::get('app')->run()->send();
    } catch (Exception $e) {
        $err = "[ERROR] {$e->getMessage()}, {$e->getFile()}, {$e->getLine()}";
        echo $err . PHP_EOL;
    }
    $res = ob_get_contents();
    if (ob_get_length()) {
        ob_end_clean();
    }
    $response->header('content-type', 'text/html;charset=utf-8', true);
    $response->end($res);
    $http->close();
});

$http->start();