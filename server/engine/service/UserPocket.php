<?php

namespace APS;

/**
 * 用户钱包扩展
 * UserPocket
 *
 * @mark !Notice:
        在钱包功能中不应该包含 账目处理
        账目处理应该先通过钱包方法返回的结果再交由具体的事务单独判断处理
 * @package APS\service\User
 */
class UserPocket extends ASModel{

    /**
     * 所属用户id
     * @var string
     */
    public $userid;

    const table     = "user_pocket";
    const comment   = '用户钱包';
    const primaryid = "userid";
    const addFields = [
        'userid',
        'point','balance',
        'status','type','createtime','lasttime'
    ];
    const updateFields = [
        'userid',
        'point','balance',
        'status','type','createtime','lasttime'
    ];
    const detailFields =[
        'userid',
        'point','balance',
        'status','type','createtime','lasttime'
    ];
    const publicDetailFields = [
        'userid',
        'point','balance',
        'status','type','createtime','lasttime'
    ];
    const overviewFields = [
        'userid',
        'point','balance',
        'status','type','createtime','lasttime'
    ]; // 概览支持字段
    const listFields = [
        'userid',
        'point','balance',
        'status','type','createtime','lasttime'
    ];
    const publicListFields = [
        'userid',
        'point','balance',
        'status','type','createtime','lasttime'
    ];
    const filterFields = [
        'userid',
        'point','balance',
        'status','type','createtime','lasttime'
    ];
    const depthStruct = [
        'point'=>DBField_Int,
        'balance'=>DBField_Int,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'userid'=>     ['type'=>DBField_String,  'len'=>8,   'nullable'=>0,                      'cmt'=>'用户ID', 'idx'=>DBIndex_Unique,],
        'balance'=>    ['type'=>DBField_Int,     'len'=>13,  'nullable'=>0,  'dft'=>0,           'cmt'=>'余额 100倍 分为单位 RMB'],
        'point'=>      ['type'=>DBField_Int,     'len'=>13,  'nullable'=>0,  'dft'=>0,           'cmt'=>'积分 带小数点'],
        'status'=>     ['type'=>DBField_String,  'len'=>12,  'nullable'=>0,  'dft'=>'enabled',   'cmt'=>'状态'],
        'type'=>       ['type'=>DBField_String,  'len'=>16,  'nullable'=>1,                      'cmt'=>'类型 暂时没用'],

        'createtime'=> ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',    'idx'=>DBIndex_Index, ],
        'lasttime'=>   ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];


    function __construct( string $userid )
    {
        parent::__construct();
        $this->userid = $userid;
    }

    /**
     * 初始化用户钱包
     * init
     * @param DBValues $data
     * @return ASResult
     */
    public function init( DBValues $data ):ASResult
    {
        $data->purify( static::addFields );
        $data->set(static::primaryid)->string($this->userid);

        return $this->add($data);
    }


    /**
     * 字段增长
     * addition
     * @param  int     $size
     * @param  string  $field
     * @return ASResult
     */
    public function addition( int $size = 1, string $field = 'point' ): ASResult
    {
        $this->params = ['field'=>$field,'conditions'=>"userid='{$this->userid}'",'size'=>$size];
        $this->result = $this->getDB()->increase($field,static::table,static::uidCondition($this->userid),$size);

        $this->record('POCKET_ADDITION','POCKET->addition');
        return $this->result->isSucceed() ? $this->success(i18n('POCKET_INCREASE_SUC')) : $this->error(500,i18n('SYS_ERR')) ;
    }

    /**
     * 字段减少
     * reduce
     *
     * 外部调用时 注意检查对应余额是否充足
     * run enough method to check before reduce
     *
     * @param  int     $size
     * @param  string  $field
     * @return ASResult
     */
    public function reduce( int $size = 1, string $field = 'point' ): ASResult
    {
        $this->params = ['field'=>$field,'conditions'=>["userid"=>$this->userid],'size'=>$size];
        $this->result = $this->getDB()->decrease($field,static::table,static::uidCondition($this->userid),$size);

        $this->record('POCKET_REDUCE','POCKET::reduce');
        return $this->result->isSucceed() ? $this->success(i18n('POCKET_DECREASE_SUC')) : $this->error(500,i18n('SYS_ERR')) ;
    }

    /**
     * 增加积分
     * addition point
     * @param  int  $size
     * @return ASResult
     */
    public function additionPoint( int $size = 1 ): ASResult
    {
        return $this->addition($size,'point');
    }

    /**
     * 减少积分
     * reduce point
     * @param  int  $size
     * @return ASResult
     */
    public function reducePoint( int $size = 1 ): ASResult
    {
        return $this->reduce($size,'point');
    }

    /**
     * 增加余额
     * addition balance
     * @param  int  $size
     * @return ASResult
     */
    public function additionBalance( int $size = 1 ): ASResult
    {
        return $this->addition($size,'balance');
    }

    /**
     * 减少余额
     * reduce balance
     * @param  int  $size
     * @return ASResult
     */
    public function reduceBalance( int $size = 1 ): ASResult
    {
        return $this->reduce($size,'balance');
    }


    /**
     * 钱包余额
     * balance
     * @param  string  $mode
     * @return ASResult
     */
    public function balance( string $mode = 'point' ): ASResult
    {
        return $this->get($mode,$this->userid);
    }

    /**
     * 检查余额是否足够
     * enough
     * @param  Number  $quantity  所需额度
     * @param  string  $mode      钱包或积分
     * @return bool
     */
    public function enough( $quantity, $mode='point' ): bool
    {
        $quantity = (double)$quantity;
        return $this->count(
            DBConditions::init(static::table)
                ->where(static::primaryid)->equal($this->userid)
                ->and($mode)->biggerAnd($quantity)
            )->getContent()>0;
    }

    /**
     * 余额是否足够
     * enoughBalance
     * @param  int  $size
     * @return bool
     */
    public function enoughBalance( int $size = 1 ): bool
    {
        return $this->enough($size,'balance');
    }

    /**
     * 积分是否足够
     * @param  int  $size
     * @return bool
     */
    public function enoughPoint( int $size = 1 ): bool
    {
        return $this->enough($size,'point');
    }

    /**
     * 禁用钱包
     * blockPocket
     * @param  string|null  $uid
     * @return ASResult
     */
    public function block(string $uid = null): ASResult{ return $this->status($this->userid,'blocked'); }

    /**
     * 锁定钱包
     * lock
     * @return ASResult
     */
    public function lock():ASResult { return $this->status($this->userid,'locked'); }

    /**
     * 解除锁定钱包
     * unlock
     * @return ASResult
     */
    public function unlock():ASResult { return $this->status($this->userid,'enabled'); }

    /**
     * 异常锁定钱包
     * exception
     * @return ASResult
     */
    public function exception():ASResult { return $this->status($this->userid,'exception'); }


    /**
     * 是否被锁定
     * isLocked
     * @return bool
     */
    public function isLocked( ): bool
    {
        return $this->count(
            DBConditions::init(static::table)
                ->where(static::primaryid)->equal($this->userid)
                ->and('status')->belongTo(['locked','exception'])
        )->getContent() > 0;
    }

    /**
     * 清算账户
     * clearPocket
     * @param  string  $mode
     * @return ASResult
     */
    public function clear( string $mode='balance' ): ASResult
    {
        $this->record('POCKET_CLEAR','POCKET->clear',['mode'=>$mode,'userid'=>$this->userid]);

        return $this->update(DBValues::init($mode)->number(0),$this->userid);
    }

}