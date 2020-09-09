<?php
return [

    'APS\User' => [
        ['userid'=>'9999',  'username'=>'superadmin',  'nickname'=>'超管',    'groupid'=>900, 'status'=>'super'  ],
        ['userid'=>'1000',  'username'=>'administor',  'nickname'=>'管理员',  'groupid'=>800, 'status'=>'enabled' ],
    ],
    'APS\UserGroup'=>[

        ['groupid'=>'0000', 'type'=>'guest',   'level'=>0,     'groupname'=>'游客',      'sort'=>0,   'description'=>'未注册用户或未能获取到用户信息'],

        ['groupid'=>'100',  'type'=>'user',    'level'=>10000, 'groupname'=>'注册用户',   'sort'=>0,   'description'=>'注册成功的用户'],
        ['groupid'=>'300',  'type'=>'manager', 'level'=>30000, 'groupname'=>'撰稿人',     'sort'=>2,   'description'=>'可以在特定板块发布和管理'],
        ['groupid'=>'400',  'type'=>'manager', 'level'=>40000, 'groupname'=>'网站编辑',   'sort'=>3,   'description'=>'可以编辑banner，咨询，反馈等'],

        ['groupid'=>'800',  'type'=>'manager', 'level'=>80000, 'groupname'=>'管理员',    'sort'=>4,    'description'=>'可以管理网站全部内容和设置分销等'],
        ['groupid'=>'900',  'type'=>'super',   'level'=>90000, 'groupname'=>'超级管理员', 'sort'=>999,    'description'=>'可以设置全部内容以及修改网站基础配置'],

        ['groupid'=>'9999', 'type'=>'block',   'level'=>-1,    'groupname'=>'禁止访问',   'sort'=>9999,   'description'=>'因违规或其他原因被禁止访问的账户'],

        ['groupid'=>'3001', 'type'=>'manager', 'level'=>31000, 'parentid'=>'300', 'groupname'=>'普通撰稿人',   'sort'=>100,  'description'=>'普通一些的'],
        ['groupid'=>'3002', 'type'=>'manager', 'level'=>32000, 'parentid'=>'300', 'groupname'=>'高级撰稿人',   'sort'=>200,   'description'=>'经验丰富的'],
        ['groupid'=>'3003', 'type'=>'manager', 'level'=>33000, 'parentid'=>'300', 'groupname'=>'特约撰稿人',   'sort'=>300,   'description'=>'声名远播的'],

    ],

    'APS\Category'=>[

        ['categoryid'=>'media','type'=>'media','title'=>'全部媒体', ],

        ['categoryid'=>'image',   'parentid'=>'media','type'=>'media',  'title'=>'图片' ],
        ['categoryid'=>'video',   'parentid'=>'media','type'=>'media',  'title'=>'视频' ],
        ['categoryid'=>'audio',   'parentid'=>'media','type'=>'media',  'title'=>'音频' ],
        ['categoryid'=>'document','parentid'=>'media','type'=>'media',  'title'=>'文件' ],

    ],
    'APS\Area'=>[

        ['areaid'=>'root','title'=>'系统地区','description'=>'默认地区','level'=>0,'location'=>'0,0' ],

    ],
    'APS\ASSetting'=>[

        /* 后台配置 */
        ['settingid'=>'rbON15n4','keyid'=> 'DEFAULT_LANGUAGE',     'content'=>'en-WW',                   'description'=>'默认语言'],
        ['settingid'=>'MaMrhfCe','keyid'=> 'MANAGER_ROUTE_FORMAT', 'content'=>'manager/class/action/id', 'description'=>'管理后台路由格式'],
        ['settingid'=>'xlJcg985','keyid'=> 'WEBSITE_ROUTE_FORMAT', 'content'=>'class/action/id',         'description'=>'网站前台路由格式'],
        ['settingid'=>'FTTiGJeL','keyid'=> 'API_ROUTE_FORMAT',     'content'=>'api/namespace/class/id',  'description'=>'接口路由格式'],
        ['settingid'=>'W1u4Ed8c','keyid'=> 'MAIN_PATH',            'content'=>'/',                       'description'=>'主路径'],
        ['settingid'=>'cEPEP2ju','keyid'=> 'SITE_PATH',            'content'=>'/',                       'description'=>'网站前台路径'],
        ['settingid'=>'CsNSZHMQ','keyid'=> 'API_PATH',             'content'=>'/api/',                   'description'=>'接口路径'],
        ['settingid'=>'mZ6pOepk','keyid'=> 'STATIC_PATH',          'content'=>'/website/static/',        'description'=>'静态资源路径'],

        ['settingid'=>'mZ6pOepl','keyid'=> 'REDIS_HOST',           'content'=>'127.0.0.1',        'description'=>'Redis Host'],
        ['settingid'=>'mZ6pOepm','keyid'=> 'REDIS_PORT',           'content'=>6379,        'description'=>'Redis Port'],
        ['settingid'=>'mZ6pOepn','keyid'=> 'SERVER_IP',            'content'=>NULL,        'description'=>'服务器ip'],

        ['settingid'=>'xveRYhhv','keyid'=> 'title',          'scope'=>'MANAGER', 'content'=>'管理后台' , 'description'=>'后台名称',],
        ['settingid'=>'z8gIV6vw','keyid'=> 'description',    'scope'=>'MANAGER', 'content'=>'AppSite Back-End Management', 'description'=>'后台简介',],
        ['settingid'=>'KkSKd0Ha','keyid'=> 'id',             'scope'=>'MANAGER', 'content'=>'appsite_m', 'description'=>'后台识别ID 用于本地缓存',],
        ['settingid'=>'KkSKd0HN','keyid'=> 'rootPath',       'scope'=>'MANAGER', 'content'=>'/manager', 'description'=>'自定义根目录,用于隐藏后台地址。需要与Rewrite规则匹配。',],
        ['settingid'=>'c3M0U9us','keyid'=> 'logoUrl',        'scope'=>'MANAGER', 'content'=>'/website/static/appsite/images/logo480.png', 'description'=>'LOGO地址',],
        ['settingid'=>'RPytxxJ4','keyid'=> 'logoW',          'scope'=>'MANAGER', 'content'=>NULL, 'description'=>'横版logo',],
        ['settingid'=>'ceZJtfdS','keyid'=> 'logoH',          'scope'=>'MANAGER', 'content'=>NULL, 'description'=>'竖版logo',],

        ['settingid'=>'g8GWnSxi','keyid'=> 'title',        'scope'=>'WEBSITE', 'content'=>'AppSite' , 'description'=>'网站名称',],
        ['settingid'=>'G0d7NTiK','keyid'=> 'id',           'scope'=>'WEBSITE', 'content'=>'appsite_w', 'description'=>'前台识别ID 用于本地缓存',],
        ['settingid'=>'nCBiLLx7','keyid'=> 'logoUrl',      'scope'=>'WEBSITE', 'content'=>'/website/static/appsite/images/logo480.png', 'description'=>'LOGO地址',],
        ['settingid'=>'yVpZFDd9','keyid'=> 'logoW',        'scope'=>'WEBSITE', 'content'=>NULL, 'description'=>'横版logo',],
        ['settingid'=>'rjFaMzbJ','keyid'=> 'logoH',        'scope'=>'WEBSITE', 'content'=>NULL, 'description'=>'竖版logo',],
        ['settingid'=>'sZUdNGBf','keyid'=> 'defaultAvatar','scope'=>'WEBSITE', 'content'=>'/website/static/appsite/images/avatar.jpg', 'description'=>'默认头像 完整链接'],
        ['settingid'=>'NrR6yLbj','keyid'=> 'imagePreview', 'scope'=>'WEBSITE', 'content'=>NULL, 'description'=> '默认预览 完整链接 '],

        /* 安全防护配置 */

        ['settingid'=>'qr66hr6z','keyid'=>'ALLOWED_IP', 'scope'=>'PRIVATES', 'content'=>[ '127.0.0.1'=>1,] ,'description'=>'IP白名单' ],

        /* 媒体上传管理设置 */

        ['settingid'=>'pCVgXU4P','keyid'=>'MEDIA_HOST_UPLOAD',     'description'=>'是开启直传服务器',  'content'=> 0,],
        ['settingid'=>'WU9wEXgY','keyid'=>'CUSTOM_OSS_DOMAIN',     'description'=>'自定义云存储绑定域名',  'content'=> NULL,],
        ['settingid'=>'SmDyXNwZ','keyid'=>'MEDIA_UPLOAD_ENGINE',   'description'=>'外部上传',  'content'=> 'ALIYUN_OSS' ,],
        ['settingid'=>'Yom0bXxE','keyid'=>'MEDIA_TEMP_DIR',        'description'=>'上传缓存目录',  'content'=> ':/tmp/',],
        ['settingid'=>'aao38zdO','keyid'=>'MEDIA_POLICY_DURATION', 'description'=>'上传签名有效时长',  'content'=> 300  ,],
        ['settingid'=>'KYXvzxRs','keyid'=>'MEDIA_MAX_SIZE',        'description'=>'最大视频文件大小',  'content'=> 500*1024*1024 ,],
        ['settingid'=>'ekwN0u0t','keyid'=>'MEDIA_MAX_VIDEO_SIZE',  'description'=>'最大视频文件大小',  'content'=> 500*1024*1024 ,],
        ['settingid'=>'oXP6fNLU','keyid'=>'MEDIA_MAX_AUDIO_SIZE',  'description'=>'最大音频文件大小',  'content'=> 20*1024*1024 ,],
        ['settingid'=>'Y21ybGRN','keyid'=>'MEDIA_MAX_IMAGE_SIZE',  'description'=>'最大图片文件大小',  'content'=> 8*1024*1024 ,],
        ['settingid'=>'DGICfUwQ','keyid'=>'MEDIA_MAX_FILE_SIZE',   'description'=>'最大文件大小',  'content'=> 30*1024*1024 ,],

        /* 上传支持格式配置 Supported file type of upload */

        ['settingid'=>'moKQsFR6','keyid'=>'MEDIA_VIDEO_TYPE' ,           'description'=>'上传支持视频格式','content'=> 'mp4,avi,mov,mpg' , ],
        ['settingid'=>'VL3ph44v','keyid'=>'MEDIA_AUDIO_TYPE' ,           'description'=>'上传支持音频格式','content'=> 'mp3,ogg' , ],
        ['settingid'=>'e0uDftNN','keyid'=>'MEDIA_IMAGE_TYPE' ,           'description'=>'上传支持图片格式','content'=> 'jpg,jpeg,png,gif' , ],
        ['settingid'=>'Yg19Yep5','keyid'=>'MEDIA_FILE_TYPE' ,            'description'=>'上传支持文件格式','content'=> 'key,pages,numbers,xls,ppt,xlsx,pptx,pdf' , ],

        ['settingid'=>'zFSZJMAm','keyid'=>'ACCESSTOKEN_DURATION' ,       'description'=>'权限有效时长<br>Default duration of Access Token','content'=> 7*24*3600 , ],

        ['settingid'=>'WdxTdYW0','keyid'=>'LOGINTOKEN_DURATION' ,        'description'=>'登录有效时长<br>Valid duration on Login Token','content'=> 30*24*3600 , ],

        ['settingid'=>'wVcHIjPM','keyid'=>'ACCESSVERIFY_LENGTH' ,        'description'=>'验证字长<br>Length of Verify Code','content'=> 6 , ],

        ['settingid'=>'u5hwtau6','keyid'=>'ACCESSVERIFY_INTERVAL' ,      'description'=>'验证安全间隔,低于该限制的验证请求将被拒绝.<br>Limit interval when verify <br>Verify request will be reject when the interval time less this','content'=> 60 , ],

        ['settingid'=>'ZJopQQJc','keyid'=>'ACCESSVERIFY_VALID' ,         'description'=>'验证有效时间<br>Valid duration once verify','content'=> 900 , ],

        ['settingid'=>'wEUidhX0','keyid'=>'VERIFY_ONETIME' ,             'description'=>'是否仅支持一次性验证 验证通过时立即删除该验证<br>Only valid one time on verify','content'=> 1 , ],

        ['settingid'=>'wCirl541','keyid'=>'AUTO_LOGINTOREGIST' ,         'description'=>'是否支持认证登录 无账号自动创建<br>Can auto regist new user when login user not exist','content'=> 0 , ],

        ['settingid'=>'O3iE5ug1','keyid'=>'SENDLOGINCODE_USERNOTEXIST' , 'description'=>'用户不存在是是否可以发送登录验证码<br>Can send verify code ( login scope ) if user not exist','content'=> 0 , ],

        /* 支付 */
        ['settingid'=>'RAkVY6xg','keyid'=>'PAYMENT_VALIDTIME' ,    'description'=>'支付有效时间','content'=> 300 , ],


        /* 订单相关 */
        ['settingid'=>'ezzaER09','keyid'=>'ORDER_VALIDTIME' ,      'description'=>'订单有效时长<br>Valid duration of an order','content'=> 3*24*3600 , ],

        /* RECORD 后台统计设置  */

        ['settingid'=>'GPbRA6dl','keyid'=>'RECORD_ENABLE' ,        'description'=>'是否开启日志记录<br>Is record enabled','content'=> 1  , ],
        ['settingid'=>'IixM9IQY','keyid'=>'RECORD_PRIVACY' ,       'description'=>'是否记录用户隐私,该功能目前无效<br>Is privacy information record enabled<br>Not supported now','content'=> 0 , ],



        /** 微信公众平台 Wechat Mp **/
        [ 'settingid'=>'KDN2RQ2X','keyid'=>'WXMP_ID',              'scope'=>'WECHAT','description'=>'公众号ID' ,   'content'=> NULL, ],
        [ 'settingid'=>'BdyE2mIY','keyid'=>'WXMP_SECRET',          'scope'=>'WECHAT','description'=>'公众号Secret' ,   'content'=> NULL, ],
        [ 'settingid'=>'b5DNDQht','keyid'=>'WXMP_TOKEN',           'scope'=>'WECHAT','description'=>'公众号Token' ,   'content'=> NULL, ],
        [ 'settingid'=>'U0iLIANT','keyid'=>'WXMP_EncodingAESKey',  'scope'=>'WECHAT','description'=>'公众号AESKey' ,   'content'=> NULL, ],

        /** 微信开放平台 wechat Open **/
        [ 'settingid'=>'FDhReq4X','keyid'=>'WXOPEN_ID',            'scope'=>'WECHAT','description'=>'微信开放平台-ID', 'content'=>NULL ,],
        [ 'settingid'=>'ywoVmb27','keyid'=>'WXOPEN_SECRET',        'scope'=>'WECHAT','description'=>'微信开放平台-SECRET', 'content'=>NULL ,],

        /** 微信支付 Wechat Payment **/
        [ 'settingid'=>'VlwhDqTQ','keyid'=>'WXPAY_ID',             'scope'=>'WECHAT','description'=>'微信支付-商户ID', 'content'=>NULL ,],
        [ 'settingid'=>'lSZDHspr','keyid'=>'WXPAY_KEY',            'scope'=>'WECHAT','description'=>'微信支付-支付密钥', 'content'=>NULL ,],
        [ 'settingid'=>'NCHYoQID','keyid'=>'WXPAY_SSLCERT_PATH',   'scope'=>'WECHAT','description'=>'微信支付-支付证书文件', 'content'=>NULL ,],
        [ 'settingid'=>'elJ7bbM3','keyid'=>'WXPAY_SSLKEY_PATH',    'scope'=>'WECHAT','description'=>'微信支付-支付密钥文件', 'content'=>NULL ,],



        /* 阿里云存储 ALIYUN OSS */

        [ 'settingid'=>'HV5ZhhsI','keyid'=>'OSS_KEYID',         'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'阿里云存储KEYID' ],
        [ 'settingid'=>'M0rlXnIM','keyid'=>'OSS_KEYSECRET',     'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'阿里云存储SECRET' ],
        [ 'settingid'=>'x2JbbMIa','keyid'=>'OSS_ENDPOINT',      'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'阿里云存储ENDPOINT' ],
        [ 'settingid'=>'mQJRz325','keyid'=>'OSS_BUCKET',        'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'阿里云存储BUCKET' ],
        [ 'settingid'=>'mQJZD225','keyid'=>'CUSTOM_OSS_DOMAIN',        'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'自定义绑定阿里云域名' ],

        /* 阿里云短信 ALIYUN SMS */

        [ 'settingid'=>'kJVKL3j2','keyid'=>'SMS_KEYID',         'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'阿里云短信KEYID' ],
        [ 'settingid'=>'nxActDTD','keyid'=>'SMS_KEYSECRET',     'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'云短信SECRET' ],
        [ 'settingid'=>'d85AyW4S','keyid'=>'SMS_MODULE_VERIFY', 'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'验证短信模板(更多模板设置进入短信模块)' ],
        [ 'settingid'=>'Li8ocs4m','keyid'=>'SMS_SIGN_VERIFY',   'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'验证短信签名' ],
        [ 'settingid'=>'ORKVnb2S','keyid'=>'SMS_MODULE_DM',     'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'推广短信模板(更多模板设置进入短信模块)' ],
        [ 'settingid'=>'CQb7FoOS','keyid'=>'SMS_SIGN_DM',       'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'推广短信签名' ],

        /* 阿里云邮件 ALIYUN SMTP */

        [ 'settingid'=>'WnXMO5TU','keyid'=>'SMTP_SERVER',          'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'SMTP服务器' ],
        [ 'settingid'=>'FVYZfGB8','keyid'=>'SMTP_PORT',            'scope'=>'ALIYUN', 'content'=> 80 , 'description'=>'端口 阿里云 建议使用80 本机可以使用25' ],
        [ 'settingid'=>'TJOxRWMf','keyid'=>'SMTP_REPLY_ACCOUNT',   'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'回复邮箱地址' ],
        [ 'settingid'=>'sbS8Mkzk','keyid'=>'SMTP_DM_ACCOUNT',      'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'广告邮箱地址' ],
        [ 'settingid'=>'WKxdK8fa','keyid'=>'SMTP_DM_PASS',         'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'广告邮箱密码' ],
        [ 'settingid'=>'tYqmhVYF','keyid'=>'SMTP_SUPPORT_ACCOUNT', 'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'服务邮箱地址' ],
        [ 'settingid'=>'dktp1BOG','keyid'=>'SMTP_SUPPORT_PASS',    'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'服务邮箱密码' ],



        /* 百度AI baidu-ai */

        [ 'settingid'=>'CtN8obRZ','keyid'=>'ORC_BAIDU_ID', 'scope'=>'BAIDUYUN', 'content'=> NULL, 'description'=>'百度云-AI识图ID' ],
        [ 'settingid'=>'YXzISeI2','keyid'=>'ORC_BAIDU_AK', 'scope'=>'BAIDUYUN', 'content'=> NULL, 'description'=>'百度云-AI识图KEY' ],
        [ 'settingid'=>'nWPBqZxO','keyid'=>'ORC_BAIDU_SK', 'scope'=>'BAIDUYUN', 'content'=> NULL, 'description'=>'百度云-AI识图SECRET' ],
        [ 'settingid'=>'L7OJMrcM','keyid'=>'access_token', 'scope'=>'BAIDUYUN', 'content'=> NULL, 'description'=>'百度云-AI识图Token' ],


        /* Point Bonus 积分奖励 */
        [ 'settingid'=>'3sD2AZZs','keyid'=>'regist','scope'=>'POINTBONUS_RULES','content'=>['title'=>'系统奖励','description'=>'注册平台用户成功','value'=>100,'limit'=>1],'description'=>'注册平台用户成功奖励' ],


        /* 苹果内购设置 */
        [ 'settingid'=>'bWihGFX0','keyid'=>'0000000000', 'scope'=>'APPLE_IAP_ITEMS', 'description'=>'绑定账号的永久权限的专业版','content'=>['ituneLevel'=>3,'price'=>18,'title'=>'专业版','description'=>'绑定账号的永久权限的专业版'],],


        /* 短信验证所使用的短信模板编号 */

        ['settingid'=>'LhSM5RK5','keyid'=>'verify',        'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 code',	],
        ['settingid'=>'cF6smfBU','keyid'=>'login',         'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 登录 code',	],
        ['settingid'=>'UCHxMmYy','keyid'=>'regist',        'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 注册 code',	],
        ['settingid'=>'tKUPuXhK','keyid'=>'loginError',    'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 登录异常 code',	],
        ['settingid'=>'hMJZ0gIe','keyid'=>'resetPassword', 'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 修改密码 code',	],
        ['settingid'=>'FVIuilFf','keyid'=>'updateInfo',    'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 修改信息 code',	],
        ['settingid'=>'Ztx1kzqz','keyid'=>'findPassword',  'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 查找密码 code',	],


    ],


];




