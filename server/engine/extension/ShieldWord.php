<?php

namespace APS;

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
     * @return ASResult  int | string | string[]
     */
    public function inspection( $input, $rewrite = false, $shieldList = null ){

        $checker   = $rewrite ? $input : 0;
        $blockList = $shieldList ? $shieldList : $this->getBlockList()->getContent();

        if (gettype($input)=='array') {
            foreach ($input as $key => $value) {
                if($rewrite){
                    $checker[$key] = $this->inspection($value,$rewrite,$blockList);
                }else{
                    $checker += $this->inspection($value,$rewrite,$blockList);
                }
            }
        }elseif(gettype($input)=='string'){
            for ($i=0; $i < count($blockList); $i++) {
                $rewrite ? $checker=str_replace($blockList[$i],static::converBlocks($blockList[$i]),$checker) : $checker += substr_count($input,$blockList[$i]);
            }
        }

        return $checker;
    }


    public static function converBlocks( string $input ): string
    {

        $convers = '';

        for ($i=0; $i < mb_strlen($input,'utf8'); $i++) {
            $convers .= '*';
        }

        return $convers;
    }

    /**
     * 查询屏蔽词列表
     * getBlockList
     * @return ASResult
     */
    public function getBlockList(  ): ASResult
    {

        $conditions = ASDB::spliceCondition([
            'status'=>'enabled'
        ]);

        if ($this->count(['status'=>'enabled'])->getContent()<=0){ return $this->error(400,i18n('SYS_NON'),'ShieldWord->getBlockList');}

        $res = $this->getDB()->get('title',static::$table,$conditions,1,10000,'createtime DESC');

        if( !$res->isSucceed() ){ return $res; }

        $shieldList=[];

        for ($i=0; $i < count($res->getContent()); $i++) {
            $shieldList[] = $res->getContent()[$i]['title'];
        }

        return $this->take($shieldList)->success(i18n('SYS_GET_SUC'),'ShieldWord->getBlockList');
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