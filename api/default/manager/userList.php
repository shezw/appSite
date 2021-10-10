<?php
/**
 * Description
 * itemList.php
 */

namespace manager;

use APS\ASResult;
use APS\Category;
use APS\Time;
use APS\User;

/**
 * 通用列表查询
 * itemList
 * @package manager
 */
class userList extends \APS\ASAPI{

    const scope = ASAPI_Scope_Public;
    const mode = ASAPI_Mode_Json;

    const groupCharacterRequirement = [GroupRole_Super,GroupRole_Manager,GroupRole_Editor];
    const groupLevelRequirement = GroupLevel_Editor;

    public function run(): ASResult
    {
        $filters = $this->params['filters'] ?? ['status'=>'enabled'] ;
        $page    = $this->params['page'] ?? 1;
        $size    = $this->params['size'] ?? 20;
        $order   = $this->params['order'] ?? 'createtime DESC';

        $getCount =  User::common()->countByArray($filters) ?? ASResult::shared();
        $getList  =  User::common()->listByArray( $filters, $page, $size, $order) ?? ASResult::shared();

        $list = [];
        $maxPage = (int)(($getCount->getContent() - 1 )/ $size + 1);
        $list['nav'] = $list['navigation'] = [
            'total'=> $getCount->getContent(),
            'count'=> $maxPage,'max'=> $maxPage,
            'current'=> $page,'page'=> $page,
            'size'=> $size
        ];

        if( !$getList->isSucceed() ){
            return $this->take($list)->error(400,$getList->getMessage());
        }

        $list['list'] = $getList->getContent();

        for ( $i = 0; $i < count($list['list']); $i ++ ){

            if( isset($list['list'][$i]['status']) ){
                $list['list'][$i]['status_'] = i18n($list['list'][$i]['status']);
            }
            if( isset($list['list'][$i]['type']) ){
                $list['list'][$i]['type_'] = i18n($list['list'][$i]['type']);
            }

            $list['list'][$i]['createtime_'] = Time::common($list['list'][$i]['createtime'])->humanityOutput();
            $list['list'][$i]['lasttime_'] = Time::common($list['list'][$i]['lasttime'])->humanityOutput();

        }

        return $this->take($list)->success();
    }


}