<?php

namespace APS;

use PHPMailer\PHPMailer\PHPMailer;

include SERVER_DIR."library/phpMailer/src/PHPMailer.php";
include SERVER_DIR."library/phpMailer/src/SMTP.php";
include SERVER_DIR."library/phpMailer/src/Exception.php";

/**
 * SMTP mail
 * SMTP
 * @package APS\extension
 */
class SMTP extends ASObject {

    private $server;
    private $user;
    private $pass;
    private $port;
    private $host;

    private $from;
    private $replyTo;

    private $attachments;

    function __construct($server = NULL, $user = NULL, $pass = NULL, $port = NULL)
    {
        parent::__construct();

        $this->server = $server ?? getConfig('SERVER','SMTP');
        $this->user = $user ?? getConfig('PORT','SMTP');
        $this->pass = $pass ?? getConfig('USER','SMTP');
        $this->port = $port ?? getConfig('PASS','SMTP');

    }

    public function setServer(string $server)
    {
        $this->server = $server;
    }

    public function setPort(int $port)
    {
        $this->port = $port;
    }

    public function setUser(string $user)
    {
        $this->user = $user;
    }

    public function setPass(string $pass)
    {
        $this->pass = $pass;
    }

    public function setHost(string $host)
    {
        $this->host = $host;
    }

    public function setFrom(string $from)
    {
        $this->from = $from;
    }

    public function setReplyTo(string $replyTo, string $replyToName = NULL)
    {
        $this->replyTo = $replyTo;
        if ($replyToName) {
            $this->replyToName = $replyToName;
        }
    }

    public function addAttachment(string $path, string $name = NULL, string $type = NULL)
    {

        $this->attachments = $this->attachments ?? [];
        $this->attachments[] = ['file' => $path, 'name' => $name ?? ENCRYPT::shortId(12), 'type' => $type];

    }

    /**
     * 邮件验证
     * verify
     * @param  string  $emailAddress  收件邮箱地址
     * @param  string  $scope         作用域
     * @return \APS\ASResult
     * @throws \PHPMailer\PHPMailer\Exception
     * @version  1.0
     */
    public function verify( string $emailAddress, string $scope = 'verify')
    {

        $beginVerify = AccessVerify::common()->begin($emailAddress,getConfig('ACCESSVERIFY_VALID')??300,$scope);

        if (!$beginVerify->isSucceed()) {
            return $this->take($emailAddress)->error(500, i18n('SMTP_SEND_FAL'),'SMTP::verify');
        }

        return $this->sendWithTemplate($emailAddress, ['code' => $beginVerify->getContent()], $scope);

    }

    /**
     * [sendWithTemplate 通过模板发送]
     * @Author   Sprite                   hello@shezw.com http://donsee.cn
     * @DateTime 2019-07-31T15:31:55+0800
     * @param  string  $emailAddress  [收件邮箱地址]
     * @param  array   $params        [参数 自动混合到对应模板]
     * @param  string  $template      [模板 对应在MAILS常量中]
     * @return   [array]                                  [result对象]
     * @throws \PHPMailer\PHPMailer\Exception
     * @version  [1.0]
     */
    public function sendWithTemplate(string $emailAddress, array $params, string $template = 'verify'){

        return $this->send($emailAddress, MAILS[LOCATE::$LANG][$template]['subject'], MIXER::mix($params, MAILS[LOCATE::$LANG][$template]['content']));

    }

    /**
     * 发送邮件
     * @param  string  $receiver  收件人
     * @param  string  $subject   主题
     * @param  string  $content   内容
     * @param  string  $text      纯文本模式内容  Content display in plain-text mode
     * @return \APS\ASResult
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send(string $receiver, string $subject, string $content, string $text = NULL)
    {
        $mail = new PHPMailer();

        # Tell PHPMailer to use SMTP
        $mail->isSMTP();

        # Enable SMTP debugging
        # SMTP::DEBUG_OFF = off (for production use)
        # SMTP::DEBUG_CLIENT = client messages
        # SMTP::DEBUG_SERVER = client and server messages

        $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_OFF;
        $mail->Host = $this->server;
        $mail->Port = $this->prot ?? 465;
        $mail->SMTPAuth = true;
        $mail->Username = $this->user;
        $mail->Password = $this->pass;

        // 检查发件人
        $this->from && $mail->setFrom($this->from);
        // 检查回复人
        $this->replyTo && $mail->addReplyTo($this->replyTo);

        $mail->addAddress($receiver);
        $mail->Subject = $subject;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML($content);

        // 检查纯文本模式
        isset($text) && $mail->AltBody = $text;

        // 检查附件
        if ($this->attachments && !empty($this->attachments)) {

            foreach ($this->attachments as $i => $attach) {
                $mail->addAttachment($attach['file'], $attach['name']);
            }
        }

        $state = $mail->send();

        return !$state ?
                $this->take($state)->error(500, i18n('SMTP_SEND_FAL'), "SMTP->send") :
                $this->take($receiver)->success(i18n('SMTP_SEND_SUC'),'SMTP->send');
    }


}

