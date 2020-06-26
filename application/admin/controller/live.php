<?php


namespace app\admin\controller;


use tool\Redis;

class live
{
    public function push()
    {
        try {
            if (empty($_GET)) {
                return json(['code' => 1, 'msg' => 'push 内容不可为空']);
            }
            $types = [
                1 => '第一节',
                2 => '第二节',
                3 => '第三节',
                4 => '第四节',
            ];
            $teams = [
                1 => [
                    'name' => '马刺',
                    'logo' => '/live/imgs/team1.png',
                ],
                4 => [
                    'name' => '火箭',
                    'logo' => '/live/imgs/team2.png',
                ]
            ];

            $data = [
                'type' => $types[$_GET['type']],
                'title' => !empty($teams[$_GET['team_id']]['name']) ? $teams[$_GET['team_id']]['name'] : '直播员',
                'logo' => !empty($teams[$_GET['team_id']]['logo']) ? $teams[$_GET['team_id']]['logo'] : '',
                'content' => !empty($_GET['content']) ? $_GET['content'] : '',
                'image' => !empty($_GET['image']) ? $_GET['image'] : '',
                'time' => date('H:s', time())
            ];
            $taskData = [
                'method' => 'pushRoom',
                'data' => $data
            ];
            $_POST['ws_server']->task($taskData);
            return json(['code' => 0, 'msg' => 'success']);
        } catch (\Exception $e) {
            $err = "[ERROR] {$e->getMessage()}, {$e->getFile()}, {$e->getLine()}";
            echo $err . PHP_EOL;
        }
    }
}