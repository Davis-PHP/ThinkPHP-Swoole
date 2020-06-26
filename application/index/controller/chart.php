<?php


namespace app\index\controller;


class chart
{
    public function index()
    {
        if (empty($_POST['content']) || empty($_POST['game_id'])) {
            return json(['code' => 1, 'msg' => '内容不可为空']);
        }
        $data = [
            'user' => '用户' . rand(1, 100000),
            'content' => $_POST['content']
        ];
        foreach ($_POST['ws_server']->ports[1]->connections as $fd) {
            $_POST['ws_server']->push($fd, json_encode($data));
        }
        return json(['code' => 0, 'msg' => 'success']);
    }
}