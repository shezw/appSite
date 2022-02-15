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

/**
 * 常量注册
 */
require_once "constants.php";

use APS\ASDB;
use APS\ASError;
use APS\ASRecord;
use APS\ASRedis;
use APS\ASRoute;
use APS\ASSetting;
use APS\DBFilter;
use APS\I18n;
use APS\User;

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

        'ImageUtil',      # 图像处理工具
        'Uploader',       # 服务端本地上传

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
        'ASTester',       # 测试基类

    ],

    'service'=>[          /** 业务层 */

        'Access',          # 鉴权模块
        'AccessToken',     # token
        'AccessVerify',    # 鉴权验证
        'AccessPermission',# 归属权鉴权
        'AccessOperation', # 系统功能鉴权

        'User',            # 用户
        'UserAccount',     # 用户\基础账户
        'UserInfo',        # 用户\信息扩展
        'UserPocket',      # 用户\钱包扩展
        'UserCollect',     # 用户\收藏扩展
        'UserComment',     # 用户\评论扩展
        'UserGroup',       # 用户\用户组扩展
        'UserAddress',     # 用户\地址扩展
        'UserPreference',  # 用户\偏好扩展

        'Point',           # 积分管理

        'CommerceOrder',   # 订单
        'CommercePayment', # 支付
        'CommerceCoupon',  # 优惠券
        'CommerceProduct', # 商品
        'CommerceShipping',# 物流
        'CommerceStock',   # 库存
        'CommerceWriteOff',# 核销

        'AnalysisProduct', # 统计-商品

        'FinanceDeal',     # 交易
        'FinanceWithdraw', # 提现

        'Article',         # 文章
        'Category',        # 通用分类
        'Tag',             # 通用标签
        'Page',            # 页面
        'Media',           # 媒体
        'Banner',          # 轮播图
        'MediaTemplate',   # 媒体模板(短信、邮件、页面等)

        'Management',      # 后台管理支持
        'Website',         # 网站前台支持

        'MessageNotification', # 消息
        'MessageAnnouncement', # 消息-公告
        'MessageChat',     # 消息-聊天

        'FormRequest',     # 表单-请求
        'FormContract',    # 表单-合约
        'FormVerify',      # 表单-认证

        'Relation',        # 关联查询


        'UserRecord',      # 用户日志
        'AdminRecord',     # 后台日志
        'ThirdPartyRecord',# 第三方日志

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

        'Saas',            # 平台化
        'Area',            # 地区
        'Company',         # 企业
        'District',        # 商圈
        'Subway',          # 地铁
        'Industry',        # 行业

    ],

    'supporting'=>[         /* 支持类 */

        'WebsiteConstants',

        'DBFilter',        #
        'DBConditions',
        'DBConditionSymbol',
        'DBConditionKeyword',

        'DBValue',
        'DBValues',

        'DBField',
        'DBFields',

        'DBJoinParam',
        'DBJoinParams',

        'DBFieldStruct',
        'DBTableStruct',

        /**
         * 将被弃用
         * Will be Deprecated
         */
        'JoinParams',
        'JoinPrimaryParams',
    ]
]);

/** Alias for Global Objects */

/**
 * 全局数据库访问 _ASDB
 * @param  ASDB|null  $specificDB  指定数据库 更新到全局共享
 * @return ASDB
 */
function _ASDB( APS\ASDB $specificDB = null  ): ASDB{
    return ASDB::shared( $specificDB );
}

/**
 * 全局内存数据库 _ASRedis
 * @return ASRedis
 */
function _ASRedis(): ASRedis{
    return ASRedis::shared();
}

/**
 * 全局日志
 * _ASRecord
 * @return ASRecord
 */
function _ASRecord(): ASRecord{
    return ASRecord::shared();
}

/**
 * 全局错误处理
 * _ASError
 * @return ASError
 */
function _ASError(): ASError{
    return ASError::shared();
}

/**
 * 全局系统设置单例 _Setting
 * @return ASSetting
 */
function _ASSetting(): ASSetting{
    return ASSetting::shared();
}

/**
 * 获取SaasID(存在时)
 * @return string|null
 */
function saasId()
{
    return defined('SaasID') ? SaasID : null;
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
 * @return User
 */
function _User(): User{
    return User::shared();
}

/**
 * 全局本地化单例 _I18n
 * @param  string|null  $lang
 * @return I18n
 */
function _I18n( string $lang = null ): I18n{
    return I18n::shared( $lang );
}

/**
 * 本地化翻译快捷方式
 * Shortcut of I18n->transcoding method
 * @param String $code
 * @param String|null $scope
 * @return String
 */
function i18n( string $code, string $scope = null ):String{
    return _I18n()->transcoding($code,$scope ?? i18n_Common );
}

/**
 * 全局路由单例 _ASRoute
 * @param  string|null  $format
 * @param  string|null  $mode
 * @return ASRoute
 */
function _ASRoute( string $format = null, string $mode = null ): ASRoute
{
    return ASRoute::shared( $format, $mode );
}

/**
 * 快捷方式 DBField
 * @param string $field
 * @param string $mode
 * @return DBFilter
 */
function _DBFilter( string $field, string $mode = DBFilter::AND_MODE ): DBFilter{

    return DBFilter::init( $field, $mode );
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
    $path   = __DIR__ . DIRECTORY_SEPARATOR;
    $file   = str_replace('APS\\', DIRECTORY_SEPARATOR, $classname);
    $folder = checkRegisterFolder($file);
    if (file_exists("{$path}{$folder}{$file}.php")) {
        include "{$path}{$folder}{$file}.php";
        return true;
    }
    return false;
});
