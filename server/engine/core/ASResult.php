<?php

namespace APS;

/**
 * 结果包装器(通讯单元)
 * ASResult
 * @package APS\core
 */
class ASResult
{

    /**
     * 状态码
     * @var int
     */
    private $status = 0;

    /**
     * 消息
     * @var string
     */
    private $message;

    /**
     * 内容主体 The content for returning
     * @var null
     */
    private $content;

    /**
     * 签名 Sign of method
     * @var string
     */
    private $sign = "ApsResult";

    /**
     * 时间
     * @var Time
     */
    private $time;

    /**
     * ASResult constructor.
     */
    function __construct(){
        $this->status  = 0;
        $this->message = "NO_MESSAGE";
        $this->content = NULL;
//        $this->time = new Time();
    }

    /**
     * 单例
     * shared
     * @param  int          $status
     * @param  string|null  $message
     * @param  null         $content
     * @param  string|null  $sign
     * @return ASResult
     */
    public static function shared( int $status = 0, string $message = null, $content = null, string $sign = null ):ASResult{
        $result = new static();
        if( $status>0 ){ $result->setStatus($status); }
        if( isset($message) ){ $result->setMessage($message); }
        if( isset($content) ){ $result->setContent($content); }
        if( isset($sign) ){ $result->setSign($sign); }
        return $result;
    }

    public static function fromJSON( string $jsonResult ):ASResult{
        $array = json_decode($jsonResult,true);
        if( !$array ){
            return static::shared(-100,'Decode String To Data Failed',$jsonResult,'ASResult::fromJSON');
        }
        return static::fromArray($array);
    }

    public static function fromArray( array $arrayResult ):ASResult{
        $status  = intval($arrayResult['status']);
        $message = $arrayResult['message'];
        return static::shared($status,$message,$arrayResult['content'],$arrayResult['sign']??'No Sign');
    }

    /**
     * 是否成功 isSucceed
     * @return   boolean
     */
    public function isSucceed():Bool{
        return $this->status === 0;
    }

    /**
     * 获取状态码 getStatus
     * @return Int
     */
    public function getStatus():Int{
        return $this->status;
    }

    /**
     * 设置状态码 SetStatus
     * @param  Int  $status
     */
    public function setStatus( Int $status ){
        $this->status = $status;
    }

    /**
     * 获取消息 getMessage
     * @return String
     */
    public function getMessage():String{
        return $this->message;
    }

    /**
     * 设置消息 setMessage
     * @param  String  $msg
     */
    public function setMessage( String $msg ) {
        $this->message = $msg;
    }

    /**
     * 查询内容 getContent
     * @return mixed
     */
    public function getContent(){
        return $this->content;
    }

    public function getContentOr( $value ){
        return $this->isSucceed() ? $this->content : $value;
    }

    /**
     * 设置主体内容 setContent
     * @param $content
     */
    public function setContent( $content ) {
        $this->content = $content;
    }

    /**
     * 获取签名 getSign
     * @return String
     */
    public function getSign():String{
        return $this->sign;
    }

    /**
     * 设置签名 setSign
     * @param  String  $sign
     */
    public function setSign( String $sign ) {
        $this->sign = $sign;
    }

    /**
     * 输出Result为String格式
     * toString with json_encode
     * @return string
     */
    public function toString():string {

        return $this->convertTo('string');
    }

    /**
     * 输出Result为Array格式
     * toArray
     * @return array
     */
    public function toArray():array{

        return $this->convertTo('array');
    }

    public function convertTo( string $type ){
        $this->time = new Time();
        $res = [
            'status'=>$this->status,
            'message'=>$this->message,
            'content'=>$this->content,
            'sign'=>$this->sign,
            'time'=>$this->getTimeString(),
            'timeStamp'=>$this->getTimeStamp(),
            'timeDuration'=>(microtime(true) - $this->getTimeStamp()),
            'engine'=>'AppSite 2.0',
            'link'=>'appsite.cn'
        ];

        switch ( $type ){
            case 'string':
            return json_encode($res,256);
            case 'array':
            default:
            return $res;
        }
    }

    /**
     * 获取本地化时间 getTimeString
     * @return String
     */
    public function getTimeString():String{
        return $this->time->formatOutput( TimeFormat_FullTime );
    }

    /**
     * 获取时间戳 getTimeStamp
     * @return Int
     */
    public function getTimeStamp():Int{
        return $this->time->time;
    }
}