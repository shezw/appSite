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

    const primaryid = 'uid';
    const comment   = '关系绑定';
    const table     = "relation_combine";
    const addFields = [
        'uid', 'itemid', 'itemtype', 'relationid', 'relationtype', 'type',
        'rate','status','createtime','lasttime'
    ];
    const updateFields = [
        'type',
        'status',
        'rate',
    ];
    const detailFields = [
        'uid', 'itemid', 'itemtype', 'relationid', 'relationtype', 'type',
        'rate','status','createtime','lasttime'
    ];
    const overviewFields = [
        'uid', 'itemid', 'itemtype', 'relationid', 'relationtype', 'type',
        'rate','status','createtime','lasttime'
    ];
    const listFields = [
        'uid', 'itemid', 'itemtype', 'relationid', 'relationtype', 'type',
        'rate','status','createtime','lasttime'
    ];
    const filterFields = [
        'uid', 'itemid', 'itemtype', 'relationid', 'relationtype', 'type',
        'rate','status','createtime','lasttime'
    ];
    const depthStruct = [
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'rate'=>DBField_Int,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,      'cmt'=>'主ID' ,    'idx'=>DBIndex_Unique ],
        'itemid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,      'cmt'=>'单位ID' ,  'idx'=>DBIndex_Index ],
        'itemtype'=>    ['type'=>DBField_String,    'len'=>24,  'nullable'=>0,      'cmt'=>'单位类型' , 'idx'=>DBIndex_Index ],
        'relationid'=>  ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,      'cmt'=>'关联对象ID' ,  'idx'=>DBIndex_Index ],
        'relationtype'=>['type'=>DBField_String,    'len'=>24,  'nullable'=>0,      'cmt'=>'关联对象类型' ,  'idx'=>DBIndex_Index ],
        'type'=>        ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,      'cmt'=>'类型' ,   'idx'=>DBIndex_Index ],
        'rate'=>        ['type'=>DBField_Int,       'len'=>3,   'nullable'=>0,      'cmt'=>'强度' ,   'dft'=>0,       ],
        // like type eg: superlike 5  like 1  normal 0 dislike -1  hate -5
        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,      'cmt'=>'状态',    'dft'=>'enabled',       ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',    'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];


    /**
     * 绑定
     * bind
     * @param  string       $itemid
     * @param  string       $itemtype
     * @param  string       $relationId
     * @param  string       $relationType
     * @param  string|null  $type
     * @param  null         $rate
     * @return ASResult
     */
    public function bind( string $itemid, string $itemtype, string $relationId, string $relationType, string $type = null, $rate = null ):ASResult{

        $values =
            DBValues::init(static::table)
            ->set('itemid')->string($itemid)
            ->set('itemtype')->string($itemtype)
            ->set('relationid')->string($relationId)
            ->set('relationtype')->string($relationType)
            ->set('type')->stringIf($type)
            ->set('rate')->numberIf($rate);

        return static::common()->add($values);
    }

    /**
     * 获取绑定id
     * getBindId
     * @param  string       $itemid
     * @param  string       $itemtype
     * @param  string       $relationId
     * @param  string       $relationType
     * @param  string|null  $type
     * @return ASResult
     */
    public function getBindId( string $itemid, string $itemtype, string $relationId, string $relationType, string $type = null ): ASResult
    {
        $find = $this->find( $itemid,$itemtype,$relationId,$relationType,$type );

        return $find->isSucceed() ? $this->take($find->getContent()[0][static::primaryid])->success() : $find ;
    }

    /**
     * 查询对应绑定信息
     * find
     * @param  string       $itemid
     * @param  string       $itemtype
     * @param  string       $relationId
     * @param  string       $relationType
     * @param  string|null  $type
     * @return ASResult
     */
    public function find( string $itemid, string $itemtype, string $relationId, string $relationType, string $type = null ):ASResult{

        $conditions =
        DBConditions::init(static::table)
            ->where('itemid')->equal($itemid)
            ->and('itemtype')->equal($itemtype)
            ->and('relationid')->equal($relationId)
            ->and('type')->equalIf($type);

        return $this->list( $conditions,1,1,'createtime DESC' );
    }


    // 解除
    public function unBind( string $uid ): ASResult
    {
        return static::common()->remove($uid);
    }

    /**
     * 查询已绑定信息
     * boundList
     * @param string $relationClass 与目标类名一致     relation type as same as target class name
     * @param string $keyField 绑定的主索引字段   primary field bound on target table
     * @param DBConditions|null $filters static::filterFields
     * @param int $page
     * @param int $size
     * @param string $sort
     * @return ASResult
     */
    public function boundList(string $relationClass, string $keyField, DBConditions $filters = null, int $page = 1, int $size = 15, string $sort = null ):ASResult
    {
//        $primaryJoin = DBJoinParam::convincePrimaryForDetail($relationClass, $keyField );
//
//        $infoParam   = DBJoinParam::convinceForDetail(UserInfo::class,UserAccount::table.".".UserAccount::primaryid )->asSub('info');
//        $groupParam  = DBJoinParam::convinceForDetail(UserGroup::class,UserAccount::table.".".UserAccount::primaryid )->asSub('group');
//        $pocketParam = DBJoinParam::convinceForDetail(UserPocket::class,UserAccount::table.".".UserAccount::primaryid )->asSub('pocket');
//
//        $joinParams  = DBJoinParams::init( $primaryJoin )->leftJoin($infoParam)->leftJoin($groupParam)->leftJoin($pocketParam);

//        $fullDetail  = $this->getByJoin( $joinParams );

//        return $this->joinList()
//
//        $filters = Filter::purify($filters,static::filterFields);
//        $filters['relationType'] = $relationClass;
//
//        $params = JoinPrimaryParams::common('APS\Relation',static::primaryid)->get('*')->withResultFilter($filters);
//
//        $joinParams = [];
//        $joinParams[] = JoinParams::init($relationClass, $keyField)->get('*');
//
//        $this->DBJoinGet($params,$joinParams,$page,$size,$sort??static::table.".createtime DESC");
//        return $this->feedback();
    }


    /**
     * 已绑定 计数
     * boundCount
     * @param DBConditions $filters static::filterFields
     * @return ASResult
     */
//    public function boundCount( DBConditions $filters ):ASResult{
//
//        return $this->getDB()->count(static::table,$filters);
//    }


}