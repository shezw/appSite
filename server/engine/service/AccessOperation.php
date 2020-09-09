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

    public static $table         = 'access_operation';
    public static $primaryid     = 'uid';
    public static $addFields     = ['uid','title','description','scope','parentid','mergeparents'];
    public static $countFilters  = ['title','parentid','scope','status'];
    public static $searchFilters = ['title','description'];

    public static $depthStruct = [
        'mergeparents'=>'ASJson',
    ];

    public function newOperation( string $title, string $description, string $scope = 'common', string $parentid = null){

        if ( $this->has([
            'title'=>$title,
            'scope'=>$scope,
        ]) ){
            return $this->error(610,i18n('ACC_OPR_EXT'),'AccessOperation->newOperation');
        }

        if( isset($parentid) ){
            $parent = $this->detail($parentid);
            $mergeparents = $parent->isSucceed() ? $parent->getContent()['mergeparents'] : null;
            $mergeparents = is_array($mergeparents) ? $mergeparents : [];
            $mergeparents[] = $parentid;
        }

        return $this->add([
            'title'=>$title,
            'description'=>$description,
            'scope'=>$scope,
            'parentid'=>$parentid,
            'mergeparents'=>$mergeparents ?? null
        ]);
    }

    # 检查权限

    /**
     * 检查是否具有对应权限（包含父级对象检测）
     * Check if you have corresponding permissions (including parent group detection) by uid
     * @param  string  $groupid
     * @param  string  $uid
     * @return bool
     */
    public function can( string $groupid, string $uid ):bool{

        $operations = $this->detail($uid)['mergeparents'];
        $operations[] = $uid;

        return Relation::common()->has([
            'itemid'=>$groupid,
            'itemtype'=>UserGroup::$table,
            'targetid'=>$operations,
            'targettype'=>static::$table
        ]);
    }

    /**
     * 通过Operation,scope检查是否具有对应权限
     * Check by Operation & scope
     * @param  string  $groupid
     * @param  string  $operation
     * @param  string  $scope
     * @return bool
     */
    public function canBy( string $groupid, string $operation, string $scope = 'common' ):bool{

        $queryuid = $this->find( $operation,$scope );

        if( !$queryuid->isSucceed() ){ return false; }
        return $this->can( $groupid, $queryuid->getContent() );

    }


    /**
     * 查询权限对应索引id
     * find
     * @param  string  $operation
     * @param  string  $scope
     * @return \APS\ASResult
     */
    public function find( string $operation, string $scope = 'common' ){

        $list = $this->list(['title'=>$operation,'scope'=>$scope]);

        return $list->isSucceed() ? $this->take($list->getContent()[0]['uid'])->success() : $list;
    }

    /**
     * 取消权限
     * Cancel operation access of group
     * @param  string  $groupid
     * @param  string  $uid
     * @return \APS\ASResult
     */
    public function ban( string $groupid, string $uid ):ASResult{

        $combineId = Relation::common()->getBindId($groupid,UserGroup::$table,$uid,static::$table);

        return $combineId->isSucceed() ? Relation::common()->unBind($combineId->getContent()) : $combineId ;
    }


    /**
     * 授予权限
     * grant operation access to group
     * @param  string  $groupid
     * @param  string  $uid
     * @return \APS\ASResult
     */
    public function grant( string $groupid, string $uid ){

        return Relation::common()->bind($groupid,UserGroup::$table,$uid,static::$table);
    }

}