<?php
/**
 * 引擎模块自动加载 Engine autoloader
 *
 * @link https://appsite.cn
 * @author Sprite Shur  https://shezw.com  hello@shezw.com
 * @copyright shezw.com
 * @version 2.0
 */

/**
 * 引擎模块注册
 * Register dictionary
 */
define( 'EngineRegisterDict' , [
    'tool'=>[             /** 工具层 */

        'Time',           # 时间
        'File',           # 文件操作
        'Encrypt',        # 加密/字符处理
        'Filter',         # 数据过滤
        'Network',        # 网络连接 Curl,HTTP
        'I18n',           # 本地化
        'Block',          # 核心区块
        'Mixer',          # 模板\数据混合
    ],
	'core'=>[             /** 核心层 */

        'ASObject',       # 基础通讯结构
        'ASResult',       # 操作结果
        'ASRecord',       # 日志
		'ASDB',           # 数据库交互
        'ASRedis',        # Redis服务器
		'ASBase',         # 基础对象
		'ASModel',        # 数据模型处理
        'ASSetting',      # 系统配置
        'ASError',        # 异常处理
        'ASRoute',        # 系统路由
        'ASAPI',          # 接口基类

	],

	'service'=>[          /** 业务层 */

        'Access',          # 鉴权模块
        'AccessToken',     # token
        'AccessVerify',    # 鉴权验证
        'AccessPermission',# 归属权鉴权
        'AccessOperation', # 系统功能鉴权

        'User',            # 用户
        'UserInfo',        # 用户\信息扩展
        'UserPocket',      # 用户\钱包扩展
        'UserCollect',     # 收藏
        'UserComment',     # 评论
        'UserGroup',       # 用户组

        'CommerceOrder',   # 订单
        'CommercePayment', # 支付

        'FinanceDeal',     # 交易
        'FinanceWithdraw', # 提现

        'Article',         # 文章
        'Category',        # 通用分类
        'Page',            # 页面
        'Media',           # 媒体

        'Management',      # 后台管理支持
        'Website',         # 网站前台支持

        'MessageNotification', # 消息

        'FormRequest',     # 表单-请求
        'FormContract',    # 表单-合约

        'Area',            # 地区

	],

    'extension'=>[         /* 扩展功能 */

        'IBChain',         # 内区块链

        'AliyunOSS',       # 阿里云存储OSS
        'AliyunSMS',       # 阿里云短信

        'BaiduOCR',        # 百度文字识别

        'Wechat',          # 微信
        'SMS',             # 短信
        'SMTP',            # SMTP邮件

        'ShieldWorld',     # 屏蔽词
        'Industry',        # 行业
    ],

    'supporting'=>[

        'JoinParams',
        'JoinPrimaryParams',
        'TimeFormatEnum'

    ]
]);

/** Alias for Global Objects */

/**
 * 全局数据库访问 _ASDB
 * @param  \APS\ASDB|null  $specificDB  指定数据库 更新到全局共享
 * @return \APS\ASDB
 */
function _ASDB( APS\ASDB $specificDB = null  ):\APS\ASDB{
    return \APS\ASDB::shared( $specificDB );
}

/**
 * 全局内存数据库 _ASRedis
 * @return \APS\ASRedis
 */
function _ASRedis():\APS\ASRedis{
    return \APS\ASRedis::shared();
}

/**
 * 全局日志
 * _ASRecord
 * @return \APS\ASRecord
 */
function _ASRecord():\APS\ASRecord{
    return \APS\ASRecord::shared();
}

/**
 * 全局错误处理
 * _ASError
 * @return \APS\ASError
 */
function _ASError():\APS\ASError{
    return \APS\ASError::shared();
}

/**
 * 全局系统设置单例 _Setting
 * @return \APS\ASSetting
 */
function _ASSetting():\APS\ASSetting{
    return \APS\ASSetting::shared();
}

/**
 * 快捷获取配置
 * getConf
 * 如果没有Redis服务同时没有对于后台设置配置项的需求，可以将系统配置写入到config.php中，禁用数据库配置BACKEND_CONFIG_ENABLED用以提高配置项获取的效率。
 * @param  string       $key
 * @param  string|null  $scope
 * @return mixed | null
 */
function getConfig( string $key, string $scope = null ){
    if( defined('USE_DB_CONFIG') && !USE_DB_CONFIG ){
        return isset($scope) ? CONFIG[$scope][$key] : CONFIG[$key];
    }
    return _ASSetting()->getConf($key,$scope);
}

/**
 * 全局用户单例 _User
 * @return \APS\User
 */
function _User():\APS\User{
    return \APS\User::shared();
}

/**
 * 全局本地化单例 _I18n
 * @param  string|null  $lang
 * @return \APS\I18n
 */
function _I18n( string $lang = null ):\APS\I18n{
    return \APS\I18n::shared( $lang );
}

/**
 * 本地化翻译快捷方式
 * Shortcut of I18n->translate method
 * @param String $code
 * @param String|null $scope
 * @return String
 */
function i18n( string $code, string $scope = null ):String{
    return isset($scope) ? _I18n()->transcoding($code,$scope) : _I18n()->translate($code);
}

/**
 * 全局路由单例 _ASRoute
 * @param  string|null  $format
 * @param  string|null  $mode
 * @return \APS\ASRoute
 */
function _ASRoute( string $format = null, string $mode = null ){
    return \APS\ASRoute::shared( $format, $mode );
}


/**
 * 查询组件对应路径
 * checkRegisterFolder
 * @param string $name
 * @return int|string|null
 */
function checkRegisterFolder( string $name ){
	$cleanName = str_replace("/", "", $name);
	foreach (EngineRegisterDict as $folder => $list) {
		if( in_array($cleanName, $list) ){
			return $folder;
		}
	}
	return null;
}

/**
 * 引擎默认类自动加载
 * Basic class autoload ( core, tool, service, supporting, extension )
 */
spl_autoload_register(function ($classname) {
    $pathname   = __DIR__ . DIRECTORY_SEPARATOR;
    $filename   = str_replace('APS\\', DIRECTORY_SEPARATOR, $classname);
    $foldername = checkRegisterFolder($filename);
    if (file_exists("{$pathname}{$foldername}{$filename}.php")) {
	    include "{$pathname}{$foldername}{$filename}.php";
	    return true;
	}
    return false;
});
