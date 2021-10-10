<?php

namespace APS;

/**
 * 企业
 * Company
 * @package APS
 */
class Company extends ASModel{

    const table     = "item_company";
    const comment   = "企业";
    const primaryid = "uid";
    const addFields  = [
        'saasid','regionid','areaid','subwayid','districtid','type','authorid',
        'name','cover','gallery','video','contact','website','introduce',
        'industryid',
        'socialcode','companytype','registcapital','registdate','employee','service',
        'financing','financingunit','progress',
        'address','location','lng','lat',
        'open','opencontract',
        'status','createtime','lasttime','featured','sort',
    ];
    const updateFields  = [
        'regionid','areaid','subwayid','districtid','type','authorid',
        'name','cover','gallery','video','contact','website','introduce',
        'industryid',
        'socialcode','companytype','registcapital','registdate','employee','service',
        'financing','financingunit','progress',
        'address','location','lng','lat',
        'open','opencontract',
        'status','createtime','lasttime','featured','sort',
    ];
    const detailFields  = [
        'uid','saasid','regionid','areaid','subwayid','districtid','type','authorid',
        'name','cover','gallery','video','contact','website','introduce',
        'industryid',
        'socialcode','companytype','registcapital','registdate','employee','service',
        'financing','financingunit','progress',
        'address','lng','lat',
        'open','opencontract',
        'status','createtime','lasttime','featured','sort',
    ];
    const overviewFields  = [
        'uid','saasid','regionid','areaid','subwayid','districtid','type','authorid',
        'name','cover','gallery','video','contact','website','introduce',
        'industryid',
        'socialcode','companytype','registcapital','registdate','employee','service',
        'financing','financingunit','progress',
        'address','location','lng','lat',
        'open','opencontract',
        'status','createtime','lasttime','featured','sort',

    ];
    const listFields  = [
        'uid','saasid','regionid','areaid','subwayid','districtid','type','authorid',
        'name','cover','gallery','video','contact','website',
        'industryid',
        'socialcode','companytype','registcapital','registdate','employee','service',
        'financing','financingunit','progress',
        'address','location','lng','lat',
        'open','opencontract',
        'status','createtime','lasttime','featured','sort',

    ];
    const filterFields  = [
        'uid','saasid','regionid','areaid','subwayid','districtid','type','authorid',
        'name','industryid',
        'socialcode','companytype','registcapital','registdate','employee',
        'financing','financingunit','progress',
        'status','createtime','lasttime','featured','sort',
    ];
    const depthStruct  = [
        'lng'=>             DBField_Decimal,
        'lat'=>             DBField_Decimal,
        'registcapital'=>   DBField_Int,
        'registdate'=>      DBField_Int,
        'employee'=>        DBField_Int,
        'financing'=>       DBField_Int,
        'open'=>            DBField_Boolean,
        'opencontract'=>    DBField_Int,
        'sort'=>            DBField_Int,
        'featured'=>        DBField_Boolean,
        'createtime'=>      DBField_TimeStamp,
        'lasttime'=>        DBField_TimeStamp,
    ];

