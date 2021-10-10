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

    const table     = "user_group";
    const comment   = '用户-分组';
    const primaryid = "uid";
    const addFields = [
        'uid','type','parentid',
        'level',
        'groupname','description','menuaccess',
        'status','sort','featured','createtime','lasttime',
    ];
    const updateFields = [
        'uid','type','parentid',
        'level',
        'groupname','description','menuaccess',
        'status','sort','featured','createtime','lasttime',
    ];
    const detailFields = [
        'uid','type','parentid',
        'level',
        'groupname','description','menuaccess',
        'status','sort','featured','createtime','lasttime',
    ];
    const overviewFields = [
        'uid','type','parentid',
        'level','groupname',
        'sort','description','status',
    ];
    const listFields = [
        'uid','type','parentid',
        'level',
        'groupname','description','menuaccess',
        'status','sort','featured','createtime','lasttime',
    ];
    const filterFields = [
        'uid','type','parentid',
        'level',
        'groupname',
        'status','sort','featured','createtime','lasttime',
    ];
    const depthStruct = [
        'level'=>DBField_Int,
        'menuaccess'=>DBField_Json,
        'sort'=>DBField_Int,
        'featured'=>DBField_Boolean,
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>         ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'组唯一ID', 'idx'=>DBIndex_Unique ],
        'type'=>        ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'组类型 character 角色 department 部门',        'dft'=>'character', ],
        'parentid'=>    ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'父级ID'],
        'level'=>       ['type'=>DBField_Int,       'len'=>5,   'nullable'=>0,  'cmt'=>'权限级别',  'dft'=>0,       ],
        'groupname'=>   ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'组名'],
        'description'=> ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述 120字以内'],
        'menuaccess'=>  ['type'=>DBField_Json,  'len'=>-1,  'nullable'=>1,  'cmt'=>'菜单栏权限'],
        'status'=>      ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>Status_Enabled, ],

        'createtime'=>  ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',           'idx'=>DBIndex_Index, ],
        'lasttime'=>    ['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>    ['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,    'idx'=>DBIndex_Index, ],
        'sort'=>        ['type'=>DBField_Int,      'len'=>5,  'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,   'idx'=>DBIndex_Index, ],
    ];


    /**
     * 通过id获取组名
     * getNameById
     * @param  string  $uid
     * @return ASResult
     */
    public function getNameById( string $uid ): ASResult
    {
        return $this->get('groupname',$uid);
    }


    /**
     * 获取子组
     * getChild
     * @param  string  $uid
     * @param  int     $page
     * @param  int     $size
     * @param  string  $sort
     * @return ASResult
     */
    public function getChild( string $uid, int $page=1, int $size=100, string $sort = 'sort DESC, level DESC, createtime DESC' ): ASResult
    {
        if (!$this->hasChild($uid)) {
            return $this->error(400,i18n('SYS_NON'),'USERGROUP->getChild');
        }

        return $this->list(DBConditions::init(static::table)->where('parentid')->equal($uid),$page,$size,$sort);
    }

    /**
     * 是否拥有子组
     * hasChild
     * @param  string  $uid
     * @return bool
     */
    public function hasChild( string $uid ): bool
    {
        return $this->count(DBConditions::init(static::table)->where('parentid')->equal($uid))->getContent() > 0;
    }


}