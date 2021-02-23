<?php

namespace APS;

/**
 * 内区块链
 * Inner Block Chain
 *
 * 服务端内部区块链数据支持
 *
 * 默认在插入新块的时候会进行插入锁、插入结束(成功或失败)解锁。
 * 如果希望不影响其他部分性能、Redis服务器应该指定一个独立数据库处理。
 * 不支持Redis时自动使用MySQL数据库、采用插入锁表的方式。
 *
 * 考虑到区块链本身的实时性要求很高、所以应当采用Redis主、MySQL辅（备份）的方式。
 * 其中Redis主数据库集中维护实时数据提交和计算。
 * MySQL数据库作为一个定时的数据备份，这样避免了MySQL锁的性能瓶颈，通过异步机制或队列管道进行数据的备份。
 * 其中长期不被读取的数据可以从Redis服务器中删除、节省内存空间。
 *
 * Server internal block-chain data support
 *
 * Lock inserts when insert a new block by default, and unlock at the end of the insert done (success or failure).
 * If you want to not affect other parts of the performance, you should specify a separate Redis database for processing.
 * Automatically use MySQL database when Redis is not supported, and lock write when insert block.
 *
 * Considering the high real-time requirements of the blockchain itself, you should use the Redis master and MySql assistant (backup).
 * The Redis master database centrally maintains real-time data submission and calculations.
 * MySQL database as a timed data backup, this avoids the performance bottleneck of MySQL lock, data backup through asynchronous mechanism or queue pipeline.
 * Data that has not been read for a long time can be deleted from the Redis server, saving memory space.
 *
 * @link( https://shezw.com/archives/122/, about)
 * @version 1.0
 * @package APS\service
 */
class IBChain extends ASModel {

    /**
     * Chain context 链上下文(不包含当前块)
     * 用于存储当前块所处的上下文环境 [ 以blockID初始化时前后各2个, 默认以最新block初始化 上下文为最新2个 ]
     * Used to store the context in which the current block is located
     * 2 blocks before and after when initialization with blockID.
     * In other cases, the latest block initialization context is the latest 2 by default]
     */
    private $context;

    /**
     * Current block 当前块
     * 指向初始化ID的块,没有初始化ID则指向最新一块
     */
    private $current;

    /**
     * Temporary block 临时块 用于创建新块
     */
    private $temporary;

    function __construct( $blockID = null ){

        parent::__construct();

        if(isset($blockID)){

            $checkIndex = _ASDB()->get( ['id'], static::$table, [static::$primaryid => $blockID] );
            $blockIndex = $checkIndex->isSucceed() ? $checkIndex->getContent()['id'] : 0 ;
        }

        $condition = isset($blockIndex) ? ['id'=>'[[<=]]'.$blockIndex] : [];

        $listChain = $this->list(array_merge($condition,['uid'=>$blockID]),1,3,'createtime DESC');

        if( !$listChain->isSucceed() ){ return $listChain; }

        $this->current = $listChain->getContent()[0];

        array_splice($listChain['content'], 0,1);

        $this->context = $listChain['content'];

    }

    /**
     * 添加新块
     * addBlock
     * @param  mixed        $content   [内容]
     * @param  string|null  $userid    [用户ID]
     * @param  string|null  $itemid    [对象ID]
     * @param  string|null  $itemtype  [对象类型]
     * @param  string|null  $saasid    [saasid ? ]
     *                                 1. 检测(锁定标记) # 系统标记 通过SETTING实现 ( SETTING_IBCHAIN_LOCK, bool ) 其中SETTING自动处理redis
     *                                 2. 等待( 循环等待 <检测间隙,检测次数> )
     *                                 2.1 失败超出次数 系统繁忙-退出
     *                                 2.2 等待成功 开始后续流程 ( 获取同时 进行锁定标记 )
     *                                 3. 获取最后块
     *                                 3.1 区块链加密 生成临时新块
     *                                 3.2 插入新块到数据库
     *                                 3.3 插入完成(无论成功失败) 解除锁定标记
     *                                 4. 更新最后块
     *                                 5. 比对当前块和临时块的Hash是否相同
     *                                 5.1 相同 插入成功
     *                                 5.2 不同 插入失败
     * @return ASResult|Block
     */
    public function addBlock($content, string $userid = null, string $itemid = null, string $itemtype = null, string $saasid = null ){

        $timeout = false;

        if( $this->isAdding() ){

            for ($i=0; $i < 10; $i++) {

                if( $this->isAdding() ){
                    $timeout = true;
                }else{
                    $timeout = false;
                    break;
                }
                usleep(10000);
            }
        }

        if( $timeout ){
            return $this->error(108,i18n('SYS_TIMEOUT'));
        }

        if( !$this->current ){
            $this->initCreation();
        }

        $blockInfo = $this->current['content'];

        $currentBlock = $this->getBlockFromData($blockInfo);

        $newBlock = $currentBlock->add($content);

        $this->add([
            'id'=>$newBlock->getId(),
            'hash'=>$newBlock->getHash(),
            'content'=>$newBlock->encode(),
            'userid'=>$userid,
            'itemid'=>$itemid,
            'itemtype'=>$itemtype,
            'saasid'=>$saasid
        ]);

        $this->endAdding();

        return $newBlock;

    }

