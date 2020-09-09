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
    public static $primaryid = "uid";
    public static $addFields = [
        'uid',
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
        'uid',
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
        'uid',
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
        'uid',
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
        'uid',
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
     * @param  string  $uid
     * @return \APS\ASResult
     */
    public function getNameById( string $uid ){

        return $this->get('groupname',$uid);
    }


    /**
     * 获取子组
     * getChild
     * @param  string  $uid
     * @param  int     $page
     * @param  int     $size
     * @param  string  $sort
     * @return \APS\ASResult
     */
    public function getChild( string $uid, int $page=1, int $size=100, string $sort = 'sort DESC, level DESC, createtime DESC' ){

        if (!$this->hasChild($uid)) {
            return $this->error(400,i18n('SYS_NON'),'USERGROUP->getChild');
        }

        return $this->list(['parentid'=>$uid],$page,$size,$sort);
    }

    /**
     * 是否拥有子组
     * hasChild
     * @param  string  $uid
     * @return bool
     */
    public function hasChild( string $uid ){

        return $this->count(['parentid'=>$uid])->getContent() > 0;
    }


}