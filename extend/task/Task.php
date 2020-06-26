<?php

namespace task;

use tool\Mail;
use tool\Redis;

class Task
{
    /**
     * @param $data
     * @return string
     * @throws \Exception
     */
    public function sendMail($data)
    {
        $mailNum = $data['mailNum'];
        $randNum = $data['randNum'];
        $result = Mail::send($mailNum,
            "【Davis 】登录验证码: {$randNum}",
            "【Davis】登录验证码: {$randNum}, 用于验证登录, 请勿泄露和转发!"
        );
        if ($result == 'success') {
            $redis = new Redis();
            $key = "Mails:{$mailNum}";
            $redis->set($key, 120, $randNum);
            return $result;
        }
        return 'error';
    }

    /**
     * @param $data
     */
    public function pushRoom($data)
    {
        $redis = new Redis();
        $res = $redis->sMembers('Room:Fds');
        if (count($res) > 0) {
            foreach ($res as $re) {
                $_POST['ws_server']->push($re, json_encode($data));
            }
        }
    }
}