<?php

namespace APS;

/**
 * 通用测试类
 * Abstract Test class
 *
 * @package APS\core
 */
abstract class ASTester extends ASAPI {

    function __construct( $params = null, User $user = null ){

        parent::__construct( $params, $user );

        $this->checkSecurity();
    }

    private function checkSecurity(){
        if (
            !isset( static::$operationAccessRequirement ) &&
            !isset( static::$groupLevelRequirement ) &&
            !isset( static::$groupCharacterRequirement )
        ) {
            _ASRoute()->exit( $this->error(-300,i18n('TESTER_NOT_SECURITY'),'ASTester->run') );
        }
    }

    public function runTest():ASResult{

        if( $this->scope != 'system' ){
            return $this->run();
        }else{
            return $this->error(-1,i18n("SYS_API_NAL"),"ASTester->runAPI");
        }
    }

}