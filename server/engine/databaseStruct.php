<?php

return [
'user' => [
	'account' => 
	[
		['table'=>'账户'],

		['name'=>'userid',    'type'=>'varchar',  'len'=>8,    'dft'=>'',      'unq'=>1,  'cmt'=>'用户ID 唯一索引'],
		['name'=>'username',  'type'=>'varchar',  'len'=>63,   'dft'=>'NULL',  'unq'=>1,  'cmt'=>'用户名 账号密码登陆用'],
		['name'=>'password',  'type'=>'varchar',  'len'=>255,  'dft'=>'NULL',             'cmt'=>'密码 hash salt加密'],
		['name'=>'email',     'type'=>'varchar',  'len'=>63,   'dft'=>'NULL',   'unq'=>1, 'cmt'=>'邮箱 唯一'],
		['name'=>'mobile',    'type'=>'varchar',  'len'=>24,   'dft'=>'NULL',   'unq'=>1, 'cmt'=>'手机 唯一'],
		['name'=>'nickname',  'type'=>'varchar',  'len'=>63,   'dft'=>'NULL',             'cmt'=>'昵称 30字以内'],
		['name'=>'avatar',    'type'=>'varchar',  'len'=>255,  'dft'=>'NULL',             'cmt'=>'头像 url'],
		['name'=>'cover',     'type'=>'varchar',  'len'=>255,  'dft'=>'NULL',             'cmt'=>'封面 url'],
		['name'=>'description','type'=>'varchar', 'len'=>255,  'dft'=>'NULL',             'cmt'=>'介绍 250字以内'],
		['name'=>'introduce', 'type'=>'text',     'len'=>-1,   'dft'=>'NULL',             'cmt'=>'简介 120字以内'],
		['name'=>'birthday',  'type'=>'bigint',   'len'=>11,   'dft'=>'NULL',             'cmt'=>'生日 时间戳'],
		['name'=>'gender',    'type'=>'varchar',  'len'=>16,   'dft'=>'private',          'cmt'=>'性别 female male private'],

		['name'=>'groupid',   'type'=>'varchar',  'len'=>8,   'dft'=>1,                   'cmt'=>'用户分组 参考user_group'],
		['name'=>'areaid',    'type'=>'varchar',   'len'=>8,   'dft'=>1,                  'cmt'=>'地区分组 参考user_group'],
		['name'=>'status',    'type'=>'varchar',  'len'=>12,   'dft'=>'enabled',          'cmt'=>'状态 enabled 可以 '],

	],

	'info' => 
	[
		['table'=>'详情'],

		['name'=>'userid',    'type'=>'varchar',  'len'=>8,    'dft'=>'',       'unq'=>1, 'cmt'=>'用户ID 唯一索引'],
		['name'=>'gallery',   'type'=>'text',     'len'=>-1,   'dft'=>'NULL',             'cmt'=>'相册 JSON ARRAY'],

		['name'=>'vip',       'type'=>'int',      'len'=>2,    'dft'=>0,                  'cmt'=>'是否vip'],
		['name'=>'vipexpire', 'type'=>'bigint',   'len'=>11,   'dft'=>0,                  'cmt'=>'vip过期时间'],

		['name'=>'realname',  'type'=>'varchar',  'len'=>63,   'dft'=>'NULL',             'cmt'=>'真实姓名 30字以内'],
		['name'=>'idnumber',  'type'=>'varchar',  'len'=>63,   'dft'=>'NULL',             'cmt'=>'身份证号 30字以内'],
		['name'=>'country',   'type'=>'varchar',  'len'=>24,   'dft'=>'NULL',             'cmt'=>'国家 12字以内'],
		['name'=>'province',  'type'=>'varchar',  'len'=>24,   'dft'=>'NULL',             'cmt'=>'省份 12字以内'],
		['name'=>'city',      'type'=>'varchar',  'len'=>24,   'dft'=>'NULL',             'cmt'=>'城市 12字以内'],
		['name'=>'company',   'type'=>'varchar',  'len'=>63,   'dft'=>'NULL',             'cmt'=>'公司 30字以内'],

		['name'=>'wechatid',  'type'=>'varchar',  'len'=>32,   'dft'=>'NULL',   'unq'=>1, 'cmt'=>'微信公众平台openid 默认获取 unionid'],
		['name'=>'weiboid',   'type'=>'varchar',  'len'=>63,   'dft'=>'NULL',             'cmt'=>'微博ID'],
		['name'=>'appleUUID', 'type'=>'varchar',  'len'=>64,   'dft'=>'NULL',             'cmt'=>'苹果UUID'],
		['name'=>'qqid',      'type'=>'varchar',  'len'=>63,   'dft'=>'NULL',             'cmt'=>'qqID'],
		['name'=>'status',    'type'=>'varchar',  'len'=>12,   'dft'=>'enabled',          'cmt'=>'状态 enabled'],
		['name'=>'realstatus','type'=>'varchar',  'len'=>24,   'dft'=>'default',          'cmt'=>'实名状态 '],
		// verified 已认证, pending 审核中, default 默认

	],

	'pocket' =>
	[
		['table'=>'钱包'],

		['name'=>'userid',    'type'=>'varchar',  'len'=>8,    'dft'=>'',      'unq'=>1,  'cmt'=>'用户ID'],
		['name'=>'balance',   'type'=>'bigint',   'len'=>13,   'dft'=>0,                  'cmt'=>'余额 100倍 分为单位 RMB'],
		['name'=>'point',     'type'=>'bigint',   'len'=>13,   'dft'=>0,                  'cmt'=>'积分 带小数点'],

		['name'=>'status',    'type'=>'varchar',  'len'=>12,   'dft'=>'enabled',          'cmt'=>'状态 enabled有效 block屏蔽 审核中pending 锁定中 locked'],
		['name'=>'type',      'type'=>'varchar',  'len'=>16,   'dft'=>'NULL',             'cmt'=>'类型 暂时没用'],

	],


	'group' =>
	[
		['table'=>'分组'],

		['name'=>'groupid',     'type'=>'varchar',  'len'=>8,     'dft'=>'',    'unq'=>1,  'cmt'=>'组唯一ID'],
		['name'=>'type',        'type'=>'varchar',  'len'=>16,     'dft'=>'character',     'cmt'=>'组类型 character 角色 department 部门'],
		['name'=>'parentid',    'type'=>'varchar',  'len'=>8,     'dft'=>'NULL',           'cmt'=>'父级ID'],
		['name'=>'level',       'type'=>'mediumint','len'=>5,     'dft'=>0,                'cmt'=>'权限级别'],
		['name'=>'groupname',   'type'=>'varchar',  'len'=>32,	  'dft'=>'',               'cmt'=>'组名'],
		['name'=>'description', 'type'=>'varchar',  'len'=>255,	  'dft'=>'NULL',           'cmt'=>'描述 120字以内'],
		['name'=>'menuaccess',  'type'=>'text',     'len'=>-1,    'dft'=>'NULL',           'cmt'=>'菜单栏权限'],
		['name'=>'status',      'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',        'cmt'=>'状态 enabled开启 disabled关闭'], 

	],


	'comment' =>
	[
		['table'=>'评论'],

		['name'=>'commentid',     'type'=>'varchar',  'len'=>8,	'dft'=>'',     'unq'=>1,     'cmt'=>'评论ID' ],
		['name'=>'userid',        'type'=>'varchar',  'len'=>8,	'dft'=>'',     'idx'=>1,     'cmt'=>'用户ID' ],
		['name'=>'itemid',       'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL', 'idx'=>1, 'cmt'=>'回复ID ' ],
		['name'=>'itemtype',     'type'=>'varchar',  'len'=>32,	    'dft'=>'NULL', 'idx'=>1, 'cmt'=>'回复类型' ],

		['name'=>'title',         'type'=>'varchar',  'len'=>63,    'dft'=>'NULL', 'ngr'=>1, 'cmt'=>'标题 30字以内' ],
		['name'=>'content',	      'type'=>'varchar',  'len'=>511,   'dft'=>'NULL', 'ngr'=>1, 'cmt'=>'内容 250字以内' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,	'dft'=>'enabled',        'cmt'=>'状态 enabled开启 disabled关闭' ],

	],

	// 收藏
	// collect 用于 多对多 单方向关系  以type区分不同的关系分类
	// collect 收藏 ,like 点赞, follow 关注
	'collect'  => 
	[

		['table'=>'收藏'],

		['name'=>'collectid',      'type'=>'varchar',  'len'=>8,	'dft'=>'',     'unq'=>1, 'cmt'=>'主ID'], 
		['name'=>'userid',         'type'=>'varchar',  'len'=>8,	'dft'=>'',     'idx'=>1, 'cmt'=>'用户ID'], 
		['name'=>'type',           'type'=>'varchar',  'len'=>16,	'dft'=>'NULL', 'idx'=>1, 'cmt'=>'类型 '], 
		['name'=>'itemid',         'type'=>'varchar',  'len'=>8,	'dft'=>'NULL', 'idx'=>1, 'cmt'=>'回复ID '], 
		['name'=>'itemtype',       'type'=>'varchar',  'len'=>32,	'dft'=>'NULL', 'idx'=>1, 'cmt'=>'回复类型'], 
		['name'=>'rate',           'type'=>'int',      'len'=>3,	'dft'=>1,                'cmt'=>'强度'], 
		// like type eg: superlike 5  like 1  normal 0 dislike -1  hate -5
		['name'=>'title',          'type'=>'varchar',  'len'=>63,  'dft'=>'NULL', 'ngr'=>1,  'cmt'=>'标题'], 
		['name'=>'cover',     	   'type'=>'varchar',  'len'=>255, 'dft'=>'NULL',            'cmt'=>'封面'], 
		['name'=>'description',	   'type'=>'varchar',  'len'=>255, 'dft'=>'NULL', 'ngr'=>1,  'cmt'=>'描述'], 
		['name'=>'contents',       'type'=>'text',     'len'=>-1,  'dft'=>'NULL',            'cmt'=>'内容'], 
		['name'=>'status',         'type'=>'varchar',  'len'=>12,  'dft'=>'enabled',         'cmt'=>'状态 enabled开启 disabled关闭'], 
	],

	// 偏好/个人设置
	// 通过用户和对应的key查找个人配置 类似于system_setting 区别在于仅对个人生效
	'preference' =>
	[
		['table'=>'偏好/个人设置'],

		['name'=>'preferenceid',   'type'=>'varchar',  'len'=>8,	'dft'=>'',     'unq'=>1, 'cmt'=>'主ID' ],
		['name'=>'userid',         'type'=>'varchar',  'len'=>8,	'dft'=>'',     'idx'=>1, 'cmt'=>'用户ID' ],
		['name'=>'keyid',          'type'=>'varchar',  'len'=>16,	'dft'=>'',     'idx'=>1, 'cmt'=>'查询key' ],
		['name'=>'description',    'type'=>'varchar',  'len'=>255,  'dft'=>'NULL',           'cmt'=>'描述 120字以内' ],
		['name'=>'content',        'type'=>'text',     'len'=>-1,   'dft'=>'',               'cmt'=>'设置内容 k-v json' ],
		['name'=>'type',           'type'=>'varchar',  'len'=>16,   'dft'=>'JSON',           'cmt'=>'格式 JSON INT DOUBLE FLOAT STRING BOOL BOOLEAN ' ],
		['name'=>'version',        'type'=>'int',      'len'=>8,    'dft'=>1,                'cmt'=>'版本号 整数 ' ],
		['name'=>'status',         'type'=>'varchar',  'len'=>12,  'dft'=>'enabled',         'cmt'=>'状态 enabled开启 disabled关闭'], 

	],

],


// **************************************************
//
// ITEM
// 产品、商品相关

// 分类  类型type是固定的，分类category可以无限增加 类似于文件夹的系统

'item' => [

	'industry' =>
	[
		['table'=>'行业'],

		['name'=>'industryid',    'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,  'cmt'=>'行业ID' ],
		['name'=>'parentid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,   'cmt'=>'上一级ID'],
		['name'=>'level',         'type'=>'tinyint', 'len'=>3,    'dft'=>'NULL', 'cmt'=>'区域级别' ],

		//基础字段
		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',      'ngr'=>1,  'cmt'=>'名称名' ],
		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',  'ngr'=>1,  'cmt'=>'描述' ],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',             'cmt'=>'封面' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',          'cmt'=>'状态 ' ],

	],

	'region' =>
	[
		['table'=>'地域'],

		['name'=>'regionid',    'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,  'cmt'=>'地域ID' ],
		['name'=>'parentid',    'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'idx'=>1,  'cmt'=>'上一级ID' ],

		//基础字段
		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',      'ngr'=>1,  'cmt'=>'名称名' ],
		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',  'ngr'=>1,  'cmt'=>'描述' ],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',             'cmt'=>'封面' ],
		['name'=>'gallery',       'type'=>'text',     'len'=>-1,	'dft'=>'NULL',             'cmt'=>'详情介绍' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',          'cmt'=>'状态 ' ],

	],

	'subway' =>
	[

		['table'=>'地铁'],

		['name'=>'subwayid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,   'cmt'=>'地铁ID'],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,   	'dft'=>'NULL',  'idx'=>1,   'cmt'=>'创建人ID'],
		['name'=>'parentid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,   'cmt'=>'上一级ID'],
		['name'=>'regionid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,   'cmt'=>'地域ID'],

		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',                  'cmt'=>'分类名'],
		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',              'cmt'=>'描述'],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',              'cmt'=>'封面'],
		['name'=>'gallery',       'type'=>'text',     'len'=>-1,	'dft'=>'NULL',              'cmt'=>'相册'],

		['name'=>'location',      'type'=>'GEOMETRY','len'=>-1,    'dft'=>'',   'spt'=>1, 'cmt'=>'定位 GeomFromWKB'  ], 
		['name'=>'lng',           'type'=>'decimal', 'len'=>'14,10', 'dft'=>0,   'cmt'=>'经度' ],
		['name'=>'lat',           'type'=>'decimal', 'len'=>'14,10', 'dft'=>0,   'cmt'=>'纬度' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',           'cmt'=>'状态 '],

	],

	'bsdistrict' =>
	[

		['table'=>'商圈'],

		['name'=>'bsdistrictid',  'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,   'cmt'=>'商圈ID'],
		['name'=>'regionid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,   'cmt'=>'地域ID'],
		['name'=>'areaid',        'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,   'cmt'=>'地区ID'],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,   	'dft'=>'NULL',  'idx'=>1,   'cmt'=>'创建人ID'],
		['name'=>'parentid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,   'cmt'=>'上一级ID'],

		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',                  'cmt'=>'分类名'],
		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',              'cmt'=>'描述'],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',              'cmt'=>'封面'],
		['name'=>'gallery',       'type'=>'text',     'len'=>-1,	'dft'=>'NULL',              'cmt'=>'相册'],

		['name'=>'location',      'type'=>'GEOMETRY','len'=>-1,    'dft'=>'',   'spt'=>1, 'cmt'=>'定位 GeomFromWKB'  ], 
		['name'=>'lng',           'type'=>'decimal', 'len'=>'14,10', 'dft'=>0,   'cmt'=>'经度' ],
		['name'=>'lat',           'type'=>'decimal', 'len'=>'14,10', 'dft'=>0,   'cmt'=>'纬度' ],
		// ['name'=>'geo', 'type'=>'varchar', 'len'=>32,    'cmt'=>'形状 ASJson' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',           'cmt'=>'状态 '],

	],


	'area'=>
	[
		['table'=>'地区'],

		['name'=>'areaid',        'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,   'cmt'=>'分类ID'],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,   	'dft'=>'NULL',  'idx'=>1,   'cmt'=>'创建人ID'],
		['name'=>'parentid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,   'cmt'=>'上一级ID'],

		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',                  'cmt'=>'分类名'],
		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',              'cmt'=>'描述'],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',              'cmt'=>'封面'],
		['name'=>'gallery',       'type'=>'text',     'len'=>-1,	'dft'=>'NULL',              'cmt'=>'相册'],

		['name'=>'mergename',     'type'=>'varchar', 'len'=>64,   'dft'=>'NULL', 'cmt'=>'合并名称' ],
		['name'=>'shortname',     'type'=>'varchar', 'len'=>32,   'dft'=>'NULL', 'cmt'=>'简称' ],
		['name'=>'mergeshortname','type'=>'varchar', 'len'=>32,   'dft'=>'NULL', 'cmt'=>'合并简称' ],
		['name'=>'level',         'type'=>'tinyint', 'len'=>3,    'dft'=>'NULL', 'cmt'=>'区域级别' ],
		['name'=>'code',          'type'=>'varchar', 'len'=>12,   'dft'=>'NULL', 'cmt'=>'区号' ],
		['name'=>'zipcode',       'type'=>'varchar', 'len'=>12,   'dft'=>'NULL', 'cmt'=>'邮编' ],
		['name'=>'location',      'type'=>'GEOMETRY','len'=>-1,   'dft'=>'', 'spt'=>1, 'cmt'=>'定位 GeomFromWKB'  ], 
		['name'=>'lng',           'type'=>'decimal', 'len'=>'14,10', 'dft'=>0,   'cmt'=>'经度' ],
		['name'=>'lat',           'type'=>'decimal', 'len'=>'14,10', 'dft'=>0,   'cmt'=>'纬度' ],
		// ['name'=>'geo', 'type'=>'varchar', 'len'=>32,    'cmt'=>'形状 ASJson' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',           'cmt'=>'状态 '],

	],


	'company' =>
	[
		['table'=>'企业'],

		['name'=>'companyid',    'type'=>'varchar',   'len'=>8,      'dft'=>'',     'cmt'=>'索引ID',   'unq'=>1, ],
		['name'=>'regionid',     'type'=>'varchar',   'len'=>8,      'dft'=>'NULL', 'cmt'=>'所属地域',  'idx'=>1, ],
		['name'=>'areaid',       'type'=>'varchar',   'len'=>8,      'dft'=>'NULL', 'cmt'=>'所属地区',  'idx'=>1, ],
		['name'=>'subwayid',     'type'=>'varchar',   'len'=>8,      'dft'=>'NULL', 'cmt'=>'地铁ID',   'idx'=>1, ],
		['name'=>'bsdistrict',   'type'=>'varchar',   'len'=>8,      'dft'=>'NULL', 'cmt'=>'商圈ID',   'idx'=>1, ],
		['name'=>'type',         'type'=>'varchar',   'len'=>12,     'dft'=>'NULL', 'cmt'=>'写字楼类型','idx'=>1, ],
		['name'=>'authorid',     'type'=>'varchar',   'len'=>8,      'dft'=>'NULL', 'cmt'=>'创建人ID', 'idx'=>1, ],
		['name'=>'name',         'type'=>'varchar',   'len'=>64,     'dft'=>'',     'cmt'=>'名称',     'ngr'=>1, ],
		['name'=>'cover',        'type'=>'varchar',   'len'=>256,    'dft'=>'NULL', 'cmt'=>'封面'],
		['name'=>'gallery',      'type'=>'varchar',   'len'=>2048,   'dft'=>'NULL', 'cmt'=>'相册'],
		['name'=>'video',        'type'=>'varchar',   'len'=>256,    'dft'=>'NULL', 'cmt'=>'视频'],
		['name'=>'contact',      'type'=>'varchar',   'len'=>128,    'dft'=>'NULL', 'cmt'=>'联系方式'],
		['name'=>'website',      'type'=>'varchar',   'len'=>256,    'dft'=>'NULL', 'cmt'=>'网站'],
		['name'=>'introduce',    'type'=>'text',      'len'=>-1,     'dft'=>'NULL', 'cmt'=>'介绍'],
		['name'=>'industryid',   'type'=>'varchar',   'len'=>8,      'dft'=>'NULL', 'cmt'=>'行业'],
		['name'=>'socialcode',   'type'=>'varchar',   'len'=>32,     'dft'=>'NULL', 'cmt'=>'统一社会信用代码'],
		['name'=>'companytype',  'type'=>'varchar',   'len'=>24,     'dft'=>'NULL', 'cmt'=>'公司类型'],
		['name'=>'registcapital','type'=>'bigint',    'len'=>13,     'dft'=>0,      'cmt'=>'注册资本'],
		['name'=>'registdate',   'type'=>'bigint',    'len'=>13,     'dft'=>0,      'cmt'=>'成立日期'],
		['name'=>'employee',     'type'=>'mediumint', 'len'=>8,      'dft'=>0,      'cmt'=>'公司人数'],
		['name'=>'service',      'type'=>'text',      'len'=>-1,     'dft'=>'NULL', 'cmt'=>'服务内容'],
		['name'=>'financing',    'type'=>'bigint',    'len'=>13,     'dft'=>0,      'cmt'=>'融资额度'],
		['name'=>'financingunit','type'=>'varchar',   'len'=>6,      'dft'=>'RMB',  'cmt'=>'货币单位'],
		['name'=>'progress',     'type'=>'varchar',   'len'=>12 ,    'dft'=>'none', 'cmt'=>'公司阶段(none,seed,angel,prea,a,preb,b,c,d,dplus,listed,own)'],
		['name'=>'address',      'type'=>'varchar',   'len'=>128,    'dft'=>'NULL', 'cmt'=>'地址'],
		['name'=>'location',     'type'=>'GEOMETRY',  'len'=>-1,     'dft'=>'NULL', 'cmt'=>'定位'],
		['name'=>'lng',          'type'=>'decimal',   'len'=>'14,10', 'dft'=>0,      'cmt'=>'经度'],
		['name'=>'lat',          'type'=>'decimal',   'len'=>'14,10', 'dft'=>0,      'cmt'=>'纬度'],
		['name'=>'open',         'type'=>'tinyint',   'len'=>1,       'dft'=>1,      'cmt'=>'是否公开展示 0否,1是' ],
		['name'=>'opencontract', 'type'=>'tinyint',   'len'=>1,       'dft'=>1,      'cmt'=>'是否公开展示联系方式 0否,1是' ],
		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled','cmt'=>'状态'],

	],

	'category'=>
	[
		['table'=>'分类'],

		['name'=>'categoryid',    'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,  'cmt'=>'分类ID' ],
		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',                 'cmt'=>'分类名' ],

		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'创建人ID' ],
		['name'=>'parentid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'上一级ID' ],
		['name'=>'type',          'type'=>'varchar',  'len'=>16,	'dft'=>'NULL',             'cmt'=>'类型' ],

		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',             'cmt'=>'描述' ],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',             'cmt'=>'封面' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',          'cmt'=>'状态 ' ],

	],

	'media'=>
	[
		['table'=>'媒体'],

		['name'=>'mediaid',       'type'=>'varchar',  'len'=>8,	'dft'=>'',  'unq'=>1,   'cmt'=>'图像ID'],
		['name'=>'categoryid',    'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1, 'cmt'=>'分类ID'],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1, 'cmt'=>'所有者 '],
		
		// ['name'=>'permission',    'type'=>'mediumint','len'=>5,	    'dft'=>0,               'cmt'=>'权限需求'],

		['name'=>'type',          'type'=>'varchar',  'len'=>16,	'dft'=>'NULL', 'idx'=>1, 'cmt'=>'类型 '],

		['name'=>'url',           'type'=>'varchar',  'len'=>255,	'dft'=>'',              'cmt'=>'url地址'],
		['name'=>'size',          'type'=>'int',      'len'=>20,    'dft'=>0,               'cmt'=>'文件大小'],
		['name'=>'meta',          'type'=>'text',     'len'=>-1,	'dft'=>'NULL',          'cmt'=>'元信息 k-v json'],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,	'dft'=>'enabled',       'cmt'=>'状态 enabled开启 disabled关闭'],

		['name'=>'password',      'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',          'cmt'=>'密码访问'],
		// 密码访问
	],



	// 场馆
	// 后台用高德jsapi 选址组件
	'venue'=>
	[
		['table'=>'场馆'],

		['name'=>'venueid',       'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,   'cmt'=>'场馆ID'  ],
		['name'=>'categoryid',    'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,   'cmt'=>'分类ID'  ],
		['name'=>'type',          'type'=>'varchar',  'len'=>16,	'dft'=>'NULL',              'cmt'=>'类型 lesson, vip...'  ],
		['name'=>'areaid',        'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,   'cmt'=>'地区ID'  ],
		['name'=>'authorid',        'type'=>'varchar',  'len'=>8,	'dft'=>'NULL',  'idx'=>1,   'cmt'=>'创建人ID'  ],

		//基础字段
		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',      'ngr'=>1,   'cmt'=>'名称名'  ],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'',                  'cmt'=>'封面'  ],
		['name'=>'tags',          'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',              'cmt'=>'标签 最高255'  ],
		['name'=>'thumb',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',              'cmt'=>'缩略图'  ],
		['name'=>'address',       'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',              'cmt'=>'地址'  ],
		['name'=>'location',      'type'=>'GEOMETRY', 'len'=>-1,    'dft'=>'',      'spt'=>1,     'cmt'=>'定位 GeomFromWKB(POINT(110.0003,39.0002))'  ],


		['name'=>'lng',           'type'=>'decimal',   'len'=>'14,10',    'dft'=>0,                   'cmt'=>'经度'  ],
		['name'=>'lat',           'type'=>'decimal',   'len'=>'14,10',    'dft'=>0,                   'cmt'=>'纬度'  ],

		['name'=>'phone',         'type'=>'varchar',  'len'=>64,    'dft'=>'NULL',              'cmt'=>'联系电话 '  ],
		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',  'ngr'=>1,   'cmt'=>'描述'  ],
		['name'=>'introduce',     'type'=>'text',     'len'=>-1,	'dft'=>'NULL',              'cmt'=>'详情介绍'  ],

		//开放信息
		['name'=>'opentime',      'type'=>'varchar',  'len'=>128,   'dft'=>'NULL',    'cmt'=>'开放时间 [["09:00","17:30"],[]...]'  ],
		['name'=>'opendays',      'type'=>'varchar',  'len'=>128,   'dft'=>'NULL',    'cmt'=>'开放日  [1,2,3,4,5,6,7];'  ],

		['name'=>'viewtimes',     'type'=>'bigint',      'len'=>13,    'dft'=>0,                   'cmt'=>'播放次数'  ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',           'cmt'=>'状态 '  ],

	],


	// 活动室 
	'room'=>
	[
		['table'=>'活动室'],

		['name'=>'roomid',        'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,  'cmt'=>'场馆ID' ],
		['name'=>'venueid',       'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'分类ID' ],
		['name'=>'categoryid',    'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'分类ID' ],
		['name'=>'authorid',        'type'=>'varchar',  'len'=>8,	'dft'=>'NULL',  'idx'=>1,  'cmt'=>'创建人ID' ],
		['name'=>'type',          'type'=>'varchar',  'len'=>16,	'dft'=>'NULL',             'cmt'=>'类型 lesson, vip...' ],

		//基础字段
		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',      'ngr'=>1,  'cmt'=>'名称名' ],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'',                 'cmt'=>'封面' ],
		['name'=>'tags',          'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',             'cmt'=>'标签 最高255' ],
		['name'=>'thumb',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',             'cmt'=>'缩略图' ],

		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',  'ngr'=>1,  'cmt'=>'描述' ],
		['name'=>'support',       'type'=>'varchar',  'len'=>511,   'dft'=>'NULL',  'ngr'=>1,  'cmt'=>'配套设备' ],
		['name'=>'introduce',     'type'=>'text',     'len'=>-1,	'dft'=>'NULL',             'cmt'=>'详情介绍' ],

		['name'=>'acreage',       'type'=>'int',      'len'=>8  ,   'dft'=>0 ,                 'cmt'=>'面积' ],
		['name'=>'capacity',      'type'=>'int',      'len'=>8  ,   'dft'=>0 ,                 'cmt'=>'容量' ],

		//开放信息 开放信息模版
		['name'=>'opens',         'type'=>'text',     'len'=>-1,   'dft'=>'NULL',             'cmt'=>'活动室周开放信息 eg: [[{"09:00-10:30":"on"},{"10:30-12:00":"on"},{"14:00-15:30":"on"},{"15:30-17:00":"on"}],[],[],[],[],[],[]]' ],

		//订单字段
		['name'=>'price',         'type'=>'int',      'len'=>8  ,   'dft'=>0,                  'cmt'=>'价格' ],
		['name'=>'costpoint',     'type'=>'int',      'len'=>8  ,   'dft'=>0,                  'cmt'=>'扣除积分' ],

		['name'=>'viewtimes',     'type'=>'bigint',   'len'=>13,    'dft'=>0,                  'cmt'=>'播放次数' ],
		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',          'cmt'=>'状态 ' ],

	],


	// 活动室场次
	'roomround'=>
	[
		['table'=>'活动室场次'],

		['name'=>'roomroundid', 'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,  'cmt'=>'场次ID' ],
		['name'=>'roomid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'idx'=>1,  'cmt'=>'活动室ID' ],

		['name'=>'opens',       'type'=>'text',     'len'=>-1,   'dft'=>'NULL',             'cmt'=>'活动室开放信息 ' ],
		// JSON Array 
		// [{row:[{col:col,status:status},{col:col,status:status}...]},{}...]  status on,off,used
		// eg: [{"一":[{"col":"A","status":"on"},{"col":"B","status":"on"}]},{"二":[{"col":"A","status":"off"},{"col":"B","status":"used"}]}]

		// 活动室定时任务:
		// 当前日期之前的所有活动室场次设置为下线 （从数据统计角度来说 暂不考虑删除）
		// 从当前日期开始增加活动室场次，场次信息从活动室开放信息获取模板 存在则跳过，最大七天 开始时间用0点时间戳+1 结束时间以时间戳第二天0点时间戳-1   7日以内的活动室(未被预定)可以支持手动编辑状态

		['name'=>'date',          'type'=>'int',    'len'=>11,    'dft'=>625312800,          'cmt'=>'开始时间' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',          'cmt'=>'状态 ' ],
		// 场次状态
		// expired 过期  offline 下线  

	],


	// 活动
	'active'=>
	[
		['table'=>'活动'],

		['name'=>'activeid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1, 'cmt'=>'活动ID' ],
		['name'=>'categoryid',    'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'分类ID' ],
		['name'=>'venueid',       'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'场馆ID' ],
		['name'=>'areaid',        'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'地区ID' ],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'创建人ID' ],
		['name'=>'type',          'type'=>'varchar',  'len'=>16,	'dft'=>'NULL',            'cmt'=>'类型 lesson, vip...' ],

		//基础字段
		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',      'ngr'=>1, 'cmt'=>'名称名' ],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'',                'cmt'=>'封面' ],
		['name'=>'tags',          'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',            'cmt'=>'标签 最高255' ],
		['name'=>'thumb',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',            'cmt'=>'缩略图' ],
		['name'=>'address',       'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',            'cmt'=>'地址' ],
		['name'=>'phone',         'type'=>'varchar',  'len'=>64,    'dft'=>'NULL',            'cmt'=>'联系电话 ' ],
		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',  'ngr'=>1, 'cmt'=>'描述' ],
		['name'=>'introduce',     'type'=>'text',     'len'=>-1,	'dft'=>'NULL',            'cmt'=>'详情介绍' ],

		['name'=>'information',   'type'=>'varchar',  'len'=>1024,	'dft'=>'NULL',            'cmt'=>'补充信息 ' ],
		// information.organiser    string 64    // 主办单位
		// information.coorganizer  string 64    // 协办单位
		// information.exhibitor    string 64    // 承办单位
		// information.performer    string 64    // 表演单位
		// information.speaker      string 64    // 主讲人
		// information.remark       string 128   // 提示

		//订单字段
		['name'=>'paytype',       'type'=>'varchar',  'len'=>8  ,   'dft'=>'point',           'cmt'=>'支付类型 balance,point,cash' ],
		['name'=>'price',         'type'=>'int',      'len'=>8  ,   'dft'=>0,                 'cmt'=>'价格' ],
		['name'=>'limitpoint',    'type'=>'int',      'len'=>8  ,   'dft'=>0,                 'cmt'=>'积分限制' ],
		['name'=>'costpoint',     'type'=>'int',      'len'=>8  ,   'dft'=>0,                 'cmt'=>'扣除积分' ],
		['name'=>'breachpoint',   'type'=>'int',      'len'=>8  ,   'dft'=>0,                 'cmt'=>'违约扣除积分' ],
		['name'=>'maxorder',      'type'=>'int',      'len'=>8  ,   'dft'=>1,                 'cmt'=>'订单次数限制' ],
		['name'=>'maxticket',     'type'=>'int',      'len'=>8  ,   'dft'=>5,                 'cmt'=>'单次票数限制' ],

		['name'=>'roundtype',     'type'=>'varchar',   'len'=>32,	'dft'=>'free',            'cmt'=>'参与类型' ],
		// free 直接前往, chooseSeat 在线选座, freeSeat 自由入座, private 不公开 不可预定  

		['name'=>'starttime',     'type'=>'bigint',   'len'=>13,   'dft'=>625312800,         'cmt'=>'开始时间' ],
		['name'=>'endtime',       'type'=>'bigint',   'len'=>13,   'dft'=>625312801,         'cmt'=>'结束时间' ],

		['name'=>'idverify',      'type'=>'int',       'len'=>1,    'dft'=>0,                 'cmt'=>'是否需要身份 ' ],
		['name'=>'idorder',       'type'=>'int',       'len'=>1,    'dft'=>0,                 'cmt'=>'是否可以身份证取票 ' ],

		// 场次信息
		// 存储在item_activeround表中 以activeid关联

		//状态字段
		['name'=>'viewtimes',     'type'=>'bigint',   'len'=>13,    'dft'=>0,              'cmt'=>'播放次数' ],
		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',         'cmt'=>'状态  trash 垃圾桶' ],

	],


	// 座位模版 
	// 序列号和坐次号独立
	'activeseats'=>
	[
		['table'=>'座位模版'],

		['name'=>'seatsid',       'type'=>'varchar',  'len'=>8,	    'dft'=>'',        'unq'=>1, 'cmt'=>'索引ID'],
		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',                  'cmt'=>'标签名'],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',    'idx'=>1, 'cmt'=>'创建人ID'],
		['name'=>'type',          'type'=>'varchar',  'len'=>16,	'dft'=>'NULL',              'cmt'=>'类型 '], 
		['name'=>'seats',         'type'=>'text',     'len'=>-1,    'dft'=>'',                  'cmt'=>'座位 '],
		// 座位数据结构
		// JSON Array 
		// [{row:[{col:col,status:status},{col:col,status:status}...]},{}...]  status on,off,used
		// eg: [{"一":[{"col":"A","status":"on"},{"col":"B","status":"on"}]},{"二":[{"col":"A","status":"off"},{"col":"B","status":"used"}]}]

	],


	// 活动场次
	//
	'activeround'=>
	[
		['table'=>'活动场次'],

		['name'=>'activeroundid', 'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,  'cmt'=>'场次ID' ],
		['name'=>'activeid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'idx'=>1,  'cmt'=>'活动ID' ],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'创建人ID' ],
		['name'=>'seats',         'type'=>'text',     'len'=>-1,    'dft'=>'NULL',             'cmt'=>'座位数' ],
		['name'=>'max',           'type'=>'int',      'len'=>6,	    'dft'=>0,                  'cmt'=>'最大数' ],
		['name'=>'total',         'type'=>'int',      'len'=>6,	    'dft'=>0,                  'cmt'=>'预定数' ],
		// 座位数据结构
		// JSON Array 
		// [{row:[{col:col,status:status},{col:col,status:status}...]},{}...]  status on,off,used
		// eg: [{"一":[{"col":"A","status":"on"},{"col":"B","status":"on"}]},{"二":[{"col":"A","status":"off"},{"col":"B","status":"used"}]}]
		// {"一":{"A":"on","B":"on"},"二":{"A":"off","B":"used"}}
		
		['name'=>'date',          'type'=>'bigint',   'len'=>13,    'dft'=>'',                 'cmt'=>'活动日期' ],
		['name'=>'starttime',     'type'=>'varchar',  'len'=>8,     'dft'=>'NULL',             'cmt'=>'开始时间' ],
		['name'=>'endtime',       'type'=>'varchar',  'len'=>8,     'dft'=>'NULL',             'cmt'=>'结束时间' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',          'cmt'=>'状态 ' ],
		// 场次状态
		// expired 过期  offline 下线  

	],



	// 资讯
	'article'=>
	[
		['table'=>'资讯'],

		['name'=>'articleid',     'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1, 'cmt'=>'索引ID' ],
		['name'=>'categoryid',    'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'分类ID' ],
        ['name'=>'type',          'type'=>'varchar',  'len'=>16,	'dft'=>'NULL',            'cmt'=>'类型 text,cover,video,gallery...' ],
        ['name'=>'mode',          'type'=>'varchar',  'len'=>16,	'dft'=>'NULL',            'cmt'=>'模式' ],
		['name'=>'regionid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'地域ID' ],
		['name'=>'areaid',        'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'地区ID' ],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'创建人ID' ],

		//基础字段
		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',      'ngr'=>1, 'cmt'=>'名称名' ],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',            'cmt'=>'封面' ],
		['name'=>'gallery',       'type'=>'text',     'len'=>-1,	'dft'=>'NULL',            'cmt'=>'详情介绍' ],
		['name'=>'attachments',   'type'=>'text',     'len'=>-1,	'dft'=>'NULL',            'cmt'=>'附件' ],
		['name'=>'video',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',            'cmt'=>'缩略图' ],
		['name'=>'link',          'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',            'cmt'=>'链接' ],
		['name'=>'tags',          'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',            'cmt'=>'标签 最高255' ],

		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',  'ngr'=>1, 'cmt'=>'描述' ],
		['name'=>'introduce',     'type'=>'text',     'len'=>-1,	'dft'=>'NULL',            'cmt'=>'详情介绍' ],

		['name'=>'viewtimes',     'type'=>'bigint',   'len'=>13,    'dft'=>0,              'cmt'=>'播放次数' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',         'cmt'=>'状态 ' ],

	],


	// 团队
	'community'=>
	[
		['table'=>'社团/团队'],

		['name'=>'communityid',   'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,  'cmt'=>'唯一ID'],
		['name'=>'categoryid',    'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'分类ID'],
		['name'=>'type',          'type'=>'varchar',  'len'=>12,	'dft'=>'NULL',             'cmt'=>'类型'],
		['name'=>'areaid',        'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'地区ID'],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'创建人ID'],
		['name'=>'leaderid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'领导人ID'],
		['name'=>'avatar',        'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',             'cmt'=>'头像 url'],

		//基础字段
		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',      'ngr'=>1,  'cmt'=>'名称名'],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',             'cmt'=>'封面'],
		['name'=>'gallery',       'type'=>'text',     'len'=>-1,	'dft'=>'NULL',             'cmt'=>'详情介绍'],
		['name'=>'videos',        'type'=>'text',     'len'=>-1,    'dft'=>'NULL',             'cmt'=>'缩略图'],
		['name'=>'tags',          'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',             'cmt'=>'标签 最高255'],

		['name'=>'member',        'type'=>'bigint',      'len'=>13,    'dft'=>0,               'cmt'=>'成员人数'],
		['name'=>'follower',      'type'=>'bigint',      'len'=>13,    'dft'=>0,               'cmt'=>'关注人数'],

		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',  'ngr'=>1,  'cmt'=>'描述'],
		['name'=>'introduce',     'type'=>'text',     'len'=>-1,	'dft'=>'NULL',             'cmt'=>'详情介绍'],

		['name'=>'viewtimes',     'type'=>'bigint',      'len'=>13,    'dft'=>0,               'cmt'=>'播放次数'],
		['name'=>'flower',        'type'=>'bigint',      'len'=>13,    'dft'=>0,               'cmt'=>'点赞次数/ 浇花'],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',          'cmt'=>'状态 '],

	],

	// 社团成员
	'communitymember'=>
	[
		['table'=>'社团成员'],

		['name'=>'communitymemberid', 'type'=>'varchar',  'len'=>8,   'dft'=>'',    'unq'=>1,  'cmt'=>'成员ID' ],
		['name'=>'communityid',   'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'社团ID' ],
		['name'=>'userid',        'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'用户ID' ],
		['name'=>'name',          'type'=>'varchar',  'len'=>32,	'dft'=>'',      'ngr'=>1,  'cmt'=>'成员名称' ],
		['name'=>'avatar',        'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',             'cmt'=>'头像' ],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',             'cmt'=>'封面' ],
		['name'=>'role',          'type'=>'varchar',  'len'=>24,    'dft'=>'member',           'cmt'=>'角色 ' ],
		['name'=>'introduce',     'type'=>'text',     'len'=>-1,	'dft'=>'NULL',             'cmt'=>'详情介绍' ],
		['name'=>'age',           'type'=>'int',      'len'=>3,  	'dft'=>'',                 'cmt'=>'年龄' ],
		['name'=>'gender',        'type'=>'tinyint',  'len'=>1,	    'dft'=>0,                  'cmt'=>'性别' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',          'cmt'=>'状态 ' ],

	],

	// 问答
	'vote'=>
	[

		['name'=>'voteid',       'type'=>'varchar',  'len'=>8,	    'dft'=>'',   'unq'=>1,    'cmt'=>'问题ID' ],
		['name'=>'areaid',       'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'地区ID' ],
		['name'=>'authorid',     'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'创建人ID' ],
		['name'=>'title',        'type'=>'varchar',  'len'=>255,	'dft'=>'',      'ngr'=>1, 'cmt'=>'题目' ],
		['name'=>'type',  'type'=>'varchar',  'len'=>32,	'dft'=>'NULL',            'cmt'=>'类型 radio单选 ,textarea段落 ,input单行' ],
		['name'=>'placeholder',   'type'=>'varchar',  'len'=>127,	'dft'=>'NULL',            'cmt'=>'提示 60字以内' ],

		['name'=>'options',       'type'=>'text',     'len'=>-1,    'dft'=>'NULL',            'cmt'=>'如是选择题 则 k-v json' ],
		// eg: "{'a':'第一个选项','b':'第二个选项','c':'第三个选项',...}"

		['name'=>'viewtimes',     'type'=>'bigint',      'len'=>13,    'dft'=>0,               'cmt'=>'播放次数'],
		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',         'cmt'=>'状态 enabled 开启, disabled 关闭' ],

	],


	// banner设置
	'banner' =>
	[
		['name'=>'bannerid',      'type'=>'varchar',  'len'=>8,	'dft'=>'',   'unq'=>1,      'cmt'=>'轮播图ID' ], 
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',          'cmt'=>'封面 大图' ], 
		['name'=>'position',      'type'=>'varchar',  'len'=>64,	'dft'=>'NULL',          'cmt'=>'banner位置' ], 
		['name'=>'title',         'type'=>'varchar',  'len'=>64,	'dft'=>'',   'ngr'=>1,  'cmt'=>'标题32字以内 分词' ], 
		['name'=>'link',          'type'=>'varchar',  'len'=>255,   'dft'=>'',              'cmt'=>'链接' ], 
		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',       'cmt'=>'状态 enabled 开启, disabled 关闭' ], 

		['name'=>'clicktimes',     'type'=>'int',     'len'=>16,    'dft'=>0,               'cmt'=>'点击次数' ], 
		['name'=>'viewtimes',     'type'=>'bigint',   'len'=>13,    'dft'=>0,               'cmt'=>'播放次数'],

	],

	// 页面
	'page' =>
	[

		['name'=>'title',         'type'=>'varchar',  'len'=>16,	'dft'=>'',       'unq'=>1, 'cmt'=>'轮播图ID' ], 
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',             'cmt'=>'封面 大图' ], 
		['name'=>'introduce',     'type'=>'text',     'len'=>-1,    'dft'=>'NULL',   'ngr'=>1, 'cmt'=>'详情 不限字数 分词' ], 

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',          'cmt'=>'状态 enabled 开启, disabled 关闭' ], 

		['name'=>'viewtimes',     'type'=>'int',      'len'=>16,    'dft'=>0,                  'cmt'=>'点击次数' ], 

	],

	// 通用标签
	'tag'=>
	[
		['table'=>'通用标签'],

		['name'=>'tagid',         'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1,  'cmt'=>'标签ID' ],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'创建人ID' ],

		['name'=>'type',          'type'=>'varchar',  'len'=>16,	'dft'=>'NULL',             'cmt'=>'添加type时 即为特定类型下的标签' ],
		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',                 'cmt'=>'标签名' ],
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',             'cmt'=>'封面' ],
		['name'=>'description',   'type'=>'varchar',  'len'=>255,	'dft'=>'NULL',             'cmt'=>'描述' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',          'cmt'=>'状态 ' ],

	],



	// 邮件
	'mail' =>
	[
		['name'=>'mailid',        'type'=>'varchar',  'len'=>8,	'dft'=>'',  'unq'=>1, 'cmt'=>'唯一id' ],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,	'dft'=>'',  'idx'=>1, 'cmt'=>'作者id' ],
		['name'=>'categoryid',    'type'=>'varchar',  'len'=>8,	'dft'=>'NULL',        'cmt'=>'分类id' ],
		
		['name'=>'subject',       'type'=>'text',     'len'=>-1,	'dft'=>'',        'cmt'=>'标题' ],
		['name'=>'content',       'type'=>'text',     'len'=>-1,	'dft'=>'',        'cmt'=>'内容' ],
		['name'=>'attachment',    'type'=>'text',     'len'=>-1,	'dft'=>'NULL',    'cmt'=>'附件' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,	'dft'=>'enabled', 'cmt'=>'状态' ],

		['name'=>'password',      'type'=>'varchar',  'len'=>255,  'dft'=>'NULL',    'cmt'=>'密码访问' ],
		// 密码访问
	],

	// 短信
	'sms' =>
	[
		['name'=>'smsid',         'type'=>'varchar',  'len'=>8,	'dft'=>'',  'unq'=>1,        'cmt'=>'唯一id' ],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,	'dft'=>'NULL',               'cmt'=>'作者id' ],
		['name'=>'categoryid',    'type'=>'varchar',  'len'=>8,	'dft'=>'NULL',               'cmt'=>'分类id' ],
		
		['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',           'cmt'=>'封面' ],
		['name'=>'content',       'type'=>'text',     'len'=>-1,	'dft'=>'NULL', 'ngr'=>1, 'cmt'=>'内容' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,	'dft'=>'enabled',        'cmt'=>'状态' ],

		['name'=>'password',      'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',           'cmt'=>'密码访问' ],
		// 密码访问
	],


],


'relation' =>
[

	// 关联 单位与单位之间的关联
	// item单位为主 即 1对多  ->  多对多
	// 将关联类型表join 即可获得关系集合
	// 

	'combine'  => 
	[
		['table'=>'关系绑定'],

		['name'=>'combineid',      'type'=>'varchar',  'len'=>8,	'dft'=>'',     'unq'=>1, 'cmt'=>'主ID' ],
		['name'=>'itemid',         'type'=>'varchar',  'len'=>8,	'dft'=>'',     'idx'=>1, 'cmt'=>'单位ID' ],
		['name'=>'itemtype',       'type'=>'varchar',  'len'=>24,	'dft'=>'',     'idx'=>1, 'cmt'=>'单位类型' ],
		['name'=>'relationid',     'type'=>'varchar',  'len'=>8,	'dft'=>'',     'idx'=>1, 'cmt'=>'关联对象ID' ],
		['name'=>'relationtype',   'type'=>'varchar',  'len'=>24,	'dft'=>'',     'idx'=>1, 'cmt'=>'关联对象类型' ],
		['name'=>'type',           'type'=>'varchar',  'len'=>16,	'dft'=>'NULL', 'idx'=>1, 'cmt'=>'类型' ], 
		['name'=>'rate',           'type'=>'int',      'len'=>3,	'dft'=>0,                'cmt'=>'强度' ],
		// like type eg: superlike 5  like 1  normal 0 dislike -1  hate -5
		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',        'cmt'=>'状态' ],
	],

],



// 全部表单项目
'form' => [

	'request'=>
	[
		['table'=>'申请表单'],

		['name'=>'requestid',  'type'=>'varchar',  'len'=>8,	'dft'=>'',   'unq'=>1,  'cmt'=>'索引ID' ],

		['name'=>'areaid',     'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1, 'cmt'=>'区域ID' ],
		['name'=>'userid',     'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1, 'cmt'=>'用户ID' ],
		['name'=>'itemid',     'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1, 'cmt'=>'目标ID' ],
		['name'=>'itemtype',   'type'=>'varchar',  'len'=>32,	'dft'=>'NULL','idx'=>1, 'cmt'=>'目标类别' ],

		['name'=>'open',       'type'=>'tinyint',  'len'=>1,     'dft'=>0,               'cmt'=>'是否公开' ],
		['name'=>'form',       'type'=>'text',     'len'=>-1,    'dft'=>'NULL',          'cmt'=>'表单内容 JSON 不限制' ],
		['name'=>'status',     'type'=>'varchar',  'len'=>12,    'dft'=>'pending',       'cmt'=>'状态 enabled 开启, disabled 关闭, rejected 被拒绝, pending 等待中, applied 通过' ],

		['name'=>'expire',     'type'=>'bigint',      'len'=>11,   'dft'=>0,          'cmt'=>'过期时间 时间戳' ],
		['name'=>'applycall',  'type'=>'text',     'len'=>-1,   'dft'=>'NULL',        'cmt'=>'通过回调 k-v json' ],
		['name'=>'rejectcall', 'type'=>'text',     'len'=>-1,   'dft'=>'NULL',        'cmt'=>'退款回调' ],
		// 回调函数最外层必须是 queue数组 [{},{},{},{}] or [{}] 
		// eg: {{'action':'setvip','params':{'userid':'xxx','expire':158989990}},{'action':'newPromotion','params':{'userid':'xxx','amount':8.88}}
		// 通过回调函数激活支付成功后进行的事件
		
	],

	'verify'=>
	[
		['table'=>'认证表单'],

		['name'=>'verifyid',  'type'=>'varchar',  'len'=>8,	'dft'=>'',   'unq'=>1,       'cmt'=>'索引ID' ],

		['name'=>'areaid',     'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1,  'cmt'=>'区域ID' ],
		['name'=>'userid',     'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1,  'cmt'=>'用户ID' ],
		['name'=>'itemid',     'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1,  'cmt'=>'目标ID' ],
		['name'=>'itemtype',   'type'=>'varchar',  'len'=>32,	'dft'=>'NULL','idx'=>1,  'cmt'=>'目标类别' ],

		['name'=>'title',      'type'=>'varchar',  'len'=>64,	 'dft'=>'',               'cmt'=>'名称' ],
		['name'=>'description','type'=>'varchar',  'len'=>256,	 'dft'=>'NULL',           'cmt'=>'描述' ],
		['name'=>'cover',      'type'=>'varchar',  'len'=>256,   'dft'=>'NULL',           'cmt'=>'封面' ],
		['name'=>'attachments','type'=>'text',     'len'=>-1,	 'dft'=>'NULL',           'cmt'=>'附件'],
		['name'=>'expire',     'type'=>'bigint',   'len'=>11,    'dft'=>0,                'cmt'=>'过期时间 时间戳' ],
		['name'=>'status',     'type'=>'varchar',  'len'=>12,    'dft'=>'pending',        'cmt'=>'状态' ],
	],


	'contract'=>
	[
		['table'=>'合约表单'],

		['name'=>'contractid', 'type'=>'varchar',  'len'=>8,	'dft'=>'',   'unq'=>1,    'cmt'=>'索引ID' ],
		['name'=>'userid',     'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1,   'cmt'=>'用户ID' ],
		['name'=>'targetid',   'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1,   'cmt'=>'合约方ID' ],
		['name'=>'itemid',     'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1,   'cmt'=>'目标ID' ],
		['name'=>'itemtype',   'type'=>'varchar',  'len'=>12,	'dft'=>'NULL','idx'=>1,   'cmt'=>'目标类别' ],

		['name'=>'title',      'type'=>'varchar',  'len'=>64,	'dft'=>'',                'cmt'=>'名称' ],
		['name'=>'description','type'=>'varchar',  'len'=>256,	'dft'=>'NULL',            'cmt'=>'描述' ],
		['name'=>'cover',      'type'=>'varchar',  'len'=>256,   'dft'=>'NULL',           'cmt'=>'封面' ],
		['name'=>'terms',      'type'=>'text',     'len'=>-1,	'dft'=>'NULL',            'cmt'=>'条款'],
		['name'=>'information','type'=>'text',     'len'=>-1,	'dft'=>'NULL',            'cmt'=>'备注信息'],
		['name'=>'attachments','type'=>'text',     'len'=>-1,	'dft'=>'NULL',            'cmt'=>'附件(云存储链接列表)'],

		['name'=>'signa',      'type'=>'varchar',  'len'=>64,	'dft'=>'NULL',            'cmt'=>'签章A' ],
		['name'=>'signb',      'type'=>'varchar',  'len'=>64,	'dft'=>'NULL',            'cmt'=>'签章B' ],

		['name'=>'price',      'type'=>'decimal',  'len'=>'12,2','dft'=>0,       'cmt'=>'价格' ],
		['name'=>'paytype',    'type'=>'varchar',  'len'=>12,	'dft'=>'NULL',   'cmt'=>'支付类型( once一次性,loop循环,custom自定义 )' ],
		['name'=>'payduration','type'=>'varchar',  'len'=>12,	'dft'=>'NULL',   'cmt'=>'支付周期( day天,month月,quarter季度,year年 )' ],
		['name'=>'custompay',  'type'=>'varchar',  'len'=>256,	'dft'=>'NULL',   'cmt'=>'自定义支付方式' ],
		['name'=>'payoffset',  'type'=>'tinyint',  'len'=> 3,    'dft'=>0,       'cmt'=>'支付偏移量(偏移量 按天左右偏移)' ],
		['name'=>'payments',   'type'=>'text',     'len'=>-1,	'dft'=>'NULL',   'cmt'=>'支付信息'],

		['name'=>'starttime',  'type'=>'bigint',   'len'=>11,   'dft'=>0,     'cmt'=>'开始时间'  ], 
		['name'=>'endtime',    'type'=>'bigint',   'len'=>11,   'dft'=>0,     'cmt'=>'结束时间'  ], 

		['name'=>'status',     'type'=>'varchar',  'len'=>12,    'dft'=>'pending', 'cmt'=>'状态' ],

	]

],

// 区块链
'innerblock' => [

	'chain'=>
	[
		['table'=>'内区块链'],

		['name'=>'blockid',    'type'=>'varchar',  'len'=>8,	'dft'=>'',   'unq'=>1,    'cmt'=>'索引ID' ],
		['name'=>'userid',     'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1,  'cmt'=>'用户ID' ],
		['name'=>'itemtype',   'type'=>'varchar',  'len'=>32,	'dft'=>'NULL','idx'=>1,  'cmt'=>'目标类别' ],
		['name'=>'itemid',     'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1, 'cmt'=>'目标ID' ],
		['name'=>'saasid',     'type'=>'varchar',  'len'=>8,	'dft'=>'NULL','idx'=>1,  'cmt'=>'主体ID' ],

		['name'=>'content',    'type'=>'text',     'len'=>-1,   'dft'=>'NULL',        'cmt'=>'数据内容(index,hash,data,timestamp) ASJson' ],

		['name'=>'hash',       'type'=>'varchar',  'len'=>64,    'dft'=>'pending','idx'=>1, 'cmt'=>'哈希值 SHA256' ],
		['name'=>'status',     'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',          'cmt'=>'状态 ' ],
	],

],


// **************************************************
// 
// COMMERCE
// 电商

// 订单??
'commerce' => [

	'order' =>
	[
		['table'=>'订单'],

		['name'=>'orderid',       'type'=>'varchar',  'len'=>32,	'dft'=>'',     'unq'=>1,  'cmt'=>'订单ID' ],
		['name'=>'userid',        'type'=>'varchar',  'len'=>8,	    'dft'=>'',     'idx'=>1,  'cmt'=>'用户ID' ],
		['name'=>'areaid',        'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'地区ID' ],

		['name'=>'title',          'type'=>'varchar',  'len'=>127,	'dft'=>'',     'ngr'=>1,  'cmt'=>'标题 60字以内 分词' ],
		['name'=>'cover',          'type'=>'varchar',  'len'=>255,  'dft'=>'NULL',            'cmt'=>'缩略图 小图' ],

	    ['name'=>'amount',         'type'=>'decimal',  'len'=>'12,2','dft'=>0,                 'cmt'=>'总价 精度0.01元' ],
	    ['name'=>'itemid',         'type'=>'varchar',  'len'=>8,    'dft'=>'NULL', 'idx'=>1,  'cmt'=>'商品ID' ],
	    ['name'=>'itemtype',       'type'=>'varchar',  'len'=>32,   'dft'=>'',                'cmt'=>'商品类型' ],
		['name'=>'quantity',       'type'=>'int',      'len'=>8,    'dft'=>1,                 'cmt'=>'数量' ],

		['name'=>'payment',        'type'=>'varchar',  'len'=>64,   'dft'=>'NULL',            'cmt'=>'支付方式 如 wechat-jsapi' ],
		['name'=>'paymentid',      'type'=>'varchar',  'len'=>8,    'dft'=>'NULL',             'cmt'=>'支付方式 如 wechat-jsapi' ],
		['name'=>'promoterid',     'type'=>'varchar',  'len'=>8,    'dft'=>'NULL',  'idx'=>1,  'cmt'=>'推广人ID ' ],
		['name'=>'freeorder',      'type'=>'tinyint',  'len'=>1,    'dft'=>0,                 'cmt'=>'免支付订单' ],

		['name'=>'details',        'type'=>'text',     'len'=>-1,   'dft'=>'NULL',            'cmt'=>'详情记录' ],
		['name'=>'status',         'type'=>'varchar',  'len'=>12,	'dft'=>'needpay',         'cmt'=>'状态' ],
		// eg: needpay待支付, pending等待中, shipping物流中, precancel取消中, needconfirm待审核, done已完成, error错误, closed已关闭, canceled已取消
		['name'=>'writeoff',       'type'=>'tinyint',  'len'=>1,	'dft'=>0,                  'cmt'=>'是否核销' ],

		['name'=>'ticket',         'type'=>'varchar',  'len'=>8,    'dft'=>'NULL',             'cmt'=>'票号 // 取票码' ],
		['name'=>'idnumber',       'type'=>'varchar',  'len'=>32,   'dft'=>'NULL',  'idx'=>1,  'cmt'=>'身份证取票 // 取票码' ],

		['name'=>'expire',         'type'=>'bigint',      'len'=>11,   'dft'=>0,                 'cmt'=>'过期时间 时间戳' ],

		['name'=>'refundexpire',   'type'=>'bigint',      'len'=>11,   'dft'=>0,                 'cmt'=>'退款有效期' ],
		['name'=>'refundrequesttime','type'=>'bigint',    'len'=>11,   'dft'=>0,                 'cmt'=>'退款申请日期' ],
		['name'=>'refundmessage',  'type'=>'varchar',  'len'=>511,  'dft'=>'NULL',            'cmt'=>'退款申请留言 250字以内' ],

		['name'=>'callback',       'type'=>'text',     'len'=>-1,   'dft'=>'NULL',            'cmt'=>'订单成功回调 k-v json' ],
		['name'=>'refundcallback', 'type'=>'text',     'len'=>-1,  'dft'=>'NULL',             'cmt'=>'退款回调' ],
		['name'=>'breachcallback', 'type'=>'text',     'len'=>-1,  'dft'=>'NULL',             'cmt'=>'惩罚回调' ],
		// 回调函数最外层必须是 queue数组 [{},{},{},{}] or [{}] 
		// eg: {{'action':'setvip','params':{'userid':'xxx','expire':158989990}},{'action':'newPromotion','params':{'userid':'xxx','amount':8.88}}
		// 通过回调函数激活支付成功后进行的事件

	],

	'payment' =>
	[
		['table'=>'支付'],

		['name'=>'paymentid',      'type'=>'varchar',  'len'=>8,   'dft'=>'NULL',  'unq'=>1, 'cmt'=>'支付ID',  ],
		['name'=>'orderid',        'type'=>'varchar',  'len'=>32,   'dft'=>'NULL',  'idx'=>1, 'cmt'=>'订单ID',  ],
		['name'=>'payment',        'type'=>'varchar',  'len'=>16,   'dft'=>'NULL',            'cmt'=>'支付方式 如 wechat alipay...',  ],
		['name'=>'paymenttype',    'type'=>'varchar',  'len'=>16,   'dft'=>'NULL',            'cmt'=>'支付方式 如 jsapi qrcode h5 web ...',  ],

		['name'=>'paymenttradeno', 'type'=>'varchar',  'len'=>32,   'dft'=>'NULL',            'cmt'=>'支付流水号 ',  ],
		// ['name'=>'itemid',         'type'=>'varchar',  'len'=>32,   'dft'=>'NULL',            'cmt'=>'商品ID',  ],

		['name'=>'amount',         'type'=>'decimal',   'len'=>'12,2',   'dft'=>0,                 'cmt'=>'总价 精度0.01元',  ],

		['name'=>'status',         'type'=>'varchar',  'len'=>12,	'dft'=>'paid',            'cmt'=>'状态',  ],
		// eg: needpay待付款, waiting待确认, done已完成, error错误, closed已关闭, canceled已取消
		['name'=>'expire',         'type'=>'double',   'len'=>-1,   'dft'=>0,                 'cmt'=>'支付过期时间 时间戳',  ],

		['name'=>'details',        'type'=>'text',     'len'=>-1,   'dft'=>'NULL',              'cmt'=>'详情记录',  ],

		// 和 order callback不同 支付通过paymentid的回调 让支付可以顺利获取自身数据即可,后续操作根据 orderid获取 function callback

	],

    'shipping'=>
        [
            ['table'=>'物流'],

            ['name'=>'shippingid',     'type'=>'varchar',  'len'=>8,   'dft'=>'',      'unq'=>1, 'cmt'=>'索引ID',  ],
            ['name'=>'orderid',        'type'=>'varchar',  'len'=>32,  'dft'=>'',      'idx'=>1, 'cmt'=>'订单ID',  ],
            ['name'=>'userid',         'type'=>'varchar',  'len'=>8,   'dft'=>'NULL',  'idx'=>1, 'cmt'=>'用户ID',  ],
            ['name'=>'details',        'type'=>'text',     'len'=>-1,   'dft'=>'NULL',              'cmt'=>'详情记录 ASJson',  ],

            ['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',         'cmt'=>'状态  trash 垃圾桶'  ],

        ],

    'writeoff' =>
        [

            ['table'=>'核销'],

            ['name'=>'writeoffid',     'type'=>'varchar',  'len'=>8,   'dft'=>'',      'unq'=>1, 'cmt'=>'支付ID',  ],
            ['name'=>'orderid',        'type'=>'varchar',  'len'=>32,  'dft'=>'',      'idx'=>1, 'cmt'=>'订单ID',  ],
            ['name'=>'itemid',         'type'=>'varchar',  'len'=>32,  'dft'=>'NULL',  'idx'=>1, 'cmt'=>'对象ID',  ],
            ['name'=>'targetid',       'type'=>'varchar',  'len'=>8,   'dft'=>'NULL',  'idx'=>1, 'cmt'=>'核销绑定ID',  ],
            ['name'=>'userid',         'type'=>'varchar',  'len'=>8,   'dft'=>'NULL',  'idx'=>1, 'cmt'=>'用户ID',  ],

            ['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',         'cmt'=>'状态  trash 垃圾桶'  ],

        ],


],


//  MESSAGE
//  消息系统

'message'=>[
    'announcement' =>
    [

        ['table'=>'公告'],

        ['name'=>'announcementid', 'type'=>'varchar', 'len'=>8,   'dft'=>'',   'unq'=>1,  'cmt'=>'公告ID' ],
        ['name'=>'authorid',       'type'=>'varchar', 'len'=>8,   'dft'=>'',   'idx'=>1,  'cmt'=>'作者ID' ],
        ['name'=>'status',         'type'=>'varchar', 'len'=>24,   'dft'=>'send',         'cmt'=>'状态' ],
        // eg: sent 已发送 received 已接收 read 已读

        ['name'=>'title',         'type'=>'varchar',  'len'=>64,	'dft'=>'',            'cmt'=>'名称' ],
        ['name'=>'content',       'type'=>'text',     'len'=>-1,    'dft'=>'NULL',        'cmt'=>'消息内容' ],
        ['name'=>'cover',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',        'cmt'=>'封面' ],
        ['name'=>'gallery',       'type'=>'text',     'len'=>-1,	'dft'=>'NULL',        'cmt'=>'详情介绍' ],
        ['name'=>'attachments',   'type'=>'text',     'len'=>-1,	'dft'=>'NULL',        'cmt'=>'附件' ],
        ['name'=>'video',         'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',        'cmt'=>'缩略图' ],
        ['name'=>'link',          'type'=>'varchar',  'len'=>255,   'dft'=>'NULL',        'cmt'=>'链接' ],

        ['name'=>'type',           'type'=>'varchar', 'len'=>32,   'dft'=>'NULL',       'cmt'=>'公告类型' ],
    ],

    'notification' =>
	[

		['table'=>'消息通知'],

		['name'=>'notificationid', 'type'=>'varchar', 'len'=>8,   'dft'=>'',   'unq'=>1,  'cmt'=>'通知ID' ],
		['name'=>'senderid',       'type'=>'varchar', 'len'=>8,   'dft'=>'',   'idx'=>1,  'cmt'=>'发送方ID' ],
		['name'=>'receiverid',     'type'=>'varchar', 'len'=>8,   'dft'=>'',   'idx'=>1,  'cmt'=>'接收方ID' ],
		['name'=>'replyid',        'type'=>'varchar', 'len'=>8,   'dft'=>'NULL',          'cmt'=>'回复到ID' ],
		['name'=>'status',         'type'=>'varchar', 'len'=>24,   'dft'=>'sent',         'cmt'=>'状态' ],
		// eg: sent 已发送 received 已接收 read 已读
		['name'=>'content',        'type'=>'varchar', 'len'=>512,  'dft'=>'NULL',         'cmt'=>'消息内容' ],

		['name'=>'type',           'type'=>'varchar', 'len'=>32,   'dft'=>'normal',       'cmt'=>'消息类型' ],
		// eg: normal 普通 image 图片消息 audio 音频消息 article 文章 ...
		['name'=>'link',           'type'=>'varchar', 'len'=>255,  'dft'=>'NULL',         'cmt'=>'消息链接url' ],
		['name'=>'linkparams',     'type'=>'varchar', 'len'=>511,  'dft'=>'NULL',         'cmt'=>'消息链接参数 k-v json' ],
		['name'=>'linktype',       'type'=>'varchar', 'len'=>16,   'dft'=>'NULL',         'cmt'=>'消息链接类型' ],
		// eg: page 页面  website 网页 item 单元 payment支付... 
	],

    'chat'=>[

        ['table'=>'用户聊天'],

        ['name'=>'chatid',         'type'=>'varchar', 'len'=>8,   'dft'=>'',   'unq'=>1,  'cmt'=>'消息ID' ],
        ['name'=>'senderid',       'type'=>'varchar', 'len'=>8,   'dft'=>'',   'idx'=>1,  'cmt'=>'发送方ID' ],
        ['name'=>'receiveid',      'type'=>'varchar', 'len'=>8,   'dft'=>'',   'idx'=>1,  'cmt'=>'接收放ID' ],
        ['name'=>'atuserid',       'type'=>'varchar', 'len'=>8,   'dft'=>'NULL',          'cmt'=>'提示到用户ID' ],
        ['name'=>'status',         'type'=>'varchar', 'len'=>24,   'dft'=>'sent',         'cmt'=>'状态' ],
        // eg: sent 已发送 received 已接收 read 已读
        ['name'=>'content',        'type'=>'varchar', 'len'=>512,  'dft'=>'NULL',         'cmt'=>'消息内容' ],

        ['name'=>'type',           'type'=>'varchar', 'len'=>32,   'dft'=>'normal',       'cmt'=>'消息类型' ],
        // eg: normal 普通 image 图片消息 audio 音频消息 article 文章 ...
        ['name'=>'link',           'type'=>'varchar', 'len'=>255,  'dft'=>'NULL',         'cmt'=>'消息链接url' ],
        ['name'=>'linkparams',     'type'=>'varchar', 'len'=>511,  'dft'=>'NULL',         'cmt'=>'消息链接参数 k-v json' ],
        ['name'=>'linktype',       'type'=>'varchar', 'len'=>16,   'dft'=>'NULL',         'cmt'=>'消息链接类型' ],
    ]
],




// ACCESS 
// 处理访问权限

'access' => [

	'permission' =>
	[
        ['table'=>'单位权限'],

        ['name'=>'permissionid','type'=>'varchar', 'len'=>8,  'dft'=>'',   'unq'=>1,  'cmt'=>'索引ID'],
		['name'=>'userid',    'type'=>'varchar', 'len'=>8,    'dft'=>'',   'idx'=>1,  'cmt'=>'用户ID'],
		['name'=>'itemid',    'type'=>'varchar', 'len'=>8,    'dft'=>'',   'idx'=>1,  'cmt'=>'单品ID'],
	    ['name'=>'itemtype',  'type'=>'varchar', 'len'=>16,   'dft'=>'',              'cmt'=>'单品类型'],
		['name'=>'info',      'type'=>'varchar', 'len'=>512,  'dft'=>'NULL',          'cmt'=>'数据 // 可以携带兑换码等信息 '],
		['name'=>'expire',    'type'=>'bigint',  'len'=>11,   'dft'=>'9999999999',    'cmt'=>'过期时间 默认永久有效'],
		['name'=>'status',    'type'=>'varchar', 'len'=>16,   'dft'=>'enabled',       'cmt'=>'状态 // used已使用'],

	],

	'operation' => 
	[
        ['table'=>'操作权限'],

        ['name'=>'operationid','type'=>'varchar', 'len'=>8,    'dft'=>'',   'unq'=>1,  'cmt'=>'索引ID'],
        ['name'=>'parentid',   'type'=>'varchar', 'len'=>8,    'dft'=>'',   'idx'=>1,  'cmt'=>'父级ID'],
        ['name'=>'mergeparents','type'=>'varchar','len'=>128,  'dft'=>'NULL',          'cmt'=>'合并父级 120字符以内（用于快速迭代查询）JSON' ],
	    ['name'=>'title',      'type'=>'varchar', 'len'=>24,   'dft'=>'',              'cmt'=>'权限名称'],
		['name'=>'description','type'=>'varchar', 'len'=>255,  'dft'=>'NULL',          'cmt'=>'描述 120字以内' ],
        ['name'=>'scope',      'type'=>'varchar', 'len'=>16,   'dft'=>'NULL',          'cmt'=>'权限作用域'],
		['name'=>'status',     'type'=>'varchar', 'len'=>16,   'dft'=>'enabled',       'cmt'=>'状态 // disabled弃用'],
	],

	'token' => 
	[
        ['table'=>'授权令牌'],

		['name'=>'userid',    'type'=>'varchar', 'len'=>8,   'dft'=>'',   'idx'=>1,   'cmt'=>'用户ID'],
		['name'=>'token',     'type'=>'varchar', 'len'=>511,  'dft'=>'',              'cmt'=>'token令牌'],
		['name'=>'scope',     'type'=>'varchar', 'len'=>16,   'dft'=>'',              'cmt'=>'权限作用域'],
		['name'=>'expire',    'type'=>'bigint',  'len'=>11,   'dft'=>0,               'cmt'=>'过期时间 时间戳 必填'],

	],

	'verify' => 
	[
        ['table'=>'授权认证'],

		// ['name'=>'accessid',   'type'=>'varchar', 'len'=>32,	'dft'=>'',   'idx'=>1, 'cmt'=>'权限ID'],
		['name'=>'origin',     'type'=>'varchar', 'len'=>127,	'dft'=>'',   'idx'=>1, 'cmt'=>'来源信息'],
		// 申请认证的来源信息 eg: 18601013441 .. sprite@donsee.cn
		['name'=>'scope',      'type'=>'varchar', 'len'=>16,	'dft'=>'',             'cmt'=>'权限作用域'],
		['name'=>'code',       'type'=>'varchar', 'len'=>511,	'dft'=>'',             'cmt'=>'认证码'],
		['name'=>'expire',     'type'=>'bigint',  'len'=>11,    'dft'=>'',             'cmt'=>'过期时间 时间戳 必填'],

	]
],


// FINANCE
// 财务

// 交易
'finance' => [

	// 交易
	'deal' =>
	[
		['table'=>'交易'],

		['name'=>'dealid',     'type'=>'varchar', 'len'=>8,	    'dft'=>'',   'idx'=>1,     'cmt'=>'交易ID' ],
		['name'=>'payer',      'type'=>'varchar', 'len'=>8,  	'dft'=>'',   'idx'=>1,     'cmt'=>'支付方ID' ],
		['name'=>'payee',      'type'=>'varchar', 'len'=>8,  	'dft'=>'',   'idx'=>1,     'cmt'=>'收款方ID' ],

		['name'=>'type',       'type'=>'varchar', 'len'=>16,    'dft'=>'NULL',             'cmt'=>'交易类型 默认 佣金 ' ],
		// eg: commission 佣金 transmission 转账 bonus 系统奖励...
		['name'=>'pocket',     'type'=>'varchar', 'len'=>24,    'dft'=>'balance', 'idx'=>1, 'cmt'=>'钱包类型 默认 账户余额 ' ],
		// eg: balance 账户余额  point 积分 

		['name'=>'title',      'type'=>'varchar', 'len'=>63,    'dft'=>'',     'ngr'=>1,    'cmt'=>'标题 30字以内' ],
		['name'=>'details',	   'type'=>'text',    'len'=>-1,    'dft'=>'NULL',              'cmt'=>'详情 不限 k-v json' ],
		['name'=>'amount',     'type'=>'decimal', 'len'=>'12,2',	'dft'=>0,                   'cmt'=>'总价 精度0.01元' ],
		['name'=>'status',    'type'=>'varchar',  'len'=>24,    'dft'=>'enabled',           'cmt'=>'状态 等待入账waiting,已使用used' ],

	],


	// 提现
	'withdraw' =>
	[
		['table'=>'提现'],

		['name'=>'withdrawid', 'type'=>'varchar', 'len'=>8,	'dft'=>'',   'unq'=>1,       'cmt'=>'提现申请ID' ],
		['name'=>'userid',     'type'=>'varchar', 'len'=>8,	'dft'=>'',   'idx'=>1,       'cmt'=>'收款方ID' ],

		['name'=>'type',       'type'=>'varchar', 'len'=>16,    'dft'=>'wechat_pocket',  'cmt'=>'提现类型 ' ],
		['name'=>'target',	   'type'=>'varchar', 'len'=>255,   'dft'=>'',               'cmt'=>'详情 不限 k-v json' ],
		['name'=>'amount',     'type'=>'bigint',    'len'=>13,	'dft'=>0,                'cmt'=>'总价 精度0.01元' ],
		['name'=>'status',     'type'=>'varchar', 'len'=>24,	'dft'=>'pending',        'cmt'=>'提现状态' ],
		// pending 待确认 rejected 已拒绝  confirmed 已通过  completed 已完成

		['name'=>'callback',   'type'=>'text',    'len'=>-1,    'dft'=>'NULL',           'cmt'=>'提现成功回调 k-v json' ],
		// eg: {'action':'setvip','params':{'userid':'xxx','expire':158989990}},{'action':'newPromotion','params':{'userid':'xxx','amount':8.88}}

	],
],


// 系统日志监控
'record' => [


	'system' => 
	[
		['table'=>'系统日志'],

		['name'=>'userid',     'type'=>'varchar', 'len'=>8,    'dft'=>'',        'idx'=>1,   'cmt'=>'用户ID' ],
		['name'=>'itemid',     'type'=>'varchar', 'len'=>8,    'dft'=>'NULL',    'idx'=>1,   'cmt'=>'用户ID' ],
		['name'=>'type',     'type'=>'varchar', 'len'=>16,     'dft'=>'success', 'idx'=>1,   'cmt'=>'类型' ],
		['name'=>'status',     'type'=>'varchar', 'len'=>32,    'dft'=>'success',            'cmt'=>'状态' ],
		['name'=>'event',      'type'=>'varchar', 'len'=>64,    'dft'=>'',                   'cmt'=>'事件' ],
		['name'=>'content',    'type'=>'text',    'len'=>-1,    'dft'=>'NULL',               'cmt'=>'内容 k-v json' ],
		['name'=>'sign',       'type'=>'varchar', 'len'=>63,    'dft'=>'NULL',               'cmt'=>'签名' ],
		['name'=>'ip',       'type'=>'varchar', 'len'=>32,    'dft'=>'NULL',                 'cmt'=>'签名' ],
		['name'=>'host',       'type'=>'varchar', 'len'=>32,    'dft'=>'NULL',               'cmt'=>'签名' ],

	],


	'user' => 
	[
		['table'=>'用户日志'],

		['name'=>'userid',     'type'=>'varchar', 'len'=>8,    'dft'=>'',        'idx'=>1,  'cmt'=>'用户ID' ],
		['name'=>'itemid',     'type'=>'varchar', 'len'=>8,    'dft'=>'NULL',    'idx'=>1,  'cmt'=>'用户ID' ],
		['name'=>'type',     'type'=>'varchar', 'len'=>16,     'dft'=>'success', 'idx'=>1,  'cmt'=>'类型' ],
		['name'=>'status',     'type'=>'varchar', 'len'=>32,    'dft'=>'success',           'cmt'=>'状态' ],
		['name'=>'event',      'type'=>'varchar', 'len'=>64,    'dft'=>'',                  'cmt'=>'事件' ],
		['name'=>'content',    'type'=>'text',    'len'=>-1,    'dft'=>'NULL',              'cmt'=>'内容 k-v json' ],
		['name'=>'sign',       'type'=>'varchar', 'len'=>63,    'dft'=>'NULL',              'cmt'=>'签名' ],
		['name'=>'ip',       'type'=>'varchar', 'len'=>32,    'dft'=>'NULL',                'cmt'=>'签名' ],
		['name'=>'host',       'type'=>'varchar', 'len'=>32,    'dft'=>'NULL',              'cmt'=>'签名' ],

	],


	'admin' => 
	[
		['table'=>'管理后台日志'],

		['name'=>'userid',     'type'=>'varchar', 'len'=>8,    'dft'=>'',       'idx'=>1,  'cmt'=>'用户ID' ],
		['name'=>'itemid',     'type'=>'varchar', 'len'=>8,    'dft'=>'NULL',   'idx'=>1,  'cmt'=>'用户ID' ],
		['name'=>'type',     'type'=>'varchar', 'len'=>16,     'dft'=>'success','idx'=>1,  'cmt'=>'类型' ],
		['name'=>'status',     'type'=>'varchar', 'len'=>32,    'dft'=>'success',          'cmt'=>'状态' ],
		['name'=>'event',      'type'=>'varchar', 'len'=>64,    'dft'=>'',                 'cmt'=>'事件' ],
		['name'=>'content',    'type'=>'text',    'len'=>-1,    'dft'=>'NULL',             'cmt'=>'内容 k-v json' ],
		['name'=>'sign',       'type'=>'varchar', 'len'=>63,    'dft'=>'NULL',             'cmt'=>'签名' ],
		['name'=>'ip',       'type'=>'varchar', 'len'=>32,    'dft'=>'NULL',               'cmt'=>'签名' ],
		['name'=>'host',       'type'=>'varchar', 'len'=>32,    'dft'=>'NULL',             'cmt'=>'签名' ],

	],


	'thirdparty' => 
	[
		['table'=>'第三方日志'],

		['name'=>'userid',     'type'=>'varchar', 'len'=>8,    'dft'=>'',     'idx'=>1,    'cmt'=>'用户ID' ],
		['name'=>'itemid',     'type'=>'varchar', 'len'=>8,    'dft'=>'NULL',    'idx'=>1, 'cmt'=>'用户ID' ],
		['name'=>'type',     'type'=>'varchar', 'len'=>16,     'dft'=>'success','idx'=>1,  'cmt'=>'类型' ],
		['name'=>'status',     'type'=>'varchar', 'len'=>32,    'dft'=>'success',          'cmt'=>'状态' ],
		['name'=>'event',      'type'=>'varchar', 'len'=>64,    'dft'=>'',                 'cmt'=>'事件' ],
		['name'=>'content',    'type'=>'text',    'len'=>-1,    'dft'=>'NULL',             'cmt'=>'内容 k-v json' ],
		['name'=>'sign',       'type'=>'varchar', 'len'=>63,    'dft'=>'NULL',             'cmt'=>'签名' ],
		['name'=>'ip',       'type'=>'varchar', 'len'=>32,    'dft'=>'NULL',               'cmt'=>'签名' ],
		['name'=>'host',       'type'=>'varchar', 'len'=>32,    'dft'=>'NULL',             'cmt'=>'签名' ],

	],
],


// SETTING
// 系统

'system' =>[


	'setting' => 
	[
		['table'=>'系统设置'],
		
		['name'=>'settingid',  'type'=>'varchar', 'len'=>32,	 'dft'=>'',   'unq'=>1, 'cmt'=>'设置ID' ],
		['name'=>'keyid',      'type'=>'varchar', 'len'=>32,	 'dft'=>'',   'idx'=>1, 'cmt'=>'查询key' ],
		['name'=>'description','type'=>'varchar', 'len'=>255,    'dft'=>'NULL',         'cmt'=>'描述 120字以内' ],
		['name'=>'content',    'type'=>'text',    'len'=>-1,     'dft'=>'',             'cmt'=>'设置内容 k-v json' ],
		['name'=>'scope',      'type'=>'varchar',  'len'=>16,    'dft'=>'NULL',         'cmt'=>'作用域' ],
		['name'=>'status',     'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',      'cmt'=>'状态 ' ],
	],



	'shieldword'=>
	[
		['table'=>'屏蔽词'],
		
		['name'=>'shieldwordid',  'type'=>'varchar',  'len'=>8,	    'dft'=>'',      'unq'=>1, 'cmt'=>'主ID' ],
		['name'=>'title',         'type'=>'varchar',  'len'=>32,	'dft'=>'',                'cmt'=>'标签名' ],
		['name'=>'authorid',      'type'=>'varchar',  'len'=>8,	    'dft'=>'NULL',  'idx'=>1, 'cmt'=>'创建人ID' ],

		['name'=>'type',          'type'=>'varchar',  'len'=>16,	'dft'=>'NULL',            'cmt'=>'添加type时 即为特定类型下的标签' ],

		['name'=>'status',        'type'=>'varchar',  'len'=>12,    'dft'=>'enabled',         'cmt'=>'状态 ' ],

	],

],


];
