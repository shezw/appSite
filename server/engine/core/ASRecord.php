<?php

namespace APS;

/**
 * 日志、统计管理器
 * ASRecord

    !! 重要信息
    除了系统信息 如: "系统颁发的证书,令牌, 系统运行(安全检测) 需要的设备信息等"
    属于用户隐私信息的请在此处清空,全局设置 RECORD_PRIVACY 设置为空 或记录时手动删除
    详情咨询相关法律人士, 若侵犯用户隐私涉及到法律问题, 与本程序原作者无关

    !! NOTICE
    Except for system information such as "system-issued certificates, tokens, device information required for system operation (security test), etc."
    Belongs to the user"s privacy information here empty, the global settings RECORD_PRIVACY # config.php # set to empty or record manually delete.
    Details Consult the relevant legal persons, if the violation of user privacy involves legal issues, and the original author of this program exemption.

 * @package APS\core
 */
class ASRecord extends ASObject{

    /**
     * 用户id
     * @var string | null
     */
    private $userid;

    /**
     * 对象id
     * @var string | null
     */
    private $itemid;

    /**
     * 状态码
     * @var int
     */
    private $status;

    /**
     * 事件
     * @var string
     */
    private $event;

    /**
     * 记录内容
     * @var string  (JSON)
     */
    private $content;

    /**
     * 事件类型
     * @var string Function,System
     */
    private $type;

    /**
     * 签名头信息
     * @var string
     */
    private $sign;

    /**
     * 记录分类
     * @var string  System, User, Error, Thirdparty
     */
    private $category;

    /**
     * 数据库联接
     * @var \APS\ASDB
     */
    public $DB;

    function __construct( array $_= null , ASDB $db = null )
    {
        parent::__construct();

        $this->userid      = $_["userid"]    ?? "SYSTEM" ;
        $this->status      = $_["status"]    ?? 0 ;
        $this->event       = $_["event"]     ?? "NAN" ;
        $this->content     = $_["content"]   ?? "NAN" ;
        $this->sign        = $_["sign"]      ?? "AppSite" ;
        $this->category    = $_["category"]  ?? "system" ;
    }

    /**
     * 全局单例
     * shared
     * @param  array|null      $_  日志记录参数
     * @param  \APS\ASDB|null  $db 指定数据库链接
     * @return \APS\ASRecord
     */
    public static function shared( array $_= null , ASDB $db = null ):ASRecord{

        if ( !isset($GLOBALS['ASRecord']) ){
            $GLOBALS['ASRecord'] = new ASRecord( $_,$db );
        }
        return $GLOBALS['ASRecord'];
    }

    /**
     * 设置独立数据库
     * setDB
     * @param  \APS\ASDB  $dbInstance
     * @return $this
     */
    public function setDB( ASDB $dbInstance ){
        $this->DB = $dbInstance;
        return $this;
    }

    protected function getDB():ASDB{
        if( !isset($this->DB) ){
            $this->DB = _ASDB();
        }
        return $this->DB;
    }

    /**
     * 设定操作用户id
     * set User id
     * @param  string  $userid
     * @return $this
     */
    public function setUserid( string $userid ){

        $this->userid  =  $userid;
        return $this;
    }

    /**
     * 设定记录分类 (影响数据库记录表)
     * setCategory (Change record table)
     * @param  string  $category
     * @return $this
     */
    public function setCategory( string $category ){

        $this->category  =  $category;
        return $this;
    }

    /**
     * 设定操作对象id
     * setItemid
     * @param  string  $itemid
     * @return $this
     */
    public function setItemid( string $itemid ){

        $this->itemid  =  $itemid;
        return $this;
    }

    /**
     * 设置记录类型
     * setType
     * @param  string  $type
     * @return $this
     */
    public function setType( string $type ){

        $this->type  =  $type;
        return $this;
    }


