<?php

namespace APS;

/**
 * 表单 - 合约
 * FormContract
 *
 * 合约可以提供线上合约的机制，其中双方的Sign由IBChian模块支持，即Sign使用Block的HASH，双方签署的信息可追述
 *
 * @package APS\service
 */
class FormContract extends ASModel{


    public static $table     = "form_contract";  // 表
    public static $primaryid = "contractid";     // 主字段
    public static $addFields  = [
        'userid','targetid','itemid','itemtype',
        'title','description','cover','terms','information','attachments',
        'signa','signb',
        'price','paytype','time','payduration','custompay','payoffset','payments',
        'starttime','endtime',
        'status',
        'sort','featured',
    ];
    public static $updateFields  = [
        'information','attachments',
        'signa','signb',
        'status',
        'sort','featured',
    ];
    public static $detailFields  = [
        'contractid','userid','targetid','itemid','itemtype',
        'title','description','cover','terms','information','attachments',
        'signa','signb',
        'price','paytype','time','payduration','custompay','payoffset','payments',
        'starttime','endtime',
        'status',
        'sort','featured','createtime','lasttime'
    ];
    public static $overviewFields  = [
        'contractid','userid','targetid','itemid','itemtype',
        'title','description','cover',
        'signa','signb',
        'price','paytype','time','payduration','payoffset','payments',
        'starttime','endtime',
        'status',
        'sort','featured','createtime','lasttime'
    ];
    public static $listFields  = [
        'contractid','userid','targetid','itemid','itemtype',
        'title','description','cover','information',
        'signa','signb',
        'price','paytype','time','payduration','custompay','payoffset',
        'starttime','endtime',
        'status',
        'sort','featured','createtime','lasttime'
    ];
    public static $countFilters  = [
        'contractid','userid','targetid','itemid','itemtype',
        'price','paytype','time','payduration',
        'starttime','endtime',
        'status',
        'sort','featured','createtime','lasttime'
    ];
    public static $depthStruct  = [

        'price'=>'int',
        'time'=>'int',
        'payoffset'=>'int',

        'featured'=>'int',
        'sort'=>'int',
        'createtime'=>'int',
        'lasttime'=>'int',
        'starttime'=>'int',
        'endtime'=>'int',
        'attachments'=>'ASJson',
        'information'=>'ASJson'
    ];

}