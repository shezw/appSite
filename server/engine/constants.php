<?php
/**
 * 全局常量注册
 *
 * @link https://appsite.cn
 * @author Sprite Shur  https://shezw.com  hello@shezw.com
 * @copyright shezw.com
 * @version 2.0
 */

use APS\AccessOperation;
use APS\AccessPermission;
use APS\AccessToken;
use APS\AccessVerify;
use APS\AdminRecord;
use APS\AnalysisProduct;
use APS\Area;
use APS\Article;
use APS\ASRecord;
use APS\ASSetting;
use APS\Banner;
use APS\Category;
use APS\CommerceCoupon;
use APS\CommerceOrder;
use APS\CommercePayment;
use APS\CommerceProduct;
use APS\CommerceShipping;
use APS\CommerceStock;
use APS\CommerceWriteOff;
use APS\Company;
use APS\District;
use APS\FinanceDeal;
use APS\FinanceWithdraw;
use APS\FormContract;
use APS\FormRequest;
use APS\FormVerify;
use APS\IBChain;
use APS\Industry;
use APS\Media;
use APS\MediaTemplate;
use APS\MessageAnnouncement;
use APS\MessageChat;
use APS\MessageNotification;
use APS\Page;
use APS\Relation;
use APS\ShieldWord;
use APS\Subway;
use APS\Tag;
use APS\ThirdPartyRecord;
use APS\UserAccount;
use APS\UserAddress;
use APS\UserCollect;
use APS\UserComment;
use APS\UserGroup;
use APS\UserInfo;
use APS\UserPocket;
use APS\UserPreference;
use APS\UserRecord;








const RouteScopeWebsite = 'WEBSITE';
const WebsiteDefaultID  = 'AppSite_W';
const WebsiteDefaultTheme = 'boomerang';
const WebsiteDefaultRootPath = 'website';
const WebsiteDefaultRouteFormat = 'class/action/id';

const RouteScopeManagement = 'MANAGER';
const ManagementDefaultID  = 'AppSite_M';
const ManagementDefaultTheme = 'stisla';
const ManagementDefaultRootPath = 'manager';
const ManagementDefaultRouteFormat = 'manager/class/action/id';

const DefaultAvatar     = '/website/static/appsiteJS/images/avatar.jpg';

const DBField_Null      = -10;
const DBField_Boolean   = -1;  # tinyint 1

const DBField_Int       = 1;   # tinyint 1-3     mediumint 3-6   bigint 8-13
const DBField_Float     = 2;   # 0
const DBField_Double    = 3;   # 0
const DBField_Decimal   = 9;   # ?,?
const DBField_TimeStamp = 10; # bigint 13

const DBField_Location  = 50;  # GEOMETRY ( DOUBLE )

const DBField_String    = 100; # varchar <=2048 ,  text >2048
const DBField_RichText  = 101; # text 65535  mediumint 16777215
const DBField_Json      = 200; # text 0
const DBField_ASJson    = 201; # text 0


const DBOrder_ASC  = ' ASC ';    /** 升序 */
const DBOrder_DESC = ' DESC ';   /** 降序 */

const DBIndex_Primary  = ' PRIMARY ';       /** 主键索引 */
const DBIndex_Index    = ' INDEX ';         /** 一般索引 */
const DBIndex_Unique   = ' UNIQUE ';        /** 唯一索引 */
const DBIndex_FullText = ' FULLTEXT ';      /** 分词索引 */
const DBIndex_Spatial  = ' SPATIAL ';       /** 空间索引 */


const SuperAdminUID             = 'SUPER';
const AdminUID                  = 'ADMIN';

const Group_SuperAdmin          = '900';
const Group_Admin               = '800';
const Group_Editor              = '400';
const Group_Author              = '300';
const Group_AuthorStandard      = '3010';
const Group_AuthorPro           = '3020';
const Group_AuthorExclusive     = '3030';
const Group_Registered          = '100';
const Group_Guest               = '0';
const Group_Illegal             = '00000000';

const GroupLevel_SuperAdmin     = 90000;
const GroupLevel_Admin          = 80000;
const GroupLevel_Editor         = 40000;
const GroupLevel_Author         = 30000;
const GroupLevel_AuthorStandard = 30100;
const GroupLevel_AuthorPro      = 30200;
const GroupLevel_AuthorExclusive= 30300;
const GroupLevel_Registered     = 10000;
const GroupLevel_Guest          = 0;
const GroupLevel_Illegal        = -1;

const GroupRole_Guest      = 'guest';
const GroupRole_User       = 'user';
const GroupRole_Manager    = 'manager';
const GroupRole_Super      = 'super';
const GroupRole_Editor     = 'editor';
const GroupRole_Illegal    = 'illegal';

const Status_Super    		= 'SUPER';
const Status_Enabled  		= 'enabled';
const Status_Pending  		= 'pending';
const Status_Blocked        = 'blocked';
const Status_Locked         = 'locked';

const Status_Default  		= 'default';
const Status_Verified 		= 'verified';

const Status_Sent     		= 'sent';
const Status_Received 		= 'received';
const Status_Read     		= 'read';

const Status_Success  		= 'success';
const Status_Failed   		= 'failed';
const Status_Error    		= 'error';


const Type_Email        	= 'email';
const Type_EmailSubject 	= 'subject';
const Type_SMS          	= 'sms';
const Type_HTML         	= 'html';
const Type_Message      	= 'message';
const Type_Notify       	= 'notify';
const Type_Common           = 'common';
const Type_Normal           = 'normal';
const Type_Media            = 'media';
const Type_Image            = 'image';
const Type_Audio            = 'audio';
const Type_Video            = 'video';
const Type_Article          = 'article';
const Type_Url              = 'url';
const Type_File             = 'file';
const Type_Product          = 'product';

