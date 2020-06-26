<?php

namespace app\index\controller;

use tool\Redis;

class login
{
    /**
     * 校验验证码登录
     * @return \think\response\Json
     */
    public function login()
    {
        try {
            $mailNum = $_GET['mail'];
            if (empty($mailNum)) return json(['code' => 1, 'msg' => '邮箱地址必填!']);
            $codeNum = $_GET['code'];
            if (empty($codeNum)) return json(['code' => 1, 'msg' => '验证码必填!']);
            $key = "Mails:{$mailNum}";
            $redis = new Redis();
            $res = $redis->get($key);
            if ($res == 'error') return json(['code' => 1, 'msg' => '验证码已过期!']);
            if ($res !== $codeNum) return json(['code' => 1, 'msg' => '验证码错误!']);
            if ($res == $codeNum) {
                $redis->del($key);
                $userKey = "Users:{$mailNum}";
                $data = [
                    'user' => $mailNum,
                    'key' => md5($userKey),
                    'time' => date('Y-m-d H:i:s'),
                    'isLogin' => true
                ];
                $userRes = $redis->set($userKey, -1, json_encode($data));
                if ($userRes) {
                    return json(['code' => 0, 'msg' => 'success']);
                }
            }
            return json(['code' => 1, 'msg' => '登录失败!']);
        } catch (\Exception $e) {
            $err = "[ERROR] {$e->getMessage()}, {$e->getFile()}, {$e->getLine()}";
            return json(['code' => 1, 'msg' => $err]);
        }
    }

    /**
     * 发送登录验证码
     * @return \think\response\Json
     */
    public function sendMail()
    {
        try {
            $mailNum = $_GET['mail'];
            if (empty($mailNum)) return json(['code' => 1, 'msg' => '邮箱地址必填!']);
            $checkMail = filter_var($mailNum, FILTER_VALIDATE_EMAIL);
            if (empty($checkMail)) return json(['code' => 1, 'msg' => '邮箱地址格式错误!']);
            $randNum = rand(100000, 999999);
            $data = [
                'method' => 'sendMail',
                'data' => [
                    'mailNum' => $mailNum,
                    'randNum' => $randNum
                ]
            ];
            $_POST['ws_server']->task($data);
            return json(['code' => 0, 'msg' => 'success']);
        } catch (\Exception $e) {
            $err = "[ERROR] {$e->getMessage()}, {$e->getFile()}, {$e->getLine()}";
            return json(['code' => 1, 'msg' => $err]);
        }
    }
}
