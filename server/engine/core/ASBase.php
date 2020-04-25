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

    /**
     * 表名称
     * Table name
     * @var string
     */
    public static $table;

    /**
     * 目标字段
     * Target fields
     * @var array
     */
    protected $fields = '*';

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
     * 搜索过滤字段
     * @var array
     */
	protected static $searchFilters = ['title','description'];

    /**
     * 开启日志
     * @var bool
     */
	protected static $record_enabled = false;

    /**
     * 数据库链接
     * SQL Connect
     * @var \APS\ASDB
     */
    protected $DB;

    /**
     * ASBase constructor.
     * @param  bool  $enableRecord  是否开启日志记录
     */
	function __construct( bool $enableRecord = true )
    {
        parent::__construct();

        static::$record_enabled = $enableRecord;
    }


    protected function setTable( string $tableName ){ static::$table = $tableName; }
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
     * @param  array  $data
     */
	protected function DBAdd( array $data )
    {
        $this->params   = $data;
        $this->result = $this->getDB()->add( $data, static::$table);
    }

	/**
     * 批量添加数据
     * DBAdds
     * @param    array           $dataList
     */
	protected function DBAdds( array $dataList )
    {
        $this->params   = $dataList;
        $this->result = $this->getDB()->add( $dataList,static::$table);
    }

    /**
     * 获取数据
     * DBGet
     * @param  null  $fields
     * @param  null  $filters
     * @param  int   $page
     * @param  int   $size
     * @param  null  $sort
     */
	protected function DBGet( $fields = null, $filters = null, $page = 1,$size = 25,$sort = null )
    {
        $this->params   = [$filters,static::$table,$page,$size,$sort];
        $this->result = $this->getDB()->get($fields ?? '*', static::$table,$filters,$page,$size,$sort );
    }

	/**
     * 更新数据
     * Update to database
     * @param    array           $keyValueData           [数据k-v]
     * @param    string|array    $conditions     [description]
     */
	protected function DBUpdate( array $keyValueData , $conditions )
    {
        $this->params   = $conditions;
        $this->result   = $this->getDB()->update($keyValueData,static::$table,$conditions);
    }

	/**
     * 批量更新数据
     * DBUpdates
     * @param    array  $keyValueDataList       数据列表 Array of K-V
     * @param    string $keyField   筛选字段
     * @param    string|array $conditions 筛选条件
     */
	protected function DBUpdates( array $keyValueDataList , string $keyField, $conditions )
    {
        $this->params   = $conditions;
        $this->result   = $this->getDB()->updates($keyValueDataList,$keyField,static::$table,$conditions);
    }

	/**
     * 从数据库中删除行
     * DBRemove
     * @param    array|string  $conditions
     */
	protected function DBRemove( $conditions )
    {
        $this->params   = $conditions;
        $this->result   = $this->getDB()->remove(static::$table,$conditions);
    }

	/**
     * 统计数据库中对应行数
     * Count valid rows with conditions
     * @param    array|string          $conditions
     */
	protected function DBCount( $conditions )
    {
        $this->params   = $conditions;
        $this->result   = $this->getDB()->count(static::$table,$conditions);
    }


	/**
     * 在数据库中检测字段是否是对应数据
     * Check data is valid at field
     * @param    string          $field          字段名
     * @param    mixed           $value          数据值
     * @param    array|string    $conditions     查询条件
     * @param    string          $sort           排序 默认创建时间倒序
     */
	protected function DBCheck( string $field, $value, $conditions = null, string $sort = "createtime DESC" )
    {
        $this->params   = ['value'=>$value,'field'=>$field,'conditions'=>$conditions];
        $this->result   = $this->getDB()->check($value,$field,static::$table,$conditions,$sort);
    }

    /**
     * 基于自然语言关键词查询计数
     * DBNatureCount
     * @param    string          $keyword        关键词
     * @param    array|string    $conditions     查询条件
     */
    protected function DBNatureCount( string $keyword, $conditions = null )
    {
        $this->params   = ['table'=>static::$table,'target'=>static::$searchFilters ,'value'=>$keyword,'conditions'=>$conditions];
        $this->result   = $this->getDB()->natureCount(static::$searchFilters,$keyword,static::$table,$conditions);
    }

    /**
     * 基于自然语言关键词查询
     * Get data with nature language keyword search
     * @param  string        $keyword     关键词
     * @param  array|string  $conditions  查询条件
     * @param  null          $fields
     */
	protected function DBNatureGet( string $keyword, $conditions = null, $fields = null )
    {
        $fields = $fields?? static::$listFields ?? '*';
        $params = ['table'=>static::$table,'fields'=>$fields,'target'=>static::$searchFilters,'value'=>$keyword,'conditions'=>$conditions];
        $this->params   = $params;
        $this->result   = $this->getDB()->natureSearch($params,$keyword,static::$table,$conditions,$fields);
    }

    /**
     * 联合查询计数
     * DBJoinCount
     * @param  JoinPrimaryParams  $primaryParams
     * @param  JoinParams[]  $joinParams
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
     * 联合查询
     * DBJoinGet
     * @param  JoinPrimaryParams   $primaryParams
     * @param  JoinParams[]        $joinParams
     * @param  int          $page
     * @param  int          $size
     * @param  string|null  $sort
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
     * 记录日志到数据
     * Make a record to DB
     * @param  string  $event  事件名称 Event name
     * @param  string  $sign   程序签名 Sign by methods
     * @param  mixed|null $content
     */
    protected function record( string $event = null , string $sign = null, $content = null )
    {

        static::$record_enabled && _ASRecord()->add([
            'itemid'   => $this->id,
            'type'     => static::$table,
            'content'  => $content ?? $this->params,
            'status'   => $this->result->getStatus(),
            'event'    => $event,
            'sign'     => $sign  ?? $this->result->getSign()
        ]);
    }

}
