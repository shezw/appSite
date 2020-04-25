<?php

namespace APS;

/**
 * 归属权限
 * AccessPermission
 * @package APS\service\Access
 */
class AccessPermission extends ASModel{

    public static $table = 'access_permission';

    public static $primaryid = 'permissionid';

    // 新增一个单位权限
    public function addItemPermission( string $userid,string $itemid, string $itemtype, $expire = 999999999, $info = null, string $status = 'enabled' ){

        $DB = $this->add([
            'data'  => [
                'userid'=>$userid,
                'itemid'=>$itemid,
                'itemtype'=>$itemtype,
                'expire'=>$expire,
                'info'=>$info,
                'status'=>$status
            ],
            'table' => static::$table,
        ]);

        if( !$DB->isSucceed() ){ return $DB; }

        return $this->take($itemid)->success(i18n('SYS_ADD_SUC'),'AccessPermission::addItemPermission');

    }


    // 统计单位权限个数
    public function countItemPermission( string $userid, string $itemid, string $itemtype, int $expire = null, string $status= null ):ASResult{

        return $this->count([
            'userid'   => $userid,
            'itemid'   => $itemid,
            'itemtype' => $itemtype,
            'expire'   => $expire,
            'status'   => $status,
        ]);
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
     * @return \APS\ASResult
     */
    public function listItemPermission( string $userid, string $itemtype, string $status='enabled', int $page=1, int $size=25, string $sort = ' expire DESC, createtime DESC ' ){

        if ( $this->countItemPermission($userid,$itemtype,$status)->getContent()===0) {

            return $this->error(400,i18n('SYS_GET_NON'),'AccessPermission->listItemPermission');

        }

        $conditions = [
            'userid'   => $userid,
            'itemtype' => $itemtype,
            'status'   => $status,
        ];

        $DB = $this->getDB()->get('*',static::$table,$conditions,$page,$size,$sort);

        if ( !$DB->isSucceed() ){ return $DB; }

        return $this->take($DB->getContent())->success(i18n('SYS_GET_SUC'),'AccessPermission->listItemPermission');

    }


    /**
     * 查看权限携带信息
     * getPermissionInfo
     * @param  string  $permissionid
     * @return \APS\ASResult
     */
    public function getPermissionInfo( string $permissionid ){

        $detail = $this->detail($permissionid);

        return $detail->isSucceed() ? $this->take($detail['info'])->success() : $detail;
    }



    /**
     * 检测物品权限
     * checkItemPermission
     * @param  string  $userid
     * @param  string  $itemid
     * @param  string  $itemtype
     * @return \APS\ASResult
     */
    public function checkItemPermission( string $userid, string $itemid, string $itemtype ){

        $t = time();
        $list = $this->list(['expire'=>"[[>]]$t",'userid'=>$userid,'itemid'=>$itemid,'itemtype'=>$itemtype],1,25,'expire DESC, createtime DESC');

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
     * @return \APS\ASResult
     */
    public function setItemPermission( string $userid, string $itemid, string $itemtype, int $expire, $info = null, string $status = 'enabled' ){

        if( $this->countItemPermission($userid,$itemid,$itemtype,null,'enabled') > 0){

            return $this->getDB()->update( [
                'expire'=>$expire,
                'info'=>$info,
                'status'=>$status
            ],
                static::$table,
                ['userid'=>$userid,'itemid'=>$itemid,'itemtype'=>$itemtype]
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
     * @return \APS\ASResult
     */
    public function setItemExpired( string $userid, string $itemid, string $itemtype ){

        return $this->setItemPermission($userid,$itemid,$itemtype,0);

    }


}