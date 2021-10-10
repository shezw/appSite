<?php

namespace APS;

/**
 * 财务 - 提现
 * FinanceWithdraw
 * @package APS\service
 */
class FinanceWithdraw extends ASModel{

    const table     = "finance_withdraw";
    const comment   = '财务-提现';
    const primaryid = "uid";
    const addFields = [
        'uid','saasid','userid','type',
        'target','amount','callback',
        'status',
    ];
    const updateFields = [
        'status',
        'callback',
    ];
    const detailFields = [
        'uid','saasid','userid','type',
        'target','amount','callback',
        'status','createtime','lasttime'
    ];
    const publicDetailFields = [
        'uid','saasid','userid','type',
        'target','amount',
        'status','createtime','lasttime'
    ];
    const overviewFields = [
        'uid','saasid','userid','type',
        'target','amount',
        'status','createtime','lasttime'
    ];
    const listFields = [
        'uid', 'saasid', 'userid', 'type',
        'target','amount',
        'status','createtime','lasttime'
    ];
    const publicListFields = [
        'uid', 'saasid', 'userid', 'type', 'target', 'amount',
        'status','createtime','lasttime'
    ];
    const filterFields = [
        'uid', 'saasid', 'userid', 'type', 'amount',
        'status','createtime','lasttime'
    ];
    const depthStruct = [
        'createtime'=>DBField_TimeStamp,
        'lasttime'=>DBField_TimeStamp,
        'amount'=>DBField_Decimal,
        'target'=>DBField_Json,
        'callback'=>DBField_Json,
    ];

    const tableStruct = [

        'uid'=>      ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'提现申请ID' ,       'idx'=>DBIndex_Unique ],
        'saasid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'所属saas',         'idx'=>DBIndex_Index,],
        'userid'=>   ['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'收款方ID' ,        'idx'=>DBIndex_Index ],

        'type'=>     ['type'=>DBField_String,    'len'=>16,  'nullable'=>0,  'cmt'=>'提现类型 ', 'dft'=>'wechat_pocket', ],
        'target'=>   ['type'=>DBField_Json,      'len'=>255, 'nullable'=>0,  'cmt'=>'详情 不限 k-v json' ],
        'amount'=>   ['type'=>DBField_Decimal,   'len'=>'12,5',  'nullable'=>0,  'cmt'=>'总价 精度0.01元' ,   'dft'=>0,       ],
        'status'=>   ['type'=>DBField_String,    'len'=>24,  'nullable'=>0,  'cmt'=>'提现状态',  'dft'=>'pending',   ],
        // pending 待确认 rejected 已拒绝  confirmed 已通过  completed 已完成

        'callback'=> ['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'提现成功回调 k-v json' ],
        // eg: {'action':'setvip','params':{'userid':'xxx','expire':158989990}},{'action':'newPromotion','params':{'userid':'xxx','amount':8.88}}

        'createtime'   =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'     =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'上一次更新时间', ],
    ];
}