<?php
/**
 * Created by PhpStorm
 * Author：FireRabbit
 * Date：2021/2/18
 * Time：14:29
 **/


namespace FireRabbit\Module\Mail;


use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    protected static $mail;
    protected static $config;

    /**
     * 轮询计数器
     * @var int
     */
    protected static $sort = 0;

    /**
     * 邮件节点
     * @var array
     */
    protected static $pool = [];

    protected $subject, $body, $altBody, $reciverMail;

    public static function setConfig($config)
    {
        self::$config = $config;
        self::$pool = $config['pool'];

        self::$mail = new PHPMailer();
        self::$mail->isSMTP();
        self::$mail->SMTPAuth = true;
        self::$mail->SMTPDebug = $config['debug'];
        self::$mail->isHTML($config['html']);
        self::$mail->SMTPSecure = $config['secure'];
    }

    public function subject($title)
    {
        $this->subject = $title;
        return $this;
    }

    public function body($html)
    {
        $this->body = $html;
        return $this;
    }

    public function altBody($text)
    {
        $this->altBody = $text;
        return $this;
    }

    public function address($mail)
    {
        $this->reciverMail = $mail;
        return $this;
    }

    public function send()
    {
        $node = self::$pool[self::$sort];

        self::$sort++;

        if (self::$sort >= count(self::$pool)) {
            self::$sort = 0;
        }

        // 载入节点配置
        self::$mail->Host = $node['host'];
        self::$mail->Port = $node['port'];
        self::$mail->Username = $node['user'];
        self::$mail->Password = $node['password'];
        self::$mail->setFrom($node['user'], $node['name']);
        self::$mail->addReplyTo($node['user'], $node['name']);

        // 生成邮件信息
        self::$mail->addAddress($this->reciverMail);
        self::$mail->Subject = $this->subject;
        self::$mail->Body = $this->body;
        self::$mail->AltBody = $this->altBody ?? '';

        self::$mail->send();
    }
}
