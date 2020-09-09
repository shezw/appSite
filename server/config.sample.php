<?php
/**
 * 配置示例
 * config.sample.php
 */

define("CONFIG",[

    /* 使用数据库模式 */
    'ENABLED_DBMODE'           => {{DB_MODE}},

    'MANAGER_ROUTE_FORMAT'     => 'manager/class/action/id',
    'WEBSITE_ROUTE_FORMAT'     => 'class/action/id',
    'API_ROUTE_FORMAT'         => 'api/namespace/class/id',
    'MAIN_PATH'                => '{{site.path}}',
    'SITE_PATH'                => '{{site.path}}',
    'API_PATH'                 => '{{site.api}}',
    'STATIC_PATH'              => '{{site.static}}',

    /* 引擎信息 */

    'DEFAULT_LANGUAGE'         => 'en-WW' ,
    // 默认语言 Default Language

    /* 服务器信息 后端功能配置 */

    /* MySQL 数据库设置 */

    'DB_BASE'                  => '{{db.base}}' ,
    // 数据库名称 Database name

    'DB_USER'                  => '{{db.user}}' ,
    // MySQL数据库用户名  Database username

    'DB_PASS'                  => '{{db.pass}}' ,
    // MySQL数据库密码 Database password

    'DB_HOST'                  => '{{db.host}}' ,
    // MySQL主机  Database host

    'SQL_MAXSIZE'              => 5000,
    // MySQL最大单次检索步长(条目数) Max size of row LIMIT once request

    'REDIS_HOST'               => '{{redis.host}}',
    'REDIS_PORT'               => {{redis.port}},

    'SERVER_IP'                => '{{server.ip}}',

    /* RESULT状态输出 */

    'ERROR_EXIT'               => 0 ,
    // 错误中断 Exit progress when RESULT is not success
    // 开启后将会在返回result时检测是否成功,并在失败时直接中断程序,建议同时开启ERROR_SHOW进行调试
    // Progress will forced exit when feedback RESULT Obj and it's not successful. Debug with ERROR_SHOW is better.

    'ERROR_SHOW'               => 0 ,
    // 错误信息输出

    'ERROR_REC'                => 0 ,
    // 错误信息进栈  Inset error info to RESULT errorStack
    // 可以监控多条结果返回信息,但是需要实例化RESULT 再通过实例进行结果检测和返回 静态化RESULT无效
    // Record error informations for steps, need to user at Instance of RESULT. Not valid when used by static method.

    /* ACCESS 权限设置 */

    'ACCESS_DB'                => 'AppSite' ,
    // 库名

    'ACCESSTOKEN_SCOPE'        => 'system' ,
    // 全局域

    'ACCESSTOKEN_ID'           => 'Donsee' ,
    // 全局id

    'ACCESSTOKEN_DURATION'     => 7*24*3600 ,
    // 权限有效时长  Default duration of Access Token

    'LOGINTOKEN_DURATION'      => 30*24*3600 ,
    // 登录有效时长 Valid duration on Login Token

    'ACCESSVERIFY_LENGTH'      => 6 ,
    // 验证字长 Length of Verify Code

    'ACCESSVERIFY_INTERVAL'    => 60 ,
    // 验证安全间隔 Limit interval when verify
    // 低于该限制的验证请求将被拒绝 Verify request will be reject when the interval time less this

    'ACCESSVERIFY_VALID'       => 900 ,
    // 验证有效时间 Valid duration once verify

    'VERIFY_ONETIME'           => 1 ,
    // 是否仅支持一次性验证 验证通过时立即删除该验证 Only valid one time on verify

    'AUTO_LOGINTOREGIST'       => 0 ,
    // 是否支持认证登录 无账号自动创建 Can auto regist new user when login user not exist

    'SENDLOGINCODE_USERNOTEXIST'=> 0 ,
    // 用户不存在是是否可以发送登录验证码 Can send verify code ( login scope ) if user not exist


    'SMTP_SERVER'              =>   NULL ,
    'SMTP_PORT'                =>   NULL ,
    'SMTP_USER'                =>   NULL ,
    'SMTP_PASS'                =>   NULL ,

    /* RECORD 后台统计设置  */

    'RECORD_ENABLE'            => 1  ,
    // 是否开启日志记录 Is record enabled

    'RECORD_PRIVACY'           => 0  ,
    // 是否记录用户隐私  Is privacy information record enabled
    // 该功能目前无效 Not supported now
    'RECORD_MODE'              => 'log', # 'sql'
    'LOG_DIR'                  => SERVER_DIR.'/RECLOG/',

    /* 支付 */

    'PAYMENT_VALIDTIME'        => 300 ,
    // 支付有效时间


    /* 订单相关 */

    'ORDER_VALIDTIME'          => 3*24*3600 ,
    // 订单有效时长 Valid duration of an order

    'ORDER_TICKET_VALID_DURATION'=> 60*24*3600,
    // 最长订单查询码有效期

    'ORDER_IDNUMBER_VALID_DURATION'=> 0,
    // 通过身份证查询订单有效期 0为不限制

    /* 推广相关 PROMOTION */

    'PROMOTION_DELAY'          => 7*24*3600 ,
    'PROMOTION_MAXRATE'        => 0.8 ,
    'PROMOTION_ON'             => 1 ,
    'WITHDRAW_MIN'             => 1  ,
    'WITHDRAW_MAX'             => 5000  ,

    'SMS_PROVIDER'             => 'ALIYUNSMS',
    'SMTP_PROVIDER'            => 'ALIYUNSMTP',
    'OSS_PROVIDER'             => 'ALIYUNOSS',

    'WECHAT'=>[

        /** 微信公众平台 Wechat Mp **/

        'WXMP_ID'                  => null ,
        // 公众号ID

        'WXMP_SECRET'              => null ,
        // 公众号Secret

        'WXMP_TOKEN'               => null ,
        // 公众号Token

        'WXMP_EncodingAESKey'      => null ,
        // 公众号AESKey


        /** 微信开放平台 wechat Open **/

        'WXOPEN_ID'                => null ,
        'WXOPEN_SECRET'            => null ,


        /** 微信支付 Wechat Payment **/

        'WXPAY_ID'                 => null ,                    // 商户ID
        'WXPAY_KEY'                => null ,                    // 支付密钥
        'WXPAY_SSLCERT_PATH'       => CERT_DIR.'weixin/' ,    // 支付证书文件
        'WXPAY_SSLKEY_PATH'        => CERT_DIR.'weixin/' ,    // 支付密钥文件

    ],

    'ALIYUN'=>[

        /* 阿里云存储 ALIYUN OSS */

        'OSS_KEYID'         => null ,
        'OSS_KEYSECRET'     => null ,
        'OSS_ENDPOINT'      => null ,
        'OSS_BUCKET'        => null ,

        /* 阿里云短信 ALIYUN SMS */

        'SMS_KEYID'         => null ,
        'SMS_KEYSECRET'     => null ,
        'SMS_MODULE_VERIFY' => null ,
        'SMS_SIGN_VERIFY'   => null ,
        'SMS_MODULE_DM'     => null ,
        'SMS_SIGN_DM'       => null ,

        /* 阿里云邮件 ALIYUN SMTP */

        'SMTP_SERVER'          =>  null ,        // SMTP服务器
        'SMTP_PORT'            =>  80 ,          // 端口 阿里云 建议使用80 本机可以使用25
        'SMTP_REPLY_ACCOUNT'   =>  null,         // 回复邮箱地址
        'SMTP_DM_ACCOUNT'      =>  null ,        // 广告邮箱地址
        'SMTP_DM_PASS'         =>  null ,        // 广告邮箱密码
        'SMTP_SUPPORT_ACCOUNT' =>  null ,	     // 服务邮箱地址
        'SMTP_SUPPORT_PASS'    =>  null ,	     // 服务邮箱密码

    ],

    /* 腾讯邮箱 QQMAIL SMTP */

    'QQSMTP_SERVER'           => 'ssl://smtp.exmail.qq.com' ,  // SMTP服务器
    'QQSMTP_PROT'             => 465 ,  // 端口 腾讯邮箱 建议使用465
    'QQSMTP_DM_ACCOUNT'       => null ,   // 广告邮箱地址
    'QQSMTP_DM_PASS'          => null ,   // 广告邮箱密码
    'QQSMTP_SUPPORT_ACCOUNT'  => null ,   // 服务邮箱地址
    'QQSMTP_SUPPORT_PASS'     => null ,	// 服务邮箱密码
    'QQSMTP_REPLY_ACCOUNT'    => null,    // 回复邮箱地址

    /* 媒体上传管理设置 */

    'MEDIA_HOST_UPLOAD'       => 0,                // 是开启直传服务器
    'MEDIA_UPLOAD_ENGIN'      => 'ALIYUN_OSS' ,    // 外部上传
    'MEDIA_TEMP_DIR'          => ':/tmp/',         // 上传缓存目录
    'MEDIA_POLICY_DURATION'   => 300  ,            // 上传签名有效时长

    'MEDIA_MAX_SIZE'          => 500*MB ,          // 最大视频文件大小
    'MEDIA_MAX_VIDEO_SIZE'    => 500*MB ,          // 最大视频文件大小
    'MEDIA_MAX_AUDIO_SIZE'    => 20*MB ,           // 最大音频文件大小
    'MEDIA_MAX_IMAGE_SIZE'    => 8*MB ,            // 最大图片文件大小
    'MEDIA_MAX_FILE_SIZE'     => 30*MB ,           // 最大文件大小

    /* 上传支持格式配置 Supported file type of upload */

    'MEDIA_VIDEO_TYPE'        => 'mp4,avi,mov,mpg' ,
    'MEDIA_AUDIO_TYPE'        => 'mp3,ogg' ,
    'MEDIA_IMAGE_TYPE'        => 'jpg,jpeg,png,gif' ,
    'MEDIA_FILE_TYPE'         => 'key,pages,numbers,xls,ppt,xlsx,pptx,pdf' ,


    'BAIDUYUN'=>[
        /* 百度AI baidu-ai */

        'ORC_BAIDU_ID' => null,
        'ORC_BAIDU_AK' => null,
        'ORC_BAIDU_SK' => null,
        'access_token' => null,

    ],

    'POINTBONUS_RULES'=>[

        'regist'=>[
            'title'=>'系统奖励','description'=>'注册平台用户成功','value'=>100,'limit'=>1
        ]

    ],

    /* GITEE代码拉取 */

    'gitUsername' => null,
    'gitPassword' => null,
    'gitPath' => null,


/* Backend Config */

    'MANAGER'=>[

        // 后台名称
        'title'=>  'AppSite' ,

        // 简介
        'description' =>  'Cross Platform Engine',

        // ID
        'id'    => '{{uid}}_m',

        'theme' => 'Dashkit',

        // LOGO地址
        'logoUrl' =>  null,
        // 横版logo
        'logoW'  =>  null,
        // 竖版logo
        'logoH'  =>  null,

    ],

/* Website Config */

    'WEBSITE'=>[

        'title'=>'AppSite',

        'id' => '{{uid}}_w',

        'theme'=>'default',

        // LOGO地址
        'logoUrl' =>  null,
        // 横版logo
        'logoW'  =>  null,
        // 竖版logo
        'logoH'  =>  null,

        // 默认头像 完整链接
        'defaultAvatar'=> null,
        'imagePreview' => null,

    ],


/* API Config */


    'SMS_MODULE_CODE'=>[
        'verify'=>null,
        'regist'=>null,
    ]

]);
