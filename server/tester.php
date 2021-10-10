<?php 
/**
 * AppSite Engine Server Side
 * 服务端入口
 *
 * @link https://appsite.cn
 * @author Sprite Shur  https://shezw.com  hello@shezw.com
 * @copyright shezw.com
 * @version 2.0
 */

require_once __DIR__.'/autoload.php';

use APS\DBField;
use APS\DBFields;
use APS\DBJoinParam;
use APS\DBJoinParams;
use APS\DBValue;
use APS\DBFieldStruct;
use APS\DBFilter;
use APS\DBConditions;
use APS\DBConditionKeyword;
use APS\DBTableStruct;
use APS\DBValues;
use APS\Encrypt;
use APS\UserAccount;
use APS\UserInfo;

$field = DBField::countLine();
$field = DBField::countLine('user_account');
$field = DBField::init( 'userid', 'user_account' )->sum();
$field = DBField::init( 'userid', 'user_account' )->countAs('total_user');
$field = DBField::init( 'balance', 'user_pocket' )->sumAs('total_balance');
$field = DBField::init('mobile')->distinct();
$field = DBField::init('mobile','user_account');
$field = DBField::init('mobile','user_account')->distinct();
$field = DBField::init('mobile','user_account')->distinctAs('unique_mobile');
$field = DBField::init('mobile','user_account')->distinctAs('unique_mobile')->countAs('total_mobile');
$field = DBField::init('location')->distance(10,100);
$field = DBField::init('location','item_company')->distance(10,100)->as('distance');

//var_dump($field->export());

$fields = DBFields::init();
$fields = DBFields::init('user_account');
$fields = DBFields::init('item_company')->countLine()
    ->and('location')->distance(100,99)->as('distance')
    ->and('registcapital')->as('capital')->sumAs('total_registcapital')
    ->and('address');

//var_dump($fields->export());

$fields = DBFields::init()->and('username')->and('nickname');
$fields = DBFields::initBySimpleList(['id','username','nickname','createtime']);
$fields = DBFields::allOf('user_account');

//$getUser = _ASDB()->get( $fields,'user_account', DBConditions::init('user_account')->where('groupid')->bigger(100) );
//var_dump($getUser);


$conditions = DBConditions::init('user_account')
    ->where('userid')->equal('123456')
    ->and('nickname')->search('administer')
    ->or('username')->belongTo(['admin','master'])
    ->or('level')->biggerAnd(9000)
    ->or('level')->less(100)
    ->orderBy('id', DBOrder_ASC)
    ->orderByDistance(10.99,99.885,'location', DBOrder_ASC)
    ->orderByAlias('distance')
    ->limitWith(1,5);

//var_dump($conditions->export());

$field = DBValue::init('userid')->string('121345678');

$values = DBValues::init('userid')->string('123456')
    ->set('nickname')->null()
    ->set('username')->string('master')
    ->set('location')->location(100.1111,90.224)
    ->set('homeLocation')->locations(99.33324,52.3)
    ->set('level')->number(9000);

//var_dump($values->export());
//var_dump($values->export(true));
//

$email = Encrypt::radomCode(10) . '@test.com';

$newUserInformation = DBValues::init( 'email' )->string($email)
    ->set('userid')->string(Encrypt::radomCode(8))
    ->set('username')->string(Encrypt::radomCode(10))
    ->set('avatar')->null()
;

//var_dump($userInformation->export());

//$addUser = _ASDB()->insert( $newUserInformation, 'user_account' );

//var_dump($addUser);


//$updateUserInformation = DBValues::init( 'email' )->string($email)
//    ->and('username')->string(Encrypt::radomCode(10))
//;
//$updateUser = _ASDB()->update( $updateUserInformation, 'user_account', DBConditions::init()->where('userid')->equal('Omrbq5LE') );
//var_dump($updateUser);


//var_dump(APS\DBFieldStruct::init('username',DBFieldType_STRING, 32 )->export() );
//var_dump(APS\DBFieldStruct::init('nickname',DBFieldType_STRING, 64 )->index(DBFieldIndex_FULLTEXT)->hasIndex() );


//$removeUser = _ASDB()->remove( 'user_account', DBConditions::init()->where('userid')->equal('Omrbq5LE') );
//var_dump($removeUser);


//$countUser = _ASDB()->count('user_account');
//$countUser = _ASDB()->count('user_account', DBConditions::init()->where('groupid')->equal('900'));
//var_dump($countUser);


//$checkUser = _ASDB()->check('s2DLS05ZvO@test.com','email','user_account', DBConditions::init()->where('username')->equal('S7C7IFc83j') );
//var_dump($checkUser);


//----- User Pocket


//$newPocket = DBValues::init( 'balance' )->number( Encrypt::radomNum(10) )
//    ->and('userid')->string(Encrypt::radomCode(8))
//    ->and('point')->number(Encrypt::radomNum(12))
//;

//$initPocket = _ASDB()->insert( $newPocket, 'user_pocket' );
//var_dump($initPocket);

//$getSum = _ASDB()->get(
//    DBFields::init('user_pocket')->and('balance')->sumAs('total_balance')->and('point')->sum(),
//    'user_pocket',
//    DBConditions::init()->groupBy('userid')->limitWith(0,5)
//);
//
//var_dump($getSum);