    /**
     * 查询是否添加锁定中
     * isAdding
     * @return bool
     */
    public function isAdding(): bool
    {

        if(_ASSetting()->switchStatus('locking','IBChain')){
            return true;
        }else{
            $this->markAdding();
            return false;
        }
    }

    /**
     * 标记正在添加
     * markAdding
     * @return ASResult|bool
     */
    public function markAdding(){
        return _ASSetting()->switchOn('locking','IBChain');
    }

    /**
     * 结束添加状态
     * endAdding
     * @return ASResult|bool
     */
    public function endAdding(){
        return _ASSetting()->switchOff('locking','IBChain');
    }

    /**
     * 向前验证
     * verify
     * @return  bool
     */
    public function verify(): bool
    {

        if(empty($this->context) && $this->current['content']['id']===1 ){
            return true;
        }

        if( isset($this->context) && isset($this->current) ){

            return $this->current['hash'] === $this->getBlockFromData($this->context[0]['content'])->generateHash();
        }

        return false;
    }

    /**
     * 从数据建立块
     * getBlockFromData
     * @param  array  $blockInfo
     * @return Block
     */
    public function getBlockFromData( array $blockInfo ): Block
    {

        return new Block( $blockInfo['data'], $blockInfo['index'], $blockInfo['timestamp'], $blockInfo['hash'] );

    }

    /**
     * 创建创世区块
     * init Creation Block
     * @return ASResult
     */
    public function initCreation(): ASResult
    {

        $time = time();
        $info = [ 'engine'=>'appsite','createtime'=>$time ];
        $hash = hash('sha256',json_encode($info));

        $creationBlock = new BLOCK( $info, 1, $time, $hash );

        return $this->add(['id'=>1,'userid'=>'SYSTEM','content'=>$creationBlock->encode(),'hash'=>$hash]);
    }

    /** Static Contents **/

    /**
     * 表
     * @var string
     */
    public static $table     = "innerblock_chain";

    /**
     * 主字段
     * @var string
     */
    public static $primaryid = "blockid";

    /**
     * 添加支持字段
     * @var array
     */
    public static $addFields = [
        'id','uid','saasid','userid','itemid','itemtype',
        'content','hash','status'
    ];

    /**
     * 更新支持字段
     * @var array
     */
    public static $updateFields = [
        'status'
    ];

    /**
     * 详情支持字段
     * @var array
     */
    public static $detailFields = [
        'id','uid','saasid','userid','itemid','itemtype',
        'content','hash','status','createtime','lasttime',
    ];

    /**
     * 开放详情支持字段
     * @var array
     */
    public static $publicDetailFields = [
        'id','uid','saasid','userid','itemid','itemtype',
        'content','hash','status','createtime','lasttime',
    ];

    /**
     * 概览支持字段
     * @var array
     */
    public static $overviewFields = [
        'id','uid','saasid','userid','itemid','itemtype',
        'content','hash','status','createtime','lasttime',
    ];

    /**
     * 列表支持字段
     * @var array
     */
    public static $listFields = [
        'id','uid','saasid','userid','itemid','itemtype',
        'content','hash','status','createtime','lasttime',
    ];

    /**
     * 开放接口列表支持字段
     * @var array
     */
    public static $publicListFields = [
        'id','uid','saasid','userid','itemid','itemtype',
        'content','hash','status','createtime','lasttime',
    ];

    /**
     * 过滤字段
     * @var array
     */
    public static $countFilters = [
        'id','uid','saasid','userid','itemid','itemtype','hash','status','createtime','lasttime',
    ];

    /**
     * 转换格式
     * @var array
     */
    public static $depthStruct = [
        'id'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
        'content'=>'ASJson',
    ];


}


