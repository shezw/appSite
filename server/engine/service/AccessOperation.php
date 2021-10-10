<?php

namespace APS;

/**
 * 系统操作权限
 * AccessOperation
 *
 * 系统权限可以对用户组颁发。
 * 权限的本身作为标识，通过APS\Relation用户组进行绑定。 检测权限时，通过查询是否具有对应绑定即可。 (通过Redis将组权限进行缓存加速检测)
 * 权限支持多级权限，通过parentid字段进行迭代查询。
 * ! 不支持用户组权限继承
 *
 * System permissions can be issued to groups.
 * The authority itself is used as an identifier to bind user groups through Relation. When detecting permissions, you can query whether there is a corresponding binding.
 * Permissions support multi-level permissions. Iterative query is performed through the parentid field.
 * ! User group access inheritance is not supported
 *
 * @package APS\service\Access
 */
class AccessOperation extends ASModel{

    const table         = 'access_operation';
    const comment       = '系统操作权限';
    const primaryid     = 'uid';
    const addFields     = ['uid','saasid','title','description','scope','parentid','mergeparents'];
    const detailFields  = ['uid','saasid','title','description','scope','parentid','mergeparents'];
    const listFields    = ['uid','saasid','title','description','scope','parentid','mergeparents'];
    const updateFields  = ['title','description','scope','parentid','mergeparents'];
    const filterFields  = ['title','saasid','parentid','scope','status'];
    const tableStruct = [

        'uid'           =>['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'索引ID',  'idx'=>DBIndex_Unique ],
        'saasid'        =>['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'parentid'      =>['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'父级ID',  'idx'=>DBIndex_Index ],
        'mergeparents'  =>['type'=>DBField_Json,      'len'=>128, 'nullable'=>1,  'cmt'=>'合并父级 120字符以内（用于快速迭代查询）JSON' ],
        'title'         =>['type'=>DBField_String,    'len'=>24,  'nullable'=>0,  'cmt'=>'权限名称'],
        'description'   =>['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述 120字以内' ],
        'scope'         =>['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'权限作用域'],
        'status'        =>['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'状态 // disabled弃用',  'dft'=>'enabled', ],

        'createtime'    =>['type'=>DBField_TimeStamp, 'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间', 'idx'=>DBIndex_Index, ],
        'lasttime'      =>['type'=>DBField_TimeStamp, 'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];

    const depthStruct = [
        'mergeparents'=>DBField_Json,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp
    ];

    public function newOperation( string $title, string $description, string $scope = 'common', string $parentid = null): ASResult
    {
        if ( $this->has(DBConditions::init()->where('title')->equal($title)->and('scope')->equal($scope)) ){
            return $this->error(610,i18n('ACC_OPR_EXT'),'AccessOperation->newOperation');
        }

        if( isset($parentid) ){
            $parent = $this->detail($parentid);
            $mergeparents = $parent->isSucceed() ? $parent->getContent()['mergeparents'] : null;
            $mergeparents = is_array($mergeparents) ? $mergeparents : [];
            $mergeparents[] = $parentid;
        }

        return $this->add( AccessOperation::initValuesFromArray([
            'title'=>$title,
            'description'=>$description,
            'scope'=>$scope,
            'parentid'=>$parentid,
            'mergeparents'=>$mergeparents ?? null,
            'saasid'=>saasId()
        ]));
    }

    # 检查权限

    /**
     * 检查是否具有对应权限（包含父级对象检测）
     * Check if you have corresponding permissions (including parent group detection) by uid
     * @param  string  $groupid
     * @param  string  $uid
     * @return bool
     */
    public function can( string $groupid, string $uid ):bool
    {
        $operations = $this->detail($uid)['mergeparents'];
        $operations[] = $uid;

        return Relation::common()->has(DBConditions::init()->where('itemid')->equal($groupid)
        ->and('itemtype')->equal(UserGroup::table)
        ->and('targetid')->equal($operations)
        ->and('targettype')->equal(static::table)
        ->and('saasid')->equalIf(saasId())
        );
    }

    /**
     * 通过Operation,scope检查是否具有对应权限
     * Check by Operation & scope
     * @param  string  $groupid
     * @param  string  $operation
     * @param  string  $scope
     * @return bool
     */
    public function canBy( string $groupid, string $operation, string $scope = 'common' ):bool
    {
        $getUid = $this->find( $operation,$scope );

        if( !$getUid->isSucceed() ){ return false; }
        return $this->can( $groupid, $getUid->getContent() );

    }


    /**
     * 查询权限对应索引id
     * find
     * @param  string  $operation
     * @param  string  $scope
     * @return ASResult
     */
    public function find( string $operation, string $scope = 'common' ): ASResult
    {
        $list = $this->list(DBConditions::init('scope')->equal($scope)->and('title')->equal($operation)->and('saasid')->equalIf(saasId()));

        return $list->isSucceed() ? $this->take($list->getContent()[0]['uid'])->success() : $list;
    }

    /**
     * 取消权限
     * Cancel operation access of group
     * @param  string  $groupid
     * @param  string  $uid
     * @return ASResult
     */
    public function ban( string $groupid, string $uid ):ASResult
    {
        $combineId = Relation::common()->getBindId($groupid,UserGroup::table,$uid,static::table);

        return $combineId->isSucceed() ? Relation::common()->unBind($combineId->getContent()) : $combineId ;
    }


    /**
     * 授予权限
     * grant operation access to group
     * @param  string  $groupid
     * @param  string  $uid
     * @return ASResult
     */
    public function grant( string $groupid, string $uid ): ASResult
    {
        return Relation::common()->bind($groupid,UserGroup::table,$uid,static::table);
    }

}