    const tableStruct = [

        'uid'=>             ['type'=>DBField_String,     'len'=>8,       'nullable'=>0,  'cmt'=>'索引ID',    'idx'=>DBIndex_Unique ],
        'saasid'=>          ['type'=>DBField_String,     'len'=>8,       'nullable'=>1,  'cmt'=>'所属saas',  'idx'=>DBIndex_Index,],
        'regionid'=>        ['type'=>DBField_String,     'len'=>8,       'nullable'=>1,  'cmt'=>'所属地域',  'idx'=>DBIndex_Index ],
        'areaid'=>          ['type'=>DBField_String,     'len'=>8,       'nullable'=>1,  'cmt'=>'所属地区',  'idx'=>DBIndex_Index ],
        'subwayid'=>        ['type'=>DBField_String,     'len'=>8,       'nullable'=>1,  'cmt'=>'地铁ID',    'idx'=>DBIndex_Index ],
        'districtid'=>      ['type'=>DBField_String,     'len'=>8,       'nullable'=>1,  'cmt'=>'商圈ID',    'idx'=>DBIndex_Index ],
        'type'=>            ['type'=>DBField_String,     'len'=>12,      'nullable'=>1,  'cmt'=>'写字楼类型', 'idx'=>DBIndex_Index ],
        'authorid'=>        ['type'=>DBField_String,     'len'=>8,       'nullable'=>1,  'cmt'=>'创建人ID',  'idx'=>DBIndex_Index ],
        'name'=>            ['type'=>DBField_String,     'len'=>64,      'nullable'=>0,  'cmt'=>'名称',      'idx'=>DBIndex_FullText ],
        'cover'=>           ['type'=>DBField_String,     'len'=>256,     'nullable'=>1,  'cmt'=>'封面'],
        'gallery'=>         ['type'=>DBField_String,     'len'=>2048,    'nullable'=>1,  'cmt'=>'相册'],
        'video'=>           ['type'=>DBField_String,     'len'=>256,     'nullable'=>1,  'cmt'=>'视频'],
        'contact'=>         ['type'=>DBField_String,     'len'=>128,     'nullable'=>1,  'cmt'=>'联系方式'],
        'website'=>         ['type'=>DBField_String,     'len'=>256,     'nullable'=>1,  'cmt'=>'网站'],
        'introduce'=>       ['type'=>DBField_RichText,   'len'=>-1,      'nullable'=>1,  'cmt'=>'介绍'],
        'industryid'=>      ['type'=>DBField_String,     'len'=>8,       'nullable'=>1,  'cmt'=>'行业'],
        'socialcode'=>      ['type'=>DBField_String,     'len'=>32,      'nullable'=>1,  'cmt'=>'统一社会信用代码'],
        'companytype'=>     ['type'=>DBField_String,     'len'=>24,      'nullable'=>1,  'cmt'=>'公司类型'],
        'registcapital'=>   ['type'=>DBField_Int,        'len'=>13,      'nullable'=>0,  'cmt'=>'注册资本',  'dft'=>0, ],
        'registdate'=>      ['type'=>DBField_Int,        'len'=>13,      'nullable'=>0,  'cmt'=>'成立日期',  'dft'=>0, ],
        'employee'=>        ['type'=>DBField_Int,        'len'=>8,       'nullable'=>0,  'cmt'=>'公司人数',  'dft'=>0, ],
        'service'=>         ['type'=>DBField_RichText,   'len'=>-1,      'nullable'=>1,  'cmt'=>'服务内容'],
        'financing'=>       ['type'=>DBField_Int,        'len'=>13,      'nullable'=>0,  'cmt'=>'融资额度',  'dft'=>0,     ],
        'financingunit'=>   ['type'=>DBField_String,     'len'=>6,       'nullable'=>0,  'cmt'=>'货币单位',  'dft'=>'RMB', ],
        'progress'=>        ['type'=>DBField_String,     'len'=>12 ,     'nullable'=>0,  'cmt'=>'公司阶段',  'dft'=>'none', ],
        // (none,seed,angel,prea,a,preb,b,c,d,dplus,listed,own)
        'address'=>         ['type'=>DBField_String,     'len'=>128,     'nullable'=>1,  'cmt'=>'地址'],
        'location'=>        ['type'=>DBField_Location,   'len'=>-1,      'nullable'=>1,  'cmt'=>'定位'],
        'lng'=>             ['type'=>DBField_Decimal,    'len'=>'14,10', 'nullable'=>0,  'cmt'=>'经度',    'dft'=>0, ],
        'lat'=>             ['type'=>DBField_Decimal,    'len'=>'14,10', 'nullable'=>0,  'cmt'=>'纬度',    'dft'=>0, ],
        'open'=>            ['type'=>DBField_Boolean,    'len'=>1,       'nullable'=>0,  'cmt'=>'是否公开展示 0否,1是' , 'dft'=>1, ],
        'opencontract'=>    ['type'=>DBField_Int,        'len'=>1,       'nullable'=>0,  'cmt'=>'是否公开展示联系方式 0否,1是' ,     'dft'=>1, ],
        'status'=>          ['type'=>DBField_String,     'len'=>12,      'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled', ],

        'createtime'=>      ['type'=>DBField_TimeStamp,  'len'=>13,      'nullable'=>0,  'cmt'=>'创建时间',                      'idx'=>DBIndex_Index, ],
        'lasttime'=>        ['type'=>DBField_TimeStamp,  'len'=>13,      'nullable'=>0,  'cmt'=>'上一次更新时间', ],
        'featured'=>        ['type'=>DBField_Boolean,    'len'=>1,       'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,               'idx'=>DBIndex_Index, ],
        'sort'=>            ['type'=>DBField_Int,        'len'=>5,       'nullable'=>0,  'cmt'=>'优先排序','dft'=>0,              'idx'=>DBIndex_Index, ],

    ];
}