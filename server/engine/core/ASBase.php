<?php

namespace APS;

/**
 * 抽象基础类
 * ASBase
 *
 * 数据库交互、日志记录、结果返回
 *
 * @package APS\core
 */
abstract class ASBase extends ASObject{

    const table = "ASBase";

    /**
     * 主体id
     * Event id
     * @var string
     */
    protected $id;

    /**
     * 事件参数暂存
     * Parameters tmp
     * @var array
     */
    protected $params;

    /**
     * 开启日志
     * @var bool
     */
	const record_enabled = true;

    /**
     * 数据库链接
     * SQL Connect
     * @var ASDB
     */
    protected $DB;

    /**
     * ASBase constructor.
     */
	function __construct()
    {
        parent::__construct();
    }


	protected function setId( string $id ){ $this->id = $id; }

	protected function getDB():ASDB{
	    if( !isset($this->DB) ){
	        $this->DB = _ASDB();
        }
	    return $this->DB;
    }

    public function setDB( ASDB $db ){
	    $this->DB = $db;
    }

    /**
     * 添加数据到DB
     * DBAdd
     * @param DBValues $data
     */
	protected function DBAdd( DBValues $data )
    {
        $this->params   = $data;
        $this->result = $this->getDB()->add( $data, static::table);
    }

    /**
     * 获取数据
     * DBGet
     * @param DBFields|null $fields
     * @param DBConditions|null $conditions
     */
	protected function DBGet( DBFields $fields = null, DBConditions $conditions = null )
    {
        $this->params   = [static::table,$fields->toArray(),$conditions->toArray()];
        $this->result = $this->getDB()->get($fields, static::table,$conditions );
    }

    /**
     * 更新数据
     * Update to database
     * @param DBValues $keyValueData [数据k-v]
     * @param DBConditions $conditions [description]
     */
	protected function DBUpdate( DBValues $keyValueData , DBConditions $conditions )
    {
        $this->params   = $conditions;
        $this->result   = $this->getDB()->update($keyValueData,static::table,$conditions);
    }

    /**
     * 从数据库中删除行
     * DBRemove
     * @param DBConditions $conditions
     */
	protected function DBRemove( DBConditions $conditions )
    {
        $this->params   = $conditions;
        $this->result   = $this->getDB()->remove(static::table,$conditions);
    }

    /**
     * 统计数据库中对应行数
     * Count valid rows with conditions
     * @param DBConditions $conditions
     */
	protected function DBCount( DBConditions $conditions )
    {
        $this->params   = $conditions;
        $this->result   = $this->getDB()->count(static::table,$conditions);
    }


    /**
     * 在数据库中检测字段是否是对应数据
     * Check data is valid at field
     * @param string $field 字段名
     * @param mixed $value 数据值
     * @param DBConditions|null $conditions 查询条件
     */
	protected function DBCheck( string $field, $value, DBConditions $conditions )
    {
        $this->params   = ['value'=>$value,'field'=>$field,'conditions'=>$conditions->toArray()];
        $this->result   = $this->getDB()->check($value,$field,static::table,$conditions);
    }

    /**
     * 联合查询计数
     * DBJoinCount
     * @param  JoinPrimaryParams  $primaryParams
     * @param  JoinParams[]  $joinParams
     * @deprecated
     */
	protected function DBJoinCount( JoinPrimaryParams $primaryParams, array $joinParams )
    {
        $params = [
            'primaryParams'=>$primaryParams,'joinParams'=>$joinParams
        ];
        $this->params = $params;
        $this->result = $this->getDB()->joinCount($primaryParams,$joinParams);
    }

    /**
     * 联合查询计数 (新)
     * Count lines by JOIN
     * @param DBJoinParams $joinParams
     */
    protected function countByJoin( DBJoinParams $joinParams )
    {
        $this->params = ['count',$joinParams->toArray()];
        $this->result = $this->getDB()->countByJoin( $joinParams );
    }

    /**
     * 联合查询
     * DBJoinGet
     * @param  JoinPrimaryParams   $primaryParams
     * @param  JoinParams[]        $joinParams
     * @param  int          $page
     * @param  int          $size
     * @param  string|null  $sort
     * @deprecated
     */
	protected function DBJoinGet( JoinPrimaryParams $primaryParams, array $joinParams, int $page = 1, int $size = 20, string $sort = null )
    {

        $params = [
            'primaryParams'=>$primaryParams,'joinParams'=>$joinParams,'page'=>$page,'size'=>$size,'sort'=>$sort
        ];
        $this->params = $params;
        $this->result = $this->getDB()->joinGet($primaryParams,$joinParams,$page,$size,$sort);
    }

    /**
     * 联合查询 (新)
     * New
     * @param DBJoinParams $joinParams
     */
    protected function getByJoin( DBJoinParams $joinParams ){

        $this->params = ['get',$joinParams->toArray()];
        $this->params = $joinParams->toArray();
        $this->result = $this->getDB()->getByJoin( $joinParams );
    }

    /**
     * 记录日志到数据
     * Make a record to DB
     * @param  string  $event  事件名称 Event name
     * @param  string  $sign   程序签名 Sign by methods
     * @param  mixed|null $content
     */
    protected function record( string $event = null , string $sign = null, $content = null )
    {
        static::record_enabled && _ASRecord()->save([
            'itemid'   => $this->id,
            'type'     => static::table,
            'content'  => $content ?? $this->params,
            'status'   => $this->result->getStatus(),
            'event'    => $event,
            'sign'     => $sign  ?? $this->result->getSign()
        ]);
    }

}
