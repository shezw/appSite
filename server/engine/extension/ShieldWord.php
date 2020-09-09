<?php

namespace APS;
?
/**
 * 屏蔽词 ? 重新考虑
 * ShieldWorld
 * @package APS\extension
 */
class ShieldWord extends ASModel{

    /**
     * 敏感词检测
     * inspection
     * @param  array|string $input      输入词
     * @param  bool         $rewrite    是否重写敏感词
     * @param  null         $shieldList
     * @return \APS\ASResult  int | string | string[]
     */
    public function inspection( $input, $rewrite = false, $shieldList = null ){

        $checker   = $rewrite ? $input : 0;
        $blockList = $shieldList ? $shieldList : $this->getBlockList()->getContent();

        if (gettype($input)=='array') {
            foreach ($input as $key => $value) {
                if($rewrite){
                    $checker[$key] = SHIELDWORD::inspection($value,$rewrite,$blockList);
                }else{
                    $checker += SHIELDWORD::inspection($value,$rewrite,$blockList);
                }
            }
        }elseif(gettype($input)=='string'){
            for ($i=0; $i < count($blockList); $i++) {
                $rewrite ? $checker=str_replace($blockList[$i],SHIELDWORD::converBlocks($blockList[$i]),$checker) : $checker += substr_count($input,$blockList[$i]);
            }
        }

        return $checker;
    }


    public static function converBlocks( string $input ){

        $convers = '';

        for ($i=0; $i < mb_strlen($input,'utf8'); $i++) {
            $convers .= '*';
        }

        return $convers;
    }

    /**
     * 查询屏蔽词列表
     * getBlockList
     * @return \APS\ASResult
     */
    public function getBlockList(  ){

        $conditions = SQL::spliceCondition([
            // 'accountid' => $_['accountid'],
            // 'status'    => $_['status'],
            'status'=>'enabled'
        ]);

        if (SHIELDWORD::count(['status'=>'enabled'])['content']<=0){ return RESULT::feedback(400,['SYS_NON'],0,'SHIELDWORD::list');}

        $data = [
            'fields'     => 'title',
            'table'      => 'system_shieldword',
            'sort'       => 'createtime DESC',
            'page'       => 1,
            'size'       => 10000,
            'conditions' => $conditions,
        ];

        $res = $GLOBALS['sql']->get($data);
        if (!RESULT::isSucceed($res)) { return $res; }

        $shieldList=[];

        for ($i=0; $i < count($res['content']); $i++) {
            $shieldList[] = $res['content'][$i]['title'];
        }

        return RESULT::feedback(0,['SYS_GET_SUC'],$shieldList,'SHIELDWORD::getBlockList');

    }


    public static $table     = "system_shieldword";  // 表
    public static $primaryid = "uid";     // 主字段
    public static $addFields = [
        'title',
        'authorid',
        'uid',
        'status',
    ];      // 添加支持字段
    public static $updateFields = [
        'title',
        'status',
    ];   // 更新支持字段
    public static $detailFields = "*";   // 详情支持字段
    public static $overviewFields = [
        'title',
        'authorid',
        'uid',
        'status',
    ]; // 概览支持字段
    public static $listFields = [
        'title',
        'authorid',
        'uid',
        'status',
    ];     // 列表支持字段
    public static $countFilters = [
        'title',
        'authorid',
    ];
    public static $depthStruct = [
        'createtime'=>'int',
        'lasttime'=>'int',
    ];

}