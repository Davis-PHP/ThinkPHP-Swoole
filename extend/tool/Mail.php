<?php

namespace tool;

use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    /**
     * 发送邮件
     * @param $to
     * @param $subject
     * @param $content
     * @param $mailConf
     * @return string
     * @throws \Exception
     */
    public static function send($to, $subject, $content)
    {
        try {
//            $mailConf = config('mail');
//            $host = $mailConf['host'];
//            $username = $mailConf['username'];
//            $password = $mailConf['password'];
//            $from = $mailConf['from'];
//            $name = $mailConf['name'];

            $host = 'smtp.qq.com';
            $username = '1479714102@qq.com';
            $password = 'xxxxxxx';
            $from = '1479714102@qq.com';
            $name = 'Davis';

            $mail = new PHPMailer();
            // 使用smtp鉴权方式发送邮件
            $mail->isSMTP();
            // smtp需要鉴权 这个必须是true
            $mail->SMTPAuth = true;
            // qq 邮箱的 smtp服务器地址，这里当然也可以写其他的 smtp服务器地址
            $mail->Host = $host;
            // smtp登录的账号 这里填入字符串格式的qq号即可
            $mail->Username = $username;
            // 这个就是之前得到的授权码，一共16位
            $mail->Password = $password;
            $mail->setFrom($from, $name);
            // $to 为收件人的邮箱地址，如果想一次性发送向多个邮箱地址，则只需要将下面这个方法多次调用即可
            $mail->addAddress($to);
            // 该邮件的主题
            $mail->Subject = $subject;
            // 该邮件的正文内容
            $mail->Body = $content;
            // 使用 send() 方法发送邮件
            $send = $mail->send();
            if ($send) { // 发送成功
                return 'success';
            } else { // 发送失败
                $err = "[ERROR] {$mail->ErrorInfo}";
                echo $err . PHP_EOL;
            }
        } catch (\Exception $e) {
            $err = "[ERROR] {$e->getMessage()}, {$e->getFile()}, {$e->getLine()}";
            echo $err . PHP_EOL;
        }
    }
}