<?php

use APS\ASAPI;
use APS\ASResult;
use APS\Filter;

/**
 * 系统回调
 * innerCallback
 */
class innerCallback extends ASAPI{

    const scope = ASAPI_Scope_System;

    /**
     * 回调数组
     * @var string | array | null
     *                    key-value list | JsonEncode of key-value list
     */
    protected $params;

    public function run(): ASResult
    {
        if( isset($this->params) ){ return $this->success('Empty Call','innerCallback'); }
        if( gettype($this->params)=='string'){
            $this->params = json_decode($this->params,true);
        }

        $success = 0 ;
        $errors  = [];
        $count   = count($this->params);
        $callbacks = $this->params;

        for ( $i = 0 ; $i < $count; $i++ ){

            $api    = $callbacks[$i]['api'] ?? $callbacks[$i]['action'];
            $params = Filter::removeInvalid( $callbacks[$i]['params'] );
            $callResult = ASAPI::systemInit( $api, $params )->run() ;

            if( $callResult->isSucceed() ){
                $success ++ ;
            }else{
                _ASRecord()->add([
                    'type'  =>'callback',
                    'status'=>788,
                    'content'=>['callbacks'=>$callbacks,'step'=>$i,'errors'=>$callResult],
                    'event'=>'innerCallback'
                ]);
                $callbackRemain = [];

                for ($j=$i; $j < count($callbacks); $j++) {
                    $callbackRemain[] = $callbacks[$j];
                }

                return $this->take(json_encode($callbackRemain,JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK))->error($callResult->getStatus(),$callResult->getMessage());
            }
        }
        _ASRecord()->add([
            'type'   =>'callback',
            'content'=>$callbacks,
            'event'=>'innerCallback'
        ]);
        return $this->success('Callback success','innerCallback');
    }

}