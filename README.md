## AppSite
### PHP跨平台应用/网站开发引擎

简体中文 [English](README_en.md) ( 介绍、文档待补充 )

AppSite 是一个以面向对象方式为主要思想的开发引擎，包括了一套可用于企业级应用的服务端程序、管理后台程序以及API接口容器和一个网站端容器。
引擎内置了众多基础核心模型和基础模块，通过相应的功能组合可以快速实现各类需求。

AppSite 可用于开发网站、服务端程序、API接口、后台管理程序或者是同时兼顾。

<hr>

### 核心模块 
#### 基础对象 ASObject  

AppSite使用基础对象来实现一些统一的结果处理，因为通常情况下业务型代码的返回分为 符合期望、不符合期望的两种最基本情况，ASResult即封装了这样的结果数据，使得每一步都拥有简明扼要的判断形式，同时又能够迭代式的向后传递成功/错误数据。
每一个模型类都从基类ASObject中继承了该结果属性，可以将自身的结果反馈给请求方。

```php
/**
 * Sample of result returning
 */

$checkUsername = \APS\User::common()->detail( filters:['username'=>'admin'] );

if( !$checkUsername->isSucceed() ){
    return $checkUsername;
}

\APS\User::common()->update(  params:['groupid'=>'900'], userid: $checkUsername->getContent()['userid'] );
```


#### 数据库 ASDB

ASDB是一个用于请求mySQL数据的操作器，用于将大部分常用的数据库请求使用数组的形式来实现。
ASDB的主要常用方式，也进一步封装进了ASModel中，例如我们需要查询 文章的列表或详情，只需要在在模型实例上使用对应方法即可。

```php
/**
 * Get data by ASDB methods
 */

// Get articles are featured  on page 1, size 15
$getListData = \APS\ASDB::shared()->get( ['uid','title','cover'], 'item_article', ['featured'=>1], 1, 15 );

// Get article details
$getDetailData = \APS\ASDB::shared()->get( ['*'], 'item_article', ['uid'=>'sampleUID'], 1, 1 );


/**
 * Get data by ASModel methods
 */

// Get articles are featured  on page 1, size 15
$getArticles = \APS\Article::common()->list( ['featured'=>1], 1, 15 );

// Get article details
$getArticleDetail = \APS\Article::common()->detail( 'sampleUID' );
```

#### 基础模型 ASModel

ASModel是将数据与模型关联的一个基类。
在AppSite开发中，基础数据无需过度关注，在实际使用中，通常只需要在继承ASModel时，将对应的字段信息记录在模型属性中就可以快速调用了。
例如在Article类中，有如下定义: [Article.php](server/engine/service/Article.php)

```php
class Article extends \APS\ASModel{
    const table     = "item_article";
    const primaryid = "uid";

    const addFields = [
        'uid','categoryid','areaid',...'description','introduce',
        'viewtimes','sort','featured','status',
        'createtime','lasttime',
    ];
    ...
    const detailFields  = [...];
    ...
    const listFields  = [
        'uid','categoryid','areaid',...'createtime','lasttime'
    ];
    const FilterFields  = [
        'uid','categoryid','areaid',...'createtime','lasttime'
    ];
    const depthStruct  = [
		'gallery'=>DBField_Json,
		'viewtimes'=>DBField_Int,
		'featured'=>DBField_Boolean,
		'createtime'=>DBField_TimeStamp,
    ];
    const tableStruct = [

		'uid'           =>['type'=>DBField_String,    'len'=>8,   'nullable'=>0,  'cmt'=>'索引ID' , 'idx'=>DBIndex_Unique ],
		'categoryid'    =>['type'=>DBField_String,    'len'=>8,   'nullable'=>1,  'cmt'=>'分类ID' , 'idx'=>DBIndex_Index ],
		'type'          =>['type'=>DBField_String,    'len'=>16,  'nullable'=>1,  'cmt'=>'类型 text,cover,video,gallery...' ],
		...
		'title'         =>['type'=>DBField_String,    'len'=>32,  'nullable'=>0,  'cmt'=>'名称名' ,  'idx'=>DBIndex_FullText ],
		'cover'         =>['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'封面' ],
		'gallery'       =>['type'=>DBField_Json,      'len'=>-1,  'nullable'=>1,  'cmt'=>'详情介绍' ],
		...
		'description'   =>['type'=>DBField_String,    'len'=>255, 'nullable'=>1,  'cmt'=>'描述' ,   'idx'=>DBIndex_FullText ],
		'introduce'     =>['type'=>DBField_RichText,  'len'=>-1,  'nullable'=>1,  'cmt'=>'详情介绍' ],
		...
		'viewtimes'     =>['type'=>DBField_Int,       'len'=>13,  'nullable'=>0,  'cmt'=>'播放次数',  'dft'=>0,       ],
		'status'        =>['type'=>DBField_String,    'len'=>12,  'nullable'=>0,  'cmt'=>'状态',    'dft'=>'enabled',       ],
		...
		'createtime'    =>['type'=>DBField_TimeStamp,'len'=>13, 'nullable'=>0,  'cmt'=>'创建时间',           'idx'=>DBIndex_Index, ],
		'featured'      =>['type'=>DBField_Boolean,  'len'=>1,  'nullable'=>0,  'cmt'=>'置顶',  'dft'=>0,    'idx'=>DBIndex_Index, ],
    ];
}
```

Article 在使用继承而来的方法 list() 时，就会自动使用 table, primaryid, listFields属性实现列表数据查询。

##### filterFields 
用于设定查询时允许的检查字段范围（通常是建立了索引的字段,否则会降低查询效率）

##### depthStruct
用于取出数据时将数据库中字符串转换成对应数据类型

<hr>

### 语义化编程(编码风格)

AppSite追求语义化编程，即代码本身具有高度语言特征，精简同时易读。
例如，在注册用户的接口文件中，我们使用以下的实现：

```php
$scope   = $this->params["scope"] ?? 'common';

$registUser = User::common()->add($this->params);

if( $registUser->isSucceed() ){

    $user = new User($registUser->getContent(),null,$scope);
    return $user->access->authorize();
}
return $registUser;
```


<hr>

#### Nginx伪静态( 网站容器、API容器、管理后台路由都需要该支持 )

```

# 图片裁切服务 Image Crop Service
location ~ \.(jpg|jpeg|png|webp)!(.*)$ {

    rewrite ^(.*)\.(jpg|jpeg|png|webp)!(.*)$ $1/crop/$3.$2;

    if ( -e $request_filename ) {
        break;
    }

    rewrite ^(.*)/crop/(.*)\.(.*)$ $1&ext=.$3&file=$1.$3&method=$2;
    rewrite ^(/website.*)*$ /api/common/imageCrop$1;
}

location /favicon.ico {break;}
location /website/static { break; }
location /website/theme { break; }
location /manager/theme { break; }
location /api {
    rewrite ^(/api.*)*$ /api/?path=$1 break;
}
location /tester {
    rewrite ^(/tester.*)*$ /tester/?path=$1 break;
}
location /manager {
    rewrite ^(/manager.*)*$ /manager/?path=$1 break;
}
location /website {
    rewrite ^(/website.*)*$ /website/?path=$1 break;
}
location / {
    rewrite ^(/.*)*$ /website/?path=$1 break;
}
```

如果希望隐藏自己的后台系统，只需要先在后台的修改根目录配置，之后将管理后台部分的重写机制按照如下修改即可。

```
location /manager321 {
    rewrite ^(/manager321.*)*$ /manager/?path=$1 break;
}
location /manager {
    rewrite ^(/manager.*)*$ / break;
}
```