const Type_Page             = 'page';
const Type_Website          = 'website';
const Type_Item             = 'item';
const Type_payment          = 'payment';

const Type_Commission       = 'commission';     # 佣金
const Type_Transmission     = 'transmission';   # 转账
const Type_Bonus            = 'bonus';          # 奖励

const Type_balance          = 'balance';        # 余额
const Type_point            = 'point';          # 积分

const ItemTypes = [
    Type_Email, Type_EmailSubject, Type_SMS, Type_HTML, Type_Message, Type_Notify, Type_Normal, Type_Media, Type_Image, Type_Audio, Type_Video, Type_Article, Type_Url, Type_File, Type_Product, Type_Page, Type_Website, Type_Item, Type_payment,
];

const i18n_Common = 'COMMON';
const i18n_Status = 'STATUS';
const i18n_Type   = 'TYPE';
const i18n_Constants = 'constants';
const i18n_Gender    = 'gender';
const i18n_Featured  = 'isFeatured';
const i18n_Education = 'education';
const i18n_Payer     = 'payer';
const i18n_Country   = 'country';
const i18n_StatusCode = 'statusCode';
const i18n_Payment = 'PAYMENT';
const i18n_Manager = 'manager';
const i18n_TimeFormat = 'TimeFormat';
const i18n_Website = 'website';
const i18n_MySQL = 'mysql';
const i18n_Redis = 'redis';

const StorageLocation_AliOSS = 2;
const StorageLocation_LocalStatic = 1;

const TimeFormat_LiteDate   = 'litedate';
const TimeFormat_FullDate   = 'fulldate';
const TimeFormat_NumberDate = 'numberdate';

const TimeFormat_LiteTime   = 'litetime';
const TimeFormat_FullTime   = 'fulltime';
const TimeFormat_NumberTime = 'numbertime';

const TimeFormat_NumberMonth= 'numbermonth';
const TimeFormat_DatePicker = 'datepicker';


const ASAPI_Mode_ASAPI       	= 'ASAPI';
const ASAPI_Mode_API         	= 'ASAPI';
const ASAPI_Mode_RAW         	= 'RAW';
const ASAPI_Mode_Json        	= 'JSON';
const ASAPI_Mode_Javascript  	= 'JSON';
const ASAPI_Mode_HTML        	= 'HTML';

const ASAPI_Scope_Public        = 'public';
const ASAPI_Scope_System        = 'system';

const AccessScope_Common    	= 'common';
const AccessScope_IOS       	= 'ios';
const AccessScope_Android   	= 'android';
const AccessScope_MiniProgram	= 'miniprogram';
const AccessScope_Website   	= 'website';

const QuerySymbol_None          = "";
const QuerySymbol_Bigger  		= '[[>]]';
const QuerySymbol_Less  		= '[[<]]';
const QuerySymbol_BiggerAnd  	= '[[>=]]';
const QuerySymbol_LessAnd  		= '[[<=]]';
const QuerySymbol_NotEqual  	= '[[!=]]';
const QuerySymbol_In  			= '[[IN]]';
const QuerySymbol_Null  		= '[[NULL]]';
const QuerySymbol_NotNull  		= '[[NOTNULL]]';
const QuerySymbol_Between  		= '[[BETWEEN]]';
const QuerySymbols = [ QuerySymbol_Bigger,QuerySymbol_Less,QuerySymbol_BiggerAnd,QuerySymbol_LessAnd,QuerySymbol_NotEqual,QuerySymbol_In,QuerySymbol_Null,QuerySymbol_NotNull,QuerySymbol_Between ];


const DBFilterSymbol_Between     = ' BETWEEN ';
const DBFilterSymbol_Less        = ' < ';
const DBFilterSymbol_LessAnd     = ' <= ';
const DBFilterSymbol_NotEqual    = ' != ';

const DBFilterSymbol_Bigger      = ' > ';
const DBFilterSymbol_BiggerAnd   = ' >= ';

const DBFilterSymbol_In          = ' IN ';

const DBFilterSymbol_Null        = ' IS NULL ';
const DBFilterSymbol_NotNull     = ' IS NOT NULL ';

const DBFilterSymbol_Keyword     = ' KEYWORD ';
const DBFilterSymbol_Keywords    = ' KEYWORDS ';

const DBFilterSymbol_Or          = ' OR ';
const DBFilterSymbol_Match       = ' MATCH ';
const DBFilterSymbol_Equal       = ' = ';

const DBFilterSymbol_Query       = ' QUERY ';


const DefaultModels = [

    ASSetting::class,
//    ShieldWord::class,

    AccessToken::class,
    AccessPermission::class,
    AccessOperation::class,
    AccessVerify::class,

    UserAccount::class,
    UserInfo::class,
    UserGroup::class,
    UserPocket::class,
    UserAddress::class,
    UserComment::class,
    UserCollect::class,
    UserPreference::class,


    Category::class,
    Tag::class,
    Banner::class,
    Page::class,
    Article::class,
    Media::class,
    MediaTemplate::class,

    CommerceOrder::class,
    CommercePayment::class,
    CommerceProduct::class,
    CommerceCoupon::class,
    CommerceShipping::class,
    CommerceStock::class,
    CommerceWriteOff::class,

    AnalysisProduct::class,

    FinanceWithdraw::class,
    FinanceDeal::class,

    MessageNotification::class,
    MessageAnnouncement::class,
    MessageChat::class,


    FormRequest::class,
    FormVerify::class,
    FormContract::class,


    IBChain::class,
    Relation::class,

    ASRecord::class,
    UserRecord::class,
    AdminRecord::class,
    ThirdPartyRecord::class,


    Area::class,
    Industry::class,
    Company::class,
    District::class,
    Subway::class,

];