//var_dump( _ASDB()->showColumns('user_account') );
//var_dump( _ASDB()->showColumns('item_company') );
//var_dump(_ASDB()->showTables());
//var_dump( _ASDB()->showTables('bless') );
//var_dump(_ASDB()->exist('user_account'));


$joinParam = DBJoinParam::primary('user_account','userid');
$joinParam->get('userid')->and('username')->and('groupid');
//$joinParam->condition()->groupBy('userid');
//$joinParam = DBJoinParam::init('user_pocket')->asSub();
//$joinParam = DBJoinParam::init('user_pocket')->asSub('pocket');

$infoParam = DBJoinParam::init('user_info','userid','user_account.userid')->asSub('info');
$infoParam->get('userid')->and('province')->and('country')->and('province');
//$infoParam->filter('mobile')->isNotNull()->and('province')->equal('anhui');
$infoParam->filter('country')->isNotNull();
//$infoParam->select('userid')->equal('uAcSZxz1');

$pocketParam = DBJoinParam::init('user_pocket','userid','user_account.userid')->asSub('pocket');
$pocketParam->select('balance')->biggerAnd(0);

$joinParams = DBJoinParams::init( $joinParam )->leftJoin($infoParam)->leftJoin($pocketParam);

//var_dump($joinParam->subAlias());
//var_dump($joinParams->exportFields());
//var_dump($joinParams->exportJoin());

//var_dump($joinParams->export());
//var_dump($joinParams->convertSubData([]));

//var_dump(_ASDB()->getByJoin( $joinParams ));
//var_dump(_ASDB()->countByJoin( $joinParams ));

//var_dump(UserAccount::tableStruct);


//var_dump(DBTableStruct::init('user_account')->fromArray(UserAccount::tableStruct));

//var_dump(DBTableStruct::init('user_account')->fromArray(UserAccount::tableStruct)->export());
//
//$Class = UserInfo::class;
//
//var_dump($Class::table);
//var_dump(UserInfo::depthStruct);
//var_dump(\APS\ASModel::depthStruct);

//var_dump(\APS\Relation::primaryid);

//$keyId = '123';
//$value = [];
//$description = NULL;
//$scope = 'ads';
//
//$values = DBValues::init('keyid')->string($keyId)
//    ->set('content')->ASJson($value)
//    ->set('description')->stringIf($description)
//    ->set('scope')->stringIf($scope);
//
////var_dump($values->export());
//
//
//
//$conditions =
//    DBConditions::init(UserAccount::table)
//        ->where('keyid')->equal($keyId)
//        ->and('content')->equal($scope)
//        ->and('relationid')->equalIf($description)
//        ->and('scope')->equal($scope);
//
//var_dump($conditions->export());

/**
 * !!!!!!!!!!!!!
 */
//var_dump((UserAccount::class)::primaryid);


//var_dump(UserAccount::common()->list(DBConditions::init()->where('userid')->isNotNull()));

//var_dump(DBFields::init()->and('amount * quantity')->as('total')->export());

$userAccountStruct = DBTableStruct::init('user_account');
$userAccountStruct
    ->add( DBFieldStruct::init('username',DBField_String,24)->index(DBIndex_Unique)->comment('用户名') )
    ->add( DBFieldStruct::init('nickname',DBField_String, 32 )->nullable() )
    ->add( DBFieldStruct::init('location', DBField_Location)->index( DBIndex_Spatial ) )
    ->timeField('lasttime')
    ->textField('introduce',9999, 'Nothing')->nullable()
    ->richTextField('homepage', true)
    ->decimalField('salary',5,10)->defaultBy(0)->indexWith(DBIndex_Index)
    ->booleanField('onlineStatus')->defaultBy(1)->comment('在线状态')->indexWith(DBIndex_Index)
    ->add( DBFieldStruct::init('level',DBField_Int, 5 )->defaultBy(0) );

//var_dump( $userAccountStruct->export() );

$conditions = DBConditions::init()
    ->where('userid')->equal($userid)
    ->and('itemtype')->equal($itemtype)
    ->and('status')->equal($status)

    ->limitWith( $size * ($page-1) , $size )
    ->orderWith($sort)
;


//var_dump($companyTableStructsObject->export());


//var_dump($companyTableStruct->export());

//var_dump( _ASDB()->increase('level', 'user_group', DBConditions::init()->where('uid')->equal('900') ) );


//var_dump( \APS\ASDB::generateTableQuery( $companyTableStructsObject ) );

//var_dump( _ASDB()->newTable( $companyTableStructsObject ) );

//var_dump( explode('.',15.55) );

//DBCondition::init( 'userid' );

//var_dump(DBCondition::init( 'userid' )->searchWith([
//    DBConditionKeyword::init( 'title', '工商' ),
//    DBConditionKeyword::init( 'description', '工商')
//]));
//
//
//var_dump(DBCondition::init('title')->search('文化'));
//
//var_dump(DBCondition::init('userid')->belongTo(['123','456','789']));
//
//var_dump([1,2,333]);
//
//$a = [1,2,333];
//
//var_dump(DBCondition::init('id','user_account')->belongTo($a));
