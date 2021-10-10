<?php

namespace APS;

/**
 * 屏蔽词 ? 重新考虑
 * ShieldWorld
 * @package APS\extension
 */
class ShieldWord extends ASModel{

    const table     = "system_shieldword";
    const comment   = "屏蔽词";
    const primaryid = "uid";
    const addFields = [
        'title',
        'authorid',
        'uid',
        'status',
    ];
    const updateFields = [
        'title',
        'status','createtime','lasttime'
    ];
    const detailFields = [
        'uid', 'authorid',
        'title',
        'status','createtime','lasttime'
    ];
    const overviewFields = [
        'uid', 'authorid',
        'title',
        'status','createtime','lasttime'
    ];
    const listFields = [
        'uid', 'authorid',
        'title',
        'status','createtime','lasttime'
    ];
    const filterFields = [
        'uid',
        'title',
        'authorid',
        'status'
    ];
    const depthStruct = [
        'createtime'=>DBField_Int,
        'lasttime'=>DBField_Int,
    ];

    const tableStruct = [

        'uid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'主ID' ,  'idx'=>DBIndex_Unique ],
        'title'=>    ['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'标签名' ],
        'authorid'=> ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'创建人ID' ,  'idx'=>DBIndex_Index ],

        'type'=>     ['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'添加type时 即为特定类型下的标签' ],

        'status'=>   ['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled', ],

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',   'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];

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

        $conditions = DBConditions::init()->where('status')->equal('enabled');

        if ($this->count($conditions)->getContent()<=0){ return $this->error(400,i18n('SYS_NON'),'ShieldWord->getBlockList');}

        $res = $this->getDB()->get(DBFields::init()->and('title'),static::table,$conditions->limitWith(0,1000) );

        if( !$res->isSucceed() ){ return $res; }

        $shieldList=[];

        for ($i=0; $i < count($res->getContent()); $i++) {
            $shieldList[] = $res->getContent()[$i]['title'];
        }

        return $this->take($shieldList)->success(i18n('SYS_GET_SUC'),'ShieldWord->getBlockList');
    }


}