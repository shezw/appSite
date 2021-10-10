<?php
/**
 * AnalysisProduct.php
 *
 * Description
 *
 *
 */

namespace APS;


class AnalysisProduct extends ASModel
{

    const table     = "analysis_product";
    const comment   = '商品统计';
    const primaryid = "uid";
    const addFields = [
        'uid','saasid','userid',
        'cover','title','price','sale','count','total',
    ];
    const updateFields = [
        'cover','title','price','sale','count','total',
    ];
    const detailFields = [
        'uid','saasid','userid',
        'cover','title','price','sale','count','total','createtime','lasttime'
    ];
    const overviewFields = [
        'uid','saasid','userid',
        'cover','title','price','sale','count','total',
        'createtime','lasttime',
    ];
    const listFields = [
        'uid','saasid','userid',
        'cover','title','price','sale','count','total',
        'createtime','lasttime',
    ];
    const filterFields = [
        'uid','saasid','userid',
        'cover','title','price','sale','count','total',
        'createtime','lasttime',
    ];
    const depthStruct = [
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'price'=>DBField_Decimal,
        'sale'=>DBField_Decimal,
        'count'=>DBField_Int,
        'total'=>DBField_Decimal
    ];

    const tableStruct = [

        'uid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'主ID' ,  'idx'=>DBIndex_Unique ],
        'saasid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas','idx'=>DBIndex_Index,],
        'userid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'用户ID' , 'idx'=>DBIndex_Index ],
        'title'=>    ['type'=>DBField_String,    'len'=>127, 'nullable'=>0,  'cmt'=>'标题 60字以内 分词' ,  'idx'=>DBIndex_FullText ],
        'cover'=>    ['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'缩略图 小图' ],

        'price'=>    ['type'=>DBField_Decimal,   'len'=>'12,5',  'nullable'=>0,  'cmt'=>'价格' ,   'dft'=>0,       ],
        'sale'=>     ['type'=>DBField_Decimal,   'len'=>'12,5',  'nullable'=>0,  'cmt'=>'售价' ,   'dft'=>0,       ],

        'count'=>    ['type'=>DBField_Int,       'len'=>8,   'nullable'=>0,  'cmt'=>'库存数量(非多类型商品)',  'dft'=>1,       ],
        'total'=>    ['type'=>DBField_Decimal,   'len'=>'12,5',  'nullable'=>0,  'cmt'=>'总价' ,   'dft'=>0,       ],

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];

}