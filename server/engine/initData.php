<?php

use APS\Area;
use APS\ASSetting;
use APS\Category;
use APS\MediaTemplate;
use APS\User;
use APS\UserAccount;
use APS\UserGroup;
use APS\UserInfo;
use APS\UserPocket;

return [

    User::class => [
        ['uid'=>SuperAdminUID,  'username'=>'superadmin',  'nickname'=>'超管',    'groupid'=>Group_SuperAdmin, 'status'=>Status_Super ,  'userid'=>SuperAdminUID, 'point'=>1000 ],
        ['uid'=>AdminUID,       'username'=>'administor',  'nickname'=>'管理员',  'groupid'=>Group_Admin,      'status'=>Status_Enabled, 'userid'=>AdminUID, 'point'=>1000 ],
    ],
    UserGroup::class =>[

        ['uid'=>Group_Guest, 'type'=>GroupRole_Guest,   'level'=>GroupLevel_Guest,     'groupname'=>'游客',      'sort'=>0,   'description'=>'未注册用户或未能获取到用户信息'],

        ['uid'=>Group_Registered,  'type'=>GroupRole_User,    'level'=>GroupLevel_Registered, 'groupname'=>'注册用户',   'sort'=>0,   'description'=>'注册成功的用户'],
        ['uid'=>Group_Author,  'type'=>GroupRole_Editor, 'level'=>GroupLevel_Author, 'groupname'=>'撰稿人',     'sort'=>2,   'description'=>'可以在特定板块发布和管理'],
        ['uid'=>Group_Editor,  'type'=>GroupRole_Manager, 'level'=>GroupLevel_Editor, 'groupname'=>'网站编辑',   'sort'=>3,   'description'=>'可以编辑banner，咨询，反馈等'],

        ['uid'=>Group_Admin,  'type'=>GroupRole_Manager, 'level'=>GroupLevel_Admin, 'groupname'=>'管理员',    'sort'=>4,    'description'=>'可以管理网站全部内容和设置分销等'],
        ['uid'=>Group_SuperAdmin,  'type'=>GroupRole_Super,   'level'=>GroupLevel_SuperAdmin, 'groupname'=>'超级管理员', 'sort'=>999,    'description'=>'可以设置全部内容以及修改网站基础配置'],

        ['uid'=>Group_Illegal, 'type'=>GroupRole_Illegal,   'level'=>GroupLevel_Illegal,    'groupname'=>'禁止访问',   'sort'=>9999,   'description'=>'因违规或其他原因被禁止访问的账户'],

        ['uid'=>Group_AuthorStandard, 'type'=>GroupRole_Editor, 'level'=>GroupLevel_AuthorStandard, 'parentid'=>Group_Author, 'groupname'=>'普通撰稿人',   'sort'=>100,  'description'=>'普通一些的'],
        ['uid'=>Group_AuthorPro, 'type'=>GroupRole_Editor, 'level'=>GroupLevel_AuthorPro, 'parentid'=>Group_Author, 'groupname'=>'高级撰稿人',   'sort'=>200,   'description'=>'经验丰富的'],
        ['uid'=>Group_AuthorExclusive, 'type'=>GroupRole_Editor, 'level'=>GroupLevel_AuthorExclusive, 'parentid'=>Group_Author, 'groupname'=>'特约撰稿人',   'sort'=>300,   'description'=>'声名远播的'],

    ],

    Category::class =>[

        ['type'=>Type_Media,'title'=>'全部媒体', ],

        ['parentid'=>Type_Media,'type'=>Type_Media,  'title'=>'图片' ],
        ['parentid'=>Type_Media,'type'=>Type_Media,  'title'=>'视频' ],
        ['parentid'=>Type_Media,'type'=>Type_Media,  'title'=>'音频' ],
        ['parentid'=>Type_Media,'type'=>Type_Media,  'title'=>'文件' ],

    ],
    Area::class =>[

        ['areaid'=>'root','title'=>'系统地区','description'=>'默认地区','level'=>0,'location'=>'126.15170,25.87275' ],

    ],

    MediaTemplate::class=>[
        ['keyid'=>'verify','type'=>Type_Email,'i18n'=>'zh-CN','description'=>'验证邮件内文',
            'content'=>"
                <div style=\"height:60px;width:100%;position:relative;background:#28282d;\">
					<b style=\"font-weight: normal; color: rgb(136, 136, 136); margin-left: 20px; height: 60px; line-height: 60px; display: inline !important;\">邮件验证&nbsp; </b></div>
			<div style=\"padding:25px 35px;color:#444;background:#f5f5f5;\">
			<h3>Welcome!</h3>
			<p><span style=\"font-family: arial,serif;\">您的验证码是: <b><u>{{code}}</u></b>&nbsp;</span></p>
			<p><span style=\"font-family: arial,serif;\">请在{{duration}}分钟内进行验证,过期将作废。</span></p>
			<p><font face=\"arial\">AppSite Framework</font></p></div>
			<div style=\"width:100%;height:60px;background:#eee;color:#999; padding:15px 30px\">
			Support: <a href=\"https://appsite.cn\">https://appsite.cn</a>&nbsp;</div>"
        ],
        ['keyid'=>'verify','type'=>Type_EmailSubject,'i18n'=>'zh-CN','description'=>'验证邮件标题',
            'content'=>'您的邮件验证码-AppSite',
        ],
        ['keyid'=>'verify','type'=>Type_Email,'i18n'=>'en-WW','description'=>'Verify Email Content',
            'content'=>"
                <div style=\"height:60px;width:100%;position:relative;background:#28282d;\">
					<b style=\"font-weight: normal; color: rgb(136, 136, 136); margin-left: 20px; height: 60px; line-height: 60px; display: inline !important;\">Email Verify&nbsp; </b></div>
			<div style=\"padding:25px 35px;color:#444;background:#f5f5f5;\">
			<h3>Welcome!</h3>
			<p><span style=\"font-family: Arial,serif;\">Your verify code is: <b><u>{{code}}</u></b>&nbsp;</span></p>
			<p><span style=\"font-family: arial,serif;\">Please verify in {{duration}} minutes.</span></p>
			<p><font face=\"arial\">AppSite Framework</font></p></div>
			<div style=\"width:100%;height:60px;background:#eee;color:#999; padding:15px 30px\">
			Support: <a href=\"https://appsite.cn\">https://appsite.cn</a>&nbsp;</div>"
        ],
        ['keyid'=>'verify','type'=>Type_EmailSubject,'i18n'=>'en-WW','description'=>'Verify Email Subject',
            'content'=>'Your Email Verify Code from AppSite',
        ],

        ['keyid'=>'newUser','type'=>Type_Notify,'i18n'=>'zh-CN','description'=>'新用户注册通知',
            'content'=>"欢迎您注册本应用<br/>"
        ],
        ['keyid'=>'newVIP','type'=>Type_Notify,'i18n'=>'zh-CN','description'=>'用户升级会员通知',
            'content'=>"欢迎您的支持,您的会员有效期至{{expire_}}<br/>享受您的时光～"
        ],

        ['keyid'=>'newUser','type'=>Type_Notify,'i18n'=>'en-WW','description'=>'New User notify',
            'content'=>"Welcome to AppSite<br/>"
        ],
        ['keyid'=>'newVIP','type'=>Type_Notify,'i18n'=>'en-WW','description'=>'VIP purchased notify',
            'content'=>"Thanks for your supporting, Your VIP will continuing till {{expire_}}<br/>Enjoy～"
        ],
    ],

    ASSetting::class=>[

        /* 后台配置 */
        ['keyid'=> 'DEFAULT_LANGUAGE',     'content'=>'en-WW',                   'description'=>'默认语言'],
        ['keyid'=> 'MANAGER_ROUTE_FORMAT', 'content'=>'manager/class/action/id', 'description'=>'管理后台路由格式'],
        ['keyid'=> 'WEBSITE_ROUTE_FORMAT', 'content'=>'class/action/id',         'description'=>'网站前台路由格式'],
        ['keyid'=> 'API_ROUTE_FORMAT',     'content'=>'api/namespace/class/id',  'description'=>'接口路由格式'],
        ['keyid'=> 'MAIN_PATH',            'content'=>'/',                       'description'=>'主路径'],
        ['keyid'=> 'SITE_PATH',            'content'=>'/',                       'description'=>'网站前台路径'],
        ['keyid'=> 'API_PATH',             'content'=>'/api/',                   'description'=>'接口路径'],
        ['keyid'=> 'STATIC_PATH',          'content'=>'/website/static/',        'description'=>'静态资源路径'],

        ['keyid'=> 'REDIS_HOST',           'content'=>'127.0.0.1',        'description'=>'Redis Host'],
        ['keyid'=> 'REDIS_PORT',           'content'=>6379,        'description'=>'Redis Port'],
        ['keyid'=> 'SERVER_IP',            'content'=>NULL,        'description'=>'服务器ip'],

        ['keyid'=> 'title',          'scope'=>'MANAGER', 'content'=>'管理后台' , 'description'=>'后台名称',],
        ['keyid'=> 'description',    'scope'=>'MANAGER', 'content'=>'AppSite Back-End Management', 'description'=>'后台简介',],
        ['keyid'=> 'id',             'scope'=>'MANAGER', 'content'=>'appsite_m', 'description'=>'后台识别ID 用于本地缓存',],
        ['keyid'=> 'rootPath',       'scope'=>'MANAGER', 'content'=>'manager', 'description'=>'自定义根目录,用于隐藏后台地址。需要与Rewrite规则匹配。',],
        ['keyid'=> 'logoUrl',        'scope'=>'MANAGER', 'content'=>'/website/static/appsiteJS/images/logo480.png', 'description'=>'LOGO地址',],
        ['keyid'=> 'logoW',          'scope'=>'MANAGER', 'content'=>NULL, 'description'=>'横版logo',],
        ['keyid'=> 'logoH',          'scope'=>'MANAGER', 'content'=>NULL, 'description'=>'竖版logo',],

        ['keyid'=> 'title',        'scope'=>'WEBSITE', 'content'=>'AppSite' , 'description'=>'网站名称',],
        ['keyid'=> 'id',           'scope'=>'WEBSITE', 'content'=>'appsite_w', 'description'=>'前台识别ID 用于本地缓存',],
        ['keyid'=> 'logoUrl',      'scope'=>'WEBSITE', 'content'=>'/website/static/appsiteJS/images/logo480.png', 'description'=>'LOGO地址',],
        ['keyid'=> 'logoW',        'scope'=>'WEBSITE', 'content'=>'/website/static/appsiteJS/images/logo-W.png', 'description'=>'横版logo',],
        ['keyid'=> 'logoH',        'scope'=>'WEBSITE', 'content'=>'/website/static/appsiteJS/images/logo-H.png', 'description'=>'竖版logo',],
        ['keyid'=> 'defaultAvatar','scope'=>'WEBSITE', 'content'=>'/website/static/appsiteJS/images/avatar.jpg', 'description'=>'默认头像 完整链接'],
        ['keyid'=> 'imagePreview', 'scope'=>'WEBSITE', 'content'=>NULL, 'description'=> '默认预览 完整链接 '],

        /* 安全防护配置 */

        ['keyid'=>'ALLOWED_IP', 'scope'=>'PRIVATES', 'content'=>[ '127.0.0.1'=>1,] ,'description'=>'IP白名单' ],

        /* 媒体上传管理设置 */

        ['keyid'=>'MEDIA_HOST_UPLOAD',     'description'=>'是开启直传服务器',  'content'=> 0,],
        ['keyid'=>'CUSTOM_OSS_DOMAIN',     'description'=>'自定义云存储绑定域名',  'content'=> NULL,],
        ['keyid'=>'MEDIA_UPLOAD_ENGINE',   'description'=>'外部上传',  'content'=> 'ALIYUN_OSS' ,],
        ['keyid'=>'MEDIA_TEMP_DIR',        'description'=>'上传缓存目录',  'content'=> ':/tmp/',],
        ['keyid'=>'MEDIA_POLICY_DURATION', 'description'=>'上传签名有效时长',  'content'=> 300  ,],
        ['keyid'=>'MEDIA_MAX_SIZE',        'description'=>'最大视频文件大小',  'content'=> 500*1024*1024 ,],
        ['keyid'=>'MEDIA_MAX_VIDEO_SIZE',  'description'=>'最大视频文件大小',  'content'=> 500*1024*1024 ,],
        ['keyid'=>'MEDIA_MAX_AUDIO_SIZE',  'description'=>'最大音频文件大小',  'content'=> 20*1024*1024 ,],
        ['keyid'=>'MEDIA_MAX_IMAGE_SIZE',  'description'=>'最大图片文件大小',  'content'=> 8*1024*1024 ,],
        ['keyid'=>'MEDIA_MAX_FILE_SIZE',   'description'=>'最大文件大小',  'content'=> 30*1024*1024 ,],

        /* 上传支持格式配置 Supported file type of upload */

        ['keyid'=>'MEDIA_VIDEO_TYPE' ,           'description'=>'上传支持视频格式','content'=> 'mp4,avi,mov,mpg' , ],
        ['keyid'=>'MEDIA_AUDIO_TYPE' ,           'description'=>'上传支持音频格式','content'=> 'mp3,ogg' , ],
        ['keyid'=>'MEDIA_IMAGE_TYPE' ,           'description'=>'上传支持图片格式','content'=> 'jpg,jpeg,png,gif' , ],
        ['keyid'=>'MEDIA_FILE_TYPE' ,            'description'=>'上传支持文件格式','content'=> 'key,pages,numbers,xls,ppt,xlsx,pptx,pdf' , ],

        ['keyid'=>'ACCESSTOKEN_DURATION' ,       'description'=>'权限有效时长<br>Default duration of Access Token','content'=> 7*24*3600 , ],

        ['keyid'=>'LOGINTOKEN_DURATION' ,        'description'=>'登录有效时长<br>Valid duration on Login Token','content'=> 30*24*3600 , ],

        ['keyid'=>'ACCESSVERIFY_LENGTH' ,        'description'=>'验证字长<br>Length of Verify Code','content'=> 6 , ],

        ['keyid'=>'ACCESSVERIFY_INTERVAL' ,      'description'=>'验证安全间隔,低于该限制的验证请求将被拒绝.<br>Limit interval when verify <br>Verify request will be reject when the interval time less this','content'=> 60 , ],

        ['keyid'=>'ACCESSVERIFY_VALID' ,         'description'=>'验证有效时间<br>Valid duration once verify','content'=> 900 , ],

        ['keyid'=>'VERIFY_ONETIME' ,             'description'=>'是否仅支持一次性验证 验证通过时立即删除该验证<br>Only valid one time on verify','content'=> 1 , ],

        ['keyid'=>'AUTO_LOGINTOREGIST' ,         'description'=>'是否支持认证登录 无账号自动创建<br>Can auto regist new user when login user not exist','content'=> 0 , ],

        ['keyid'=>'SENDLOGINCODE_USERNOTEXIST' , 'description'=>'用户不存在是是否可以发送登录验证码<br>Can send verify code ( login scope ) if user not exist','content'=> 0 , ],

        /* Payment expire duration 支付 */
        ['keyid'=>'PAYMENT_VALIDTIME' ,    'description'=>'支付有效时间','content'=> 300 , ],


        /* Order Config 订单相关 */
        ['keyid'=>'ORDER_VALIDTIME' ,      'description'=>'订单有效时长<br>Valid duration of an order','content'=> 3*24*3600 , ],

        /* RECORD 后台统计设置  */

        ['keyid'=>'RECORD_ENABLE' ,        'description'=>'是否开启日志记录<br>Is record enabled','content'=> 1  , ],
        ['keyid'=>'RECORD_PRIVACY' ,       'description'=>'是否记录用户隐私,该功能目前无效<br>Is privacy information record enabled<br>Not supported now','content'=> 0 , ],



        /** Wechat Mp 微信公众平台 **/
        ['keyid'=>'WXMP_ID',              'scope'=>'WECHAT','description'=>'公众号ID' ,   'content'=> NULL, ],
        ['keyid'=>'WXMP_SECRET',          'scope'=>'WECHAT','description'=>'公众号Secret' ,   'content'=> NULL, ],
        ['keyid'=>'WXMP_TOKEN',           'scope'=>'WECHAT','description'=>'公众号Token' ,   'content'=> NULL, ],
        ['keyid'=>'WXMP_EncodingAESKey',  'scope'=>'WECHAT','description'=>'公众号AESKey' ,   'content'=> NULL, ],

        /** Wechat Open 微信开放平台 **/
        ['keyid'=>'WXOPEN_ID',            'scope'=>'WECHAT','description'=>'微信开放平台-ID', 'content'=>NULL ,],
        ['keyid'=>'WXOPEN_SECRET',        'scope'=>'WECHAT','description'=>'微信开放平台-SECRET', 'content'=>NULL ,],

        /** Wechat Payment 微信支付 **/
        ['keyid'=>'WXPAY_ID',             'scope'=>'WECHAT','description'=>'微信支付-商户ID', 'content'=>NULL ,],
        ['keyid'=>'WXPAY_KEY',            'scope'=>'WECHAT','description'=>'微信支付-支付密钥', 'content'=>NULL ,],
        ['keyid'=>'WXPAY_SSLCERT_PATH',   'scope'=>'WECHAT','description'=>'微信支付-支付证书文件', 'content'=>NULL ,],
        ['keyid'=>'WXPAY_SSLKEY_PATH',    'scope'=>'WECHAT','description'=>'微信支付-支付密钥文件', 'content'=>NULL ,],


        /* ALIYUN OSS 阿里云存储 */

        ['keyid'=>'OSS_KEYID',         'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'阿里云存储KEYID' ],
        ['keyid'=>'OSS_KEYSECRET',     'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'阿里云存储SECRET' ],
        ['keyid'=>'OSS_ENDPOINT',      'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'阿里云存储ENDPOINT' ],
        ['keyid'=>'OSS_BUCKET',        'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'阿里云存储BUCKET' ],
        ['keyid'=>'CUSTOM_OSS_DOMAIN',        'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'自定义绑定阿里云域名' ],

        /* ALIYUN SMS 阿里云短信 */

        ['keyid'=>'SMS_KEYID',         'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'阿里云短信KEYID' ],
        ['keyid'=>'SMS_KEYSECRET',     'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'云短信SECRET' ],
        ['keyid'=>'SMS_MODULE_VERIFY', 'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'验证短信模板(更多模板设置进入短信模块)' ],
        ['keyid'=>'SMS_SIGN_VERIFY',   'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'验证短信签名' ],
        ['keyid'=>'SMS_MODULE_DM',     'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'推广短信模板(更多模板设置进入短信模块)' ],
        ['keyid'=>'SMS_SIGN_DM',       'scope'=>'ALIYUN', 'content'=>NULL , 'description'=>'推广短信签名' ],

        /* ALIYUN SMTP 阿里云邮件 */

        ['keyid'=>'SMTP_SERVER',          'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'SMTP服务器' ],
        ['keyid'=>'SMTP_PORT',            'scope'=>'ALIYUN', 'content'=> 80 , 'description'=>'端口 阿里云 建议使用80 本机可以使用25' ],
        ['keyid'=>'SMTP_REPLY_ACCOUNT',   'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'回复邮箱地址' ],
        ['keyid'=>'SMTP_DM_ACCOUNT',      'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'广告邮箱地址' ],
        ['keyid'=>'SMTP_DM_PASS',         'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'广告邮箱密码' ],
        ['keyid'=>'SMTP_SUPPORT_ACCOUNT', 'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'服务邮箱地址' ],
        ['keyid'=>'SMTP_SUPPORT_PASS',    'scope'=>'ALIYUN', 'content'=> NULL , 'description'=>'服务邮箱密码' ],



        /* Baidu Developer 百度AI baidu-ai */

        ['keyid'=>'ORC_BAIDU_ID', 'scope'=>'BAIDUYUN', 'content'=> NULL, 'description'=>'百度云-AI识图ID' ],
        ['keyid'=>'ORC_BAIDU_AK', 'scope'=>'BAIDUYUN', 'content'=> NULL, 'description'=>'百度云-AI识图KEY' ],
        ['keyid'=>'ORC_BAIDU_SK', 'scope'=>'BAIDUYUN', 'content'=> NULL, 'description'=>'百度云-AI识图SECRET' ],
        ['keyid'=>'access_token', 'scope'=>'BAIDUYUN', 'content'=> NULL, 'description'=>'百度云-AI识图Token' ],


        /* Point Bonus 积分奖励 */
        ['keyid'=>'regist','scope'=>'POINTBONUS_RULES','content'=>['title'=>'系统奖励','description'=>'注册平台用户成功','value'=>100,'limit'=>1],'description'=>'注册平台用户成功奖励' ],


        /* AppStore In-app purchase 苹果内购设置 */
        ['keyid'=>'0000000000', 'scope'=>'APPLE_IAP_ITEMS', 'description'=>'绑定账号的永久权限的专业版','content'=>['ituneLevel'=>3,'price'=>18,'title'=>'专业版','description'=>'绑定账号的永久权限的专业版'],],


        /* SMS template code 短信验证所使用的短信模板编号 */

        ['keyid'=>'verify',        'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 code',   ],
        ['keyid'=>'login',         'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 登录 code',    ],
        ['keyid'=>'regist',        'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 注册 code',    ],
        ['keyid'=>'loginError',    'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 登录异常 code',  ],
        ['keyid'=>'resetPassword', 'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 修改密码 code',  ],
        ['keyid'=>'updateInfo',    'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 修改信息 code',  ],
        ['keyid'=>'findPassword',  'scope'=>'SMS_MODULE_CODE', 'content'=>NULL, 'description'=>'验证码 查找密码 code',  ],


    ],


];

