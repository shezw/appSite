<?php

namespace APS;

/**
 * 关系绑定
 * Relation combine
 *
 * 关系绑定用于一对多、多对多绑定。在数据库中表示为 item( itemid,itemtype )-> target( targetid,targettype ) type 为绑定增加一个维度
 *
 * @package APS\service
 */
class Relation extends ASModel{

    /**
     * 绑定
     * bind
     * @param  string       $itemid
     * @param  string       $itemtype
     * @param  string       $relationid
     * @param  string       $relationtype
     * @param  string|null  $type
     * @param  null         $rate
     * @return \APS\ASResult
     */
    public function bind( string $itemid, string $itemtype, string $relationid, string $relationtype, string $type = null, $rate = null ):ASResult{

        return static::common()->add(['itemid'=>$itemid,'itemtype'=>$itemtype,'relationid'=>$relationid,'relationtype'=>$relationtype,'type'=>$type,'rate'=>$rate]);
    }

    /**
     * 获取绑定id
     * getBindId
     * @param  string       $itemid
     * @param  string       $itemtype
     * @param  string       $relationid
     * @param  string       $relationtype
     * @param  string|null  $type
     * @return \APS\ASResult
     */
    public function getBindId( string $itemid, string $itemtype, string $relationid, string $relationtype, string $type = null ){

        $find = $this->find( $itemid,$itemtype,$relationid,$relationtype,$type );

        return $find->isSucceed() ? $this->take($find->getContent()[0][static::$primaryid])->success() : $find ;
    }

    /**
     * 查询对应绑定信息
     * find
     * @param  string       $itemid
     * @param  string       $itemtype
     * @param  string       $relationid
     * @param  string       $relationtype
     * @param  string|null  $type
     * @return \APS\ASResult
     */
    public function find( string $itemid, string $itemtype, string $relationid, string $relationtype, string $type = null ):ASResult{

        return $this->list( ['itemid'=>$itemid,'itemtype'=>$itemtype,'relationid'=>$relationid,'relationtype'=>$relationtype,'type'=>$type],1,1,'createtime DESC' );
    }


    // 批量绑定
    public function binds( string $itemid, string $itemtype, array $relationids, string $relationtype, string $type = null, $rate = null ){

        $list = [];

        for ($i=0; $i < count($relationids); $i++) {
            $list[] = ['itemid'=>$itemid,'itemtype'=>$itemtype,'relationid'=>$relationids[$i],'relationtype'=>$relationtype,'type'=>$type,'rate'=>$rate];
        }

        return static::common()->adds($list);
    }

    // 解除
    public function unBind( string $combineid ){
        return static::common()->remove($combineid);
    }

    /**
     * 批量解除
     * unBinds
     * @param  array|null  $filters
     *              string $itemid
     *              string $itemtype
     *              array $relationids
     *              string $relationtype
     * @return \APS\ASResult
     */
    public function unBinds( array $filters ){

        if( isset($filters['relationids']) ){ $filters['relationid'] = Filter::arrayToString($filters['relationids']); }
        $filters = Filter::purify($filters,static::$countFilters);

        return _ASDB()->remove(static::$table,$filters);
    }

    /**
     * 查询已绑定信息
     * boundList
     * @param  string  $relationClass  与目标类名一致     relation type as same as target class name
     * @param  string  $keyField      绑定的主索引字段   primary field bound on target table
     * @param  array   $filters       static::$countFilters
     * @param  int     $page
     * @param  int     $size
     * @param  string  $sort
     * @return \APS\ASResult
     */
    public function boundList(string $relationClass, string $keyField, array $filters = [], int $page = 1, int $size = 15, string $sort = null ):ASResult{

        $filters = Filter::purify($filters,static::$countFilters);
        $filters['relationType'] = $relationClass;

        $params = JoinPrimaryParams::common('APS\Relation',static::$primaryid)->get('*')->withResultFilter($filters);

        $joinParams = [];
        $joinParams[] = JoinParams::common($relationClass, $keyField)->get('*');

        $this->DBJoinGet($params,$joinParams,$page,$size,$sort??static::$table.".createtime DESC");
        return $this->feedback();
    }


    /**
     * 已绑定 计数
     * boundCount
     * @param  array  $filters  static::$countFilters
     * @return \APS\ASResult
     */
    public function boundCount( array $filters = [] ):ASResult{

        return $this->getDB()->count(static::$table,$filters);
    }



    public static $table     = "relation_combine";
    public static $primaryid = "combineid";
    public static $addFields = [
        'combineid',
        'itemid',
        'itemtype',
        'relationid',
        'relationtype',
        'type',
        'rate',
        'sort',
    ];
    public static $updateFields = [
        'type',
        'rate',
        'sort',
    ];
    public static $detailFields = "*";
    public static $overviewFields = [
        'combineid',
        'itemid',
        'itemtype',
        'relationid',
        'relationtype',
        'type',
        'rate',
        'sort',
    ];
    public static $listFields = [
        'combineid',
        'itemid',
        'itemtype',
        'relationid',
        'relationtype',
        'type',
        'rate',
        'sort',
    ];
    public static $countFilters = [
        'combineid',
        'itemid',
        'itemtype',
        'relationid',
        'relationtype',
        'type',
        'rate',
        'sort',
    ];
    public static $depthStruct = [
        'createtime'=>'int',
        'lasttime'=>'int',
        'rate'=>'int',
        'sort'=>'int',
    ];


}