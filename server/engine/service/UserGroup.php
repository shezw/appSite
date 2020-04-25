<?php

namespace APS;

/**
 * 用户分组
 * UserGroup
 * @package APS\service\User
 */
class UserGroup extends ASModel
{
    /**
     * 用户id
     * @var string|null
     */
    private $userid;

    public static $table     = "user_group";
    public static $primaryid = "groupid";
    public static $addFields = [
        'groupid',
        'type',
        'parentid',
        'level',
        'groupname',
        'sort',
        'description',
        'menuaccess',
        'status',
    ];
    public static $updateFields = [
        'type',
        'parentid',
        'level',
        'groupname',
        'sort',
        'description',
        'menuaccess',
        'status',
    ];
    public static $detailFields = [
        'groupid',
        'type',
        'parentid',
        'level',
        'groupname',
        'sort',
        'description',
        'menuaccess',
        'status',
    ];
    public static $overviewFields = [
        'groupid',
        'type',
        'parentid',
        'level',
        'groupname',
        'sort',
        'description',
        'menuaccess',
        'status',
    ];
    public static $listFields = [
        'groupid',
        'type',
        'parentid',
        'level',
        'groupname',
        'sort',
        'description',
        'menuaccess',
        'status',
        'createtime',
        'lasttime',
    ];
    public static $countFilters = [
        'groupid',
        'type',
        'parentid',
        'level',
        'groupname',
        'sort',
        'description',
        'menuaccess',
        'status',
        'createtime',
        'lasttime',
    ];
    public static $depthStruct = [
        'level'=>'int',
        'menuaccess'=>'ASJson',
        'sort'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
    ];


    /**
     * 通过组名获取id
     * getNameById
     * @param  string  $groupid
     * @return \APS\ASResult
     */
    public function getNameById( string $groupid ){

        return $this->get('groupname',$groupid);
    }


    /**
     * 获取子组
     * getChild
     * @param  string  $groupid
     * @param  int     $page
     * @param  int     $size
     * @param  string  $sort
     * @return \APS\ASResult
     */
    public function getChild( string $groupid, int $page=1, int $size=100, string $sort = 'sort DESC, level DESC, createtime DESC' ){

        if (!$this->hasChild($groupid)) {
            return $this->error(400,i18n('SYS_NON'),'USERGROUP->getChild');
        }

        return $this->list(['parentid'=>$groupid],$page,$size,$sort);
    }

    /**
     * 是否拥有子组
     * hasChild
     * @param  string  $groupid
     * @return bool
     */
    public function hasChild( string $groupid ){

        return $this->count(['parentid'=>$groupid])->getContent() > 0;
    }


}