    /**
     * 添加记录
     * add record to database
     * @param  array   $_           数组参数形式
     * @param  int     $status      状态码
     * @param  string  $itemid      操作对象id
     * @param  string  $type        记录类型
     * @param  array   $content     记录内容 array -> JSON
     * @param  string  $userid      用户id
     * @param  string  $sign        操作签名
     * @param  string  $recordType  记录分类
     * @param  bool    $saveToFile  以文件形式记录
     * @return \APS\ASResult
     */
    public function add( $_ = null, $status = null, $itemid = null, $type = null, $content = null, $recordType=null, $userid = null, $sign = null, $saveToFile = false ){

        if(!getConfig("RECORD_ENABLE")){ return $this->success("RECORDER CONF IS NOT ENABLED","RECORD->add"); }

        if (gettype($_)=="array") {

            $status   = $_["status"]   ?? 0;
            $type     = $_["type"]     ?? "FUNCTION";
            $event    = $_["event"]    ?? "LOG";
            $content  = $_["content"]  ?? null;
            $itemid   = $_["itemid"]   ?? $this->itemid;
            $sign     = $_["sign"]     ?? $this->sign;
            $userid   = $_["userid"]   ?? $this->userid;
            $recordType = $_["category"] ?? $this->category;

        }else{

            $status   = $status   ?? 0;
            $type     = $type     ?? "FUNCTION";
            $event    = $_        ?? "LOG";
            $content  = $content  ?? null;
            $itemid   = $itemid   ?? $this->itemid;
            $sign     = $sign     ?? $this->sign;
            $userid   = $userid   ?? $this->userid;
            $recordType = $recordType ?? $this->category;

        }

        if ( $userid!=="1000" && $userid!=="system" ) {
            $recordType = $recordType ? ( $recordType=="thirdparty"||$recordType=="admin"||$recordType=="user" ? $recordType :"system" ) : "user";
        }

        if ($recordType!=="system" && $recordType !=="user" && $recordType !=="admin" && $recordType !=="thirdparty" ) {
            $this->error(10025,"Not valid Category!","RECORD->add");
        }

        $table    = "record_".$recordType;

        // 检测必填项
        if(!isset($userid )  ){ return $this->error(10086,i18n("SYS_PARA_REQ"),"ASRecord->add"); }
        if(!isset($content ) ){ return $this->error(10086,i18n("SYS_PARA_REQ"),"ASRecord->add"); }

        $data = [
            "userid"   => $userid,
            "itemid"   => $itemid,
            "type"     => $type,
            "status"   => $status,
            "event"    => $event,
            "sign"     => $sign,
            "host"     => $this->getHost(),
            "ip"       => $this->getRealIP(),
            "content"  => $content,
        ];

        if($saveToFile){

            File::write( static::generateLogName("$table/"), static::generateLogLine($data), getConfig('LOG_DIR') ?? SERVER_DIR.'/RECLOG/' );
            return $this->success();
        }else{

            return $this->getDB()->add($data,$table);
        }

    }

    /**
     * 进行文件记录
     * log by file
     * @param  array   $_
     * @param  int     $status
     * @param  string  $itemid
     * @param  string  $type
     * @param  array   $content
     * @param  string  $category
     * @param  string  $userid
     * @param  string  $sign
     * @return \APS\ASResult
     */
    public function log( $_ = null, $status = null, $itemid = null, $type = null, $content = null, $category=null, $userid = null, $sign = null ){

        return $this->add( $_, $status, $itemid, $type, $content, $category, $userid, $sign, true );
    }

    /**
     * 生成记录文件名
     * generate Log File Name
     * @param  string|null  $pre
     * @param  string       $mode
     * @return string
     */
    public static function generateLogName( string $pre = null, string $mode = 'hour' ){

        $MODES = [
            'month'=>"Ym",
            'week'=>"Y/W",
            'day'=>"Y/m/d",
            'hour'=>"Y/m/dH",
            'minute'=>"Y/m/d/Hi",
            'second'=>"Y/m/d/H/is"
        ];

        $format = $MODES[$mode] ?? $MODES['day'];

        return $pre.date($format).'.log';
    }

    /**
     * 生成记录行数据
     * generateLogLine
     * @param          $content
     * @param  string  $mode
     * @return string
     */
    public static function generateLogLine( $content , $mode = 'json' ){

        switch ($mode) {
            case 'json':
                $line = json_encode($content)."\n";
                break;

            default:
                # code...
                break;
        }
        return $line;
    }


    /**
     * 获取当前来源域名
     * getHost
     * @return mixed|string
     */
    public function getHost(){

        return isset($_SERVER["HTTP_REFERER"])||isset($_SERVER["HTTP_ORIGIN"]) ? ($_SERVER["HTTP_REFERER"]?parse_url($_SERVER["HTTP_REFERER"])["host"]:"NO HTTP_REFERER ".$_SERVER["HTTP_ORIGIN"]) : "NAN";
    }


    /**
     * 获取真实IP
     * getRealIP
     * @return array|false|mixed|string
     */
    public function getRealIP(){

        //判断服务器是否允许$_SERVER
        if(isset($_SERVER)){
            if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            }elseif(isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            }elseif(isset($_SERVER["REMOTE_ADDR"])){
                $realip = $_SERVER["REMOTE_ADDR"];
            }else{
                $realip = "NAN";
            }
        }else{
            //不允许就使用getenv获取
            if(getenv("HTTP_X_FORWARDED_FOR")){
                $realip = getenv( "HTTP_X_FORWARDED_FOR");
            }elseif(getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            }else{
                $realip = getenv("REMOTE_ADDR");
            }
        }

        return $realip;
    }

}