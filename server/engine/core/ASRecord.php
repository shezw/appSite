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
class ASRecord extends ASModel {

    const table = 'record_system';
    const comment = "系统日志";

    const Mode_System     = 'system';
    const Mode_User       = 'user';
    const Mode_Admin      = 'admin';
    const Mode_ThirdParty = 'thirdparty';

    const tableStruct = [

        'saasid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas','idx'=>DBIndex_Index,],
        'userid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'用户ID' , 'idx'=>DBIndex_Index ],
        'itemid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'用户ID' , 'idx'=>DBIndex_Index ],
        'type'=>     ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'类型',    'dft'=>'success', 'idx'=>DBIndex_Index ],
        'status'=>   ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'success',  ],
        'event'=>    ['type'=>DBField_String,    'len'=>64,  'nullable'=>0,  'cmt'=>'事件' ],
        'content'=>  ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'内容 k-v json' ],
        'sign'=>     ['type'=>DBField_String,    'len'=>63,  'nullable'=>1,  'cmt'=>'签名' ],
        'ip'=>       ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'签名' ],
        'host'=>     ['type'=>DBField_String,    'len'=>32,  'nullable'=>1,  'cmt'=>'签名' ],

        'createtime'=>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',      'idx'=>DBIndex_Index, ],
        'lasttime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];

    const depthStruct = [
        'content'=>DBField_Json,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp
    ];

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
     * 签名头信息
     * @var string
     */
    private $sign;

    /**
     * 记录分类
     * @var string  System, User, Error, Thirdparty
     */
    private $mode;

    /**
     * 数据库联接
     * @var ASDB
     */
    public $DB;

    function __construct( array $_= null )
    {
        parent::__construct();

        $this->userid      = $_["userid"]    ?? "SYSTEM" ;
        $this->status      = $_["status"]    ?? 0 ;
        $this->event       = $_["event"]     ?? "NAN" ;
        $this->content     = $_["content"]   ?? "NAN" ;
        $this->sign        = $_["sign"]      ?? "AppSite" ;
        $this->mode        = $_["mode"]      ?? static::Mode_System ;
    }

    /**
     * 全局单例
     * shared
     * @param array|null $_ 日志记录参数
     * @return ASRecord
     */
    public static function shared( array $_= null ):ASRecord{

        if ( !isset($GLOBALS['ASRecord']) ){
            $GLOBALS['ASRecord'] = new ASRecord( $_ );
        }
        return $GLOBALS['ASRecord'];
    }

    /**
     * 设置独立数据库
     * setDB
     * @param ASDB $dbInstance
     * @return $this
     */
    public function setDB( ASDB $dbInstance ): ASRecord
    {
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
    public function setUserid( string $userid ): ASRecord
    {

        $this->userid  =  $userid;
        return $this;
    }

    /**
     * 设定记录分类 (影响数据库记录表)
     * setCategory (Change record table)
     * @param  string  $category
     * @return $this
     */
    public function setCategory( string $category ): ASRecord
    {

        $this->category  =  $category;
        return $this;
    }

    /**
     * 设定操作对象id
     * setItemid
     * @param  string  $itemid
     * @return $this
     */
    public function setItemid( string $itemid ): ASRecord
    {
        $this->itemid  =  $itemid;
        return $this;
    }

    /**
     * 添加记录
     * add record to database
     * @param  array   $_           数组参数形式
     *                 - int     $status      状态码
     *                 - string  $itemid      操作对象id
     *                 - string  $type        记录类型
     *                 - array   $content     记录内容 array -> JSON
     *                 - string  $userid      用户id
     *                 - string  $sign        操作签名
     *                 - string  $recordType  记录分类
     * @param  bool    $saveToFile  以文件形式记录
     * @return ASResult
     */
    public function add( $_ = null, $saveToFile = false ): ASResult
    {
        if(!getConfig("RECORD_ENABLE")){ return $this->success("RECORDER CONF IS NOT ENABLED","RECORD->add"); }

        $status   = $_["status"]   ?? 0;
        $type     = $_["type"]     ?? "System";
        $event    = $_["event"]    ?? "Log";
        $content  = $_["content"]  ?? null;
        $itemid   = $_["itemid"]   ?? $this->itemid;
        $sign     = $_["sign"]     ?? $this->sign;
        $userid   = $_["userid"]   ?? $this->userid;
        $recordType = $_["mode"] ?? $this->mode;

        if ( $userid!=="1000" && $userid!=="system" ) {
            $recordType = $recordType ? ( $recordType=="thirdparty"||$recordType=="admin"||$recordType=="user" ? $recordType :"system" ) : "user";
        }

        if ( in_array($recordType,[static::Mode_System,static::Mode_User]) ) {
            $recordType = !in_array( $userid, ['SYSTEM','1000','system'] ) ? static::Mode_User : static::Mode_System;
        }else if( !in_array($recordType, [static::Mode_Admin,static::Mode_ThirdParty] ) ){
            $this->error(10025,"Not valid Mode!","RECORD->add");
        }

        $table    = "record_".$recordType;

        // 检测必填项
        if(!isset($userid )  ){ return $this->error(10086,i18n("SYS_PARA_REQ"),"ASRecord->add"); }
        if(!isset($content ) ){ return $this->error(10086,i18n("SYS_PARA_REQ"),"ASRecord->add"); }

        if ( $content instanceof DBValues ){
            $content = $content->toArray();
        }
        if( $content instanceof DBConditions ){
            $content = $content->toArray();
        }

        $data = DBValues::init($table)
            ->set("userid" )->string($userid)
            ->set("itemid" )->string($itemid)
            ->set("type"   )->string($type)
            ->set("status" )->string($status)
            ->set("event"  )->string($event)
            ->set("sign"   )->string($sign)
            ->set("host"   )->string($this->getHost())
            ->set("ip"     )->string($this->getRealIP())
            ->set("content")->jsonIf($content)
            ->set('saasid')->stringIf(saasId());

        if($saveToFile){

            File::write( static::generateLogName("{$table}/"), static::generateLogLine($data->toArray()), getConfig('LOG_DIR') ?? SERVER_DIR.'/RECLOG/' );
            return $this->success();
        }else{

            return $this->getDB()->add($data,$table );
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
     * @return ASResult
     */
    public function log( $_ = null, $status = null, $itemid = null, $type = null, $content = null, $category=null, $userid = null, $sign = null ): ASResult
    {
        return $this->add($_,true );
    }

    /**
     * 生成记录文件名
     * generate Log File Name
     * @param  string|null  $pre
     * @param  string       $mode
     * @return string
     */
    public static function generateLogName( string $pre = null, string $mode = 'hour' ): string
    {

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
    public static function generateLogLine( $content , $mode = 'json' ): string
    {

        switch ($mode) {
            case 'json':
                $line = json_encode($content)."\n";
                break;

            default:
                $line = (string)$content;
                break;
        }
        return $line;
    }


    /**
     * 获取当前来源域名
     * getHost
     * @return mixed|string
     */
    public function getHost(): string
    {

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
                $realIP = $_SERVER["HTTP_X_FORWARDED_FOR"];
            }elseif(isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realIP = $_SERVER["HTTP_CLIENT_IP"];
            }elseif(isset($_SERVER["REMOTE_ADDR"])){
                $realIP = $_SERVER["REMOTE_ADDR"];
            }else{
                $realIP = "NAN";
            }
        }else{
            //不允许就使用getenv获取
            if(getenv("HTTP_X_FORWARDED_FOR")){
                $realIP = getenv( "HTTP_X_FORWARDED_FOR");
            }elseif(getenv("HTTP_CLIENT_IP")) {
                $realIP = getenv("HTTP_CLIENT_IP");
            }else{
                $realIP = getenv("REMOTE_ADDR");
            }
        }

        return $realIP;
    }

}