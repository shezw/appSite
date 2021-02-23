<?php

namespace APS;

/**
 * 内区块链
 * Block
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
 * Server internal blockchain data support
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
 * @package APS\tool
 */
class Block{

    private $data;
    private $index;
    private $timestamp;
    private $hash;

    /**
     * Block constructor.
     * @param  mixed        $data       数据
     * @param  int          $index      索引id
     * @param  int|null     $timestamp  时间戳
     * @param  string|null  $hash       前块哈希
     */
    function __construct( $data, int $index = 1, int $timestamp = null, string $hash = null ){

        $this->data      = $data;
        $this->index     = $index;
        $this->timestamp = $timestamp;
        $this->hash      = $hash;
    }

    /**
     * 新块
     * new block
     * @param $data
     * @return Block
     */
    public function add( $data ): Block
    {

        return new Block( $data, $this->index+1, time(), $this->generateHash() );
    }

    /**
     * 生成哈希
     * generateHash
     * @return string
     */
    public function generateHash(  ): string
    {

        return hash('sha256',$this->encode());
    }

    /**
     * 转义
     * encode
     * @return false|string
     */
    public function encode( ){

        return json_encode([
            'data'=>$this->data,
            'index'=>$this->index,
            'timestamp'=>$this->timestamp,
            'hash'=>$this->hash,
        ],256);
    }

    public function getId(): int
    {
        return $this->index;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

}
