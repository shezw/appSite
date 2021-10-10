<?php

namespace APS;

/**
 * 归属权限
 * AccessPermission
 * @package APS\service\Access
 */
class AccessPermission extends ASModel{

    const primaryid = 'uid';
    const comment   = '(物品、单位)归属权限';
    const table = 'access_permission';
    const addFields = ['uid','saasid','userid','itemid','itemtype','info','expire','status','createtime','lasttime'];
    const updateFields = ['userid','itemid','itemtype','info','expire','status','createtime','lasttime'];
    const detailFields = ['uid','saasid','userid','itemid','itemtype','info','expire','status','createtime','lasttime'];
    const listFields = ['uid','saasid','userid','itemid','itemtype','info','expire','status','createtime','lasttime'];
    const filterFields = ['uid','saasid','userid','itemid','itemtype','expire','status','createtime','lasttime'];

    const tableStruct = [

        'uid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'索引ID',  'idx'=>DBIndex_Unique ],
        'saasid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas','idx'=>DBIndex_Index,],
        'userid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'用户ID',  'idx'=>DBIndex_Index ],
        'itemid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'单品ID',  'idx'=>DBIndex_Index ],
        'itemtype'=> ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'单品类型'],
        'info'=>     ['type'=>DBField_String,    'len'=>512, 'nullable'=>1,  'cmt'=>'数据 // 可以携带兑换码等信息 '],
        'expire'=>   ['type'=>DBField_TimeStamp, 'len'=>11,  'nullable'=>0,  'cmt'=>'过期时间 默认永久有效',   'dft'=>'9999999999', ],
        'status'=>   ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'状态 // used已使用', 'dft'=>'enabled', ],

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',  'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];

    // 新增一个单位权限
    public function addItemPermission( string $userid,string $itemid, string $itemtype, $expire = 999999999, $info = null, string $status = 'enabled' ): ASResult
    {

        $DB = $this->add( AccessPermission::initValuesFromArray([
            'userid'=>$userid,
            'itemid'=>$itemid,
            'itemtype'=>$itemtype,
            'expire'=>$expire,
            'info'=>$info,
            'status'=>$status,
            'saasid'=>saasId()
        ]));

        if( !$DB->isSucceed() ){ return $DB; }

        return $this->take($itemid)->success(i18n('SYS_ADD_SUC'),'AccessPermission::addItemPermission');
    }


    // 统计单位权限个数
    public function countItemPermission( string $userid, string $itemid, string $itemtype, int $expire = 0, string $status= null ):ASResult{

        return $this->count(
            DBConditions::init()
                ->where('userid')->equal($userid)
                ->and('itemid')->equal($itemid)
                ->and('itemtype')->equal($itemtype)
                ->and('expire')->biggerAnd($expire)
                ->and('status')->equalIf($status)
                ->and('saasid')->equalIf(saasId())
        );
    }

    /**
     * 列出单位权限清单
     * listItemPermission
     * @param  string  $userid
     * @param  string  $itemtype
     * @param  string  $status
     * @param  int     $page
     * @param  int     $size
     * @param  string  $sort
     * @return ASResult
     */
    public function listItemPermission( string $userid, string $itemtype, string $status='enabled', int $page=1, int $size=25, string $sort = ' expire DESC, createtime DESC ' ): ASResult
    {
        if ( $this->countItemPermission($userid,$itemtype,$status)->getContent()===0) {

            return $this->error(400,i18n('SYS_GET_NON'),'AccessPermission->listItemPermission');
        }

        $conditions = DBConditions::init()
            ->where('userid')->equal($userid)
            ->and('itemtype')->equal($itemtype)
            ->and('status')->equal($status)
            ->and('saasid')->equalIf(saasId())
            ->limitWith( $size * ($page-1) , $size )
            ->orderWith($sort)
        ;

        $DB = $this->getDB()->get(DBFields::allOf(),static::table,$conditions );

        if ( !$DB->isSucceed() ){ return $DB; }

        return $this->take($DB->getContent())->success(i18n('SYS_GET_SUC'),'AccessPermission->listItemPermission');

    }


    /**
     * 查看权限携带信息
     * getPermissionInfo
     * @param  string  $permissionId
     * @return ASResult
     */
    public function getPermissionInfo( string $permissionId ): ASResult
    {
        $detail = $this->detail($permissionId);

        return $detail->isSucceed() ? $this->take($detail['info'])->success() : $detail;
    }


    /**
     * 检测物品权限
     * checkItemPermission
     * @param  string  $userid
     * @param  string  $itemid
     * @param  string  $itemtype
     * @return ASResult
     */
    public function checkItemPermission( string $userid, string $itemid, string $itemtype ): ASResult
    {
        $filter = DBConditions::init()
            ->where('expire')->bigger(time())
            ->and('userid')->equal($userid)
            ->and('itemid')->equal($itemid)
            ->and('itemtype')->equal($itemtype)
            ->and('status')->equal('enabled')
            ->and('saasid')->equalIf(saasId());

        $list = $this->list( $filter,1,25,'expire DESC, createtime DESC');

        return $list->isSucceed() ? $this->take($list->getContent()[0])->success() : $this->error(400,i18n('SYS_GET_NON')) ;
    }

    /**
     * 设置单品权限
     * setItemPermission
     * @param  string  $userid
     * @param  string  $itemid
     * @param  string  $itemtype
     * @param  int     $expire
     * @param  null    $info
     * @param  string  $status
     * @return ASResult
     */
    public function setItemPermission( string $userid, string $itemid, string $itemtype, int $expire, $info = null, string $status = 'enabled' ): ASResult
    {
        if( $this->countItemPermission($userid,$itemid,$itemtype,null,'enabled') > 0){

            return $this->getDB()->update( DBValues::init('expire')->numberIf($expire)
            ->set('info')->stringIf($info)
            ->set('status')->string($status),
                static::table,
                DBConditions::init(static::table)
                    ->and('userid')->equal($userid)
                    ->and('itemid')->equal($itemid)
                    ->and('itemtype')->equal($itemtype)
                    ->and('saasid')->equalIf(saasId())
            );
        }else{
            return $this->addItemPermission($userid,$itemid,$itemtype,$expire,$info,$status);
        }
    }

    /**
     * 取消单位权限
     * setItemExpired
     * @param  string  $userid
     * @param  string  $itemid
     * @param  string  $itemtype
     * @return ASResult
     */
    public function setItemExpired( string $userid, string $itemid, string $itemtype ): ASResult
    {
        return $this->setItemPermission($userid,$itemid,$itemtype,0);
    }


}