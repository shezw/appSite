<?php

/**
 *   @name:           mixer 数据混合
 *   @version:        1.1.1
 *   @date:           2018.5.20, 2019.09.14, 2020.03.21
 *
 * 主要用于混合 数据->模板
 */

namespace APS;

/**
 * 混合器
 * Mixer
 * @package APS\tool
 */
class Mixer{

    /**
     * 混合数据与模板
     * Mix data with String template
     * @version  2.0
     * @param    array|null               $data           [数据]
     * @param    string|null              $module         [模板]
     * @return   string
     */
    public static function mix( array $data = null , string $module = null ){

        if (!$data||!$module) return "";

        Mixer::mixLoops( $data, $module );
        # 遍历混合循环体
        # Mix loops

        Mixer::mixConditions( $data, $module, true );
        # 遍历混合if条件块
        # Mix if-condition blocks

        Mixer::mixConditions( $data, $module, false );
        # 遍历混合not条件块
        # Mix not-condition blocks

        Mixer::mixI18n( $module );
        # 遍历本地化
        # Mix localization blocks

        Mixer::mixFields( $data, $module );
        # 遍历所有字段
        # Mix all key fields

        Mixer::revertVue( $module );

        return $module;
    }

    /**
     * 混合单个字段
     * mixField
     * @param $data
     * @param $struct
     * @return false|mixed|string
     */
    public static function mixField( $data, $struct ){

        $result = '';

        foreach ($struct as $key => $value) {

            if (is_array($value)) {

                if (isset($data[$key])) {

                    $result = Mixer::mixField($data[$key],$struct[$key]);
                    # 递归子数据
                }
            }else{

                if (isset($data[$key])) {
                    $result = is_array($data[$key]) ? json_encode($data[$key],JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK) : $data[$key];
                }
            }
        }
        return $result;
    }

    public static function mixLoops( &$data, &$module ):void{

        $Loops = Mixer::getLoops($module);
        if(empty($Loops)){ return ; }

        foreach ( $Loops as $key => $single ) {

            $origin = '||loop'.$single['code'].'loop||';
            $condit = '??'.$single['key'].'??';

            $datas = strstr($single['key'], '.') ? Mixer::getSubData($data,$single['key']) : $data[$single['key']] ;

            if ( isset($single['key']) && isset($datas) ) { #

                $mix = '';

                if(!is_array($datas)){
                    $module =  str_replace( $origin, $single['default'] ?? '', $module);
                    return ;
                }
                foreach ($datas as $k => $v) {
                    if(is_numeric($k) && !is_array($v) ){
                        $value = ['IDX'=>$k,'NUMBER'=>$k+1,'value'=>$v];
                        $mix .= Mixer::mix($value,$single['code']);
                    }else if( is_numeric($k) && is_array($v) ){
                        $v_ = $v;
                        $v_['IDX']=$k;
                        $v_['NUMBER']=$k+1;
                        $v_['value']=$v;
                        $mix .= Mixer::mix($v_,$single['code']);
                    }else{
                        $v['IDX'] = $k;
                        $v['NUMBER'] = $k+1;
                        $mix .= Mixer::mix($v,$single['code']);
                    }
                }
                if(isset($single['default'])){ $mix = str_replace('<*'.$single['default'].'*>', '', $mix); }
                $mix =  str_replace( $condit, '', $mix);
                $module =  str_replace( $origin, $mix, $module);
            }else{

                $module =  str_replace( $origin, $single['default'] ?? '', $module);

            }
        }
    }

    public static function getLoops( $module ){

        $result = [];
        $dResult= [];
        $struct = [];

        $preg = "/\|\|loop([^\|\|]+)?loop\|\|/S";
        # Loop Preg

        $cpreg = "/\?\?(.+?)\?\?/S";
        # Condition Preg

        $dpreg = "/\<\*(.+?)\*\>/";
        # Default Preg

        preg_match_all($preg, $module, $result );

        $s = $result[1];

        for ($i=0; $i < count($s) ; $i++) {

            $cstruct = [];
            preg_match_all($cpreg, $s[$i], $cstruct);
            preg_match_all($dpreg, $s[$i], $dResult );
            $key = $cstruct[1][0];
            $default = $dResult[1] ? $dResult[1][0] : null;

            $struct[]  = ['key'=>$key,'code'=>$s[$i],'default'=>$default];

        }
        return $struct;
    }

    public static function revertVue( & $module ){

        if( strstr($module,'{v{') ){
            $module = str_replace('{v{','{{',$module);
            $module = str_replace('}v}','}}',$module);
        }
    }


    /**
     * 混合判断条件
     * mixConditions
     * @param array   &$data    指针 数据
     * @param string  &$module  指针 模板
     * @param boolean $ifOrNotMode 肯定/否定判断
     * @return void $module 修改模板
     */
    public static function mixConditions( array &$data, string &$module, $ifOrNotMode = true ):void{

        $conditionStruct = static::getConditions( $module, $ifOrNotMode );

        if(empty($conditionStruct)){ return ; }

        for ($i=0; $i <count($conditionStruct) ; $i++) {
            foreach ($conditionStruct[$i] as $key => $value) {

                $origin = $ifOrNotMode ? '[if['.$value['code'].']if]' : '[not['.$value['code'].']not]' ;
                $condit = isset($value['symbol']) && isset($value['target'])
                    ? '::'.$key.$value['symbol'].$value['target'].'::'
                    : '::'.$key.'::' ;

                // }
                if ( // 条件不成立 Condition is not satisfied
                $ifOrNotMode ?  # IF Condition
                    (
                        (!isset($data[$key]) || !$data[$key] )&& !isset($value['symbol']) ||
                        # 不存在值
                        # value is not exists

                        in_array($value['symbol'], ['>','<','>=','<=','=']) && !Mixer::compareValue($data,$key,$value['symbol'],$value['target']) ||
                        # 对比属性值
                        # Compare value

                        $value['symbol']=='^' && !strstr($value['target'],(string)$data[$key]) ||
                        # 是否包含有属性
                        # Contain value

                        $value['symbol']=='.' && !Mixer::validValue($data,$value['key'])
                        # 是否含有子属性
                        # value has sub data
                    ) :
                    ( # Not Condition

                        !isset($value['symbol']) && (isset($data[$key]) && !!$data[$key] )||
                        # 存在值且不需要判断
                        # value is exists

                        in_array($value['symbol'], ['>','<','>=','<=','=']) && (isset($data[$key])|| Mixer::getSubData($data,$key) )&& Mixer::compareValue($data,$key,$value['symbol'],$value['target']) ||
                        # 对比属性值
                        # Compare value

                        $value['symbol']=='^' && isset($data[$key]) && strstr($value['target'],(string)$data[$key]) ||
                        # 是否包含有属性
                        # Contain value

                        $value['symbol']=='.' && Mixer::validValue($data,$value['key'])
                        # 是否含有子属性
                        # value has sub data

                    )

                ) {
                    $replacement = $value['default'] ?? '';

                }else{ // 条件成立 Condition is satisfied

                    $replacement = str_replace($condit,'',$value['code']);
                    if( isset($value['default']) ){
                        $replacement = str_replace( '<*'.$value['default'].'*>','',$replacement);
                    }
                }

                $module  = str_replace($origin, $replacement, $module);
            }
        }

        if( strstr($module,'[if[') ){
            static::mixConditions($data,$module,$ifOrNotMode);
        }
    }

    public static function getConditions( string $module, bool $ifOrNotMode = true){

        $result = [];
        $dResult= [];
        $struct = [];

        $preg  = $ifOrNotMode ? "/\[if\[(([^\[\]]*|(?R))*)]if]/S" : "/\[not\[([^\]\]]+)?\]not\]/S" ;
//        $preg  = $ifOrNotMode ? "/\[if\[([^\]\]]+)?\]if\]/S" : "/\[not\[([^\]\]]+)?\]not\]/S" ;
        # Condition Preg

        $dpreg = "/\<\*(.+?)\*\>/";
        # Default Preg

        $cpreg = "/::([^\]\]]+?)?::/S";
        # Condition Preg

        preg_match_all($preg, $module, $result);

        $s = $result[1];

        for ($i=0; $i < count($s) ; $i++) {

            $cstruct = [];
            preg_match_all($cpreg, $s[$i], $cstruct);
            preg_match_all($dpreg, $s[$i], $dResult );

            $default = $dResult[1] ? $dResult[1][0] : null;
            $key = $cstruct[1][0];

            if ( strstr( $key,'=') || strstr( $key,'>') || strstr( $key,'<') ) {
                $symbol = '=';
                foreach (['>','<','=','>=','<='] as $idx => $sig ) {
                    if(strstr($key, $sig)){ $symbol = $sig; }
                }
                $c = explode($symbol, $key);
                $key = $c[0];
                $struct[] = [$key => ['key'=>$c[0],'target'=>$c[1],'code'=>$s[$i],'symbol'=>$symbol,'default'=>$default]];
            }else if( strstr( $key,'^') ){
                $c = explode('^', $key);
                $key = $c[0];
                $struct[] = [$key => ['key'=>$c[0],'target'=>$c[1],'code'=>$s[$i],'symbol'=>'^','default'=>$default]];
            }else if( strstr( $key, '.') ){
                $c = explode('.', $key);
                $struct[] = [$key=>['key'=>$c,'code'=>$s[$i],'symbol'=>'.','default'=>$default]];
            }else{
                $struct[] = [$key => ['code'=>$s[$i],'default'=>$default]];
            }
        }

        return $struct;
    }

    public static function mixFields( &$data, &$module ):void{

        $struct = Mixer::getFields($module);
        if(empty($struct)){ return ; }

        foreach ($struct as $key => $value) {

            if (gettype($value)=='string') {

                if (isset($data[$value])) {
                    $module  =  str_replace($key, (gettype($data[$value])=='array') ? json_encode($data[$value],JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK) : $data[$value], $module);
                }else{
                    $module  =  str_replace($key, '', $module);
                }

            }else{
                $module  =  str_replace($key, Mixer::mixField($data,$value), $module);
            }
        }
    }

    public static function getFields( $module ){

        $result = [];

        $preg = "/\{\{([^\}\}]+)?\}\}/S";

        preg_match_all($preg, $module, $result);

        $s = $result[1];

        $struct = [];

        for ($i=0; $i < count($s) ; $i++) {

            if (strstr($s[$i],'.')) {

                $a = explode('.', $s[$i]);

                $struct['{{'.$s[$i].'}}'] = Mixer::structDepth($a);

            }else{
                $struct['{{'.$s[$i].'}}'] = trim($s[$i]);
            }
        }

        return $struct;
    }

    public static function mixI18n( &$module ){

        $struct = Mixer::getI18nFields($module);

        if(empty($struct)){ return ; }

        foreach ($struct as $key => $value) {

            if ( strstr($key,'.') ) {

                $keyAndScope = explode('.',$value);
                $module  =  str_replace($key, i18n($keyAndScope[1],$keyAndScope[0]), $module);

            }else{
                $module  =  str_replace($key, i18n($value), $module);
            }
        }
    }


    public static function getI18nFields( $module ){

        $result = [];

        $preg   = "/\{i18n\{([^\}\}]+)?\}i18n\}/S";

        preg_match_all($preg, $module, $result);

        $s = $result[1];

        $struct = [];

        for ($i=0; $i < count($s) ; $i++) {

            $struct['{i18n{'.$s[$i].'}i18n}'] = trim($s[$i]);

        }

        return $struct;
    }

    public static function getSubData( $data, $subKeys ){

        if(!isset($data)){ return null; }
        if(!strstr($subKeys, '.')){ return $data[$subKeys] ?? null;}

        $depthKeys = explode('.', $subKeys);

        $limit = 1;
        return Mixer::getSubData( $data[$depthKeys[0]], str_replace($depthKeys[0].'.', '', $subKeys,$limit) );
    }

    public static function validValue( $value, $key ):bool{

        if( !isset($value) ){ return false; }

        if( gettype($key)=='array' ){

            $key = Filter::arrayToString($key,'.');
        }

        if( strstr($key, '.') ){

            $value = Mixer::getSubData($value,$key);
        }
        return isset($value) && $value!=='' && $value!==false;
    }

    /**
     * 对比数据
     * compareValue
     * @param $data
     * @param String $key
     * @param string $symbol 对比符号
     * @param mixed  $target 对比值
     * @return   boolean
     */
    public static function compareValue( $data, ?String $key, string $symbol, $target ){

        if( strstr($key, '.') ){
            $value = Mixer::getSubData( $data, $key );
        }else{
            $value = $data[$key];
        }

        if(!isset($value)){ return false; }
        switch ($symbol){ # 对比方法
            case '=':
                $compare = $value == $target;
                break;
            case '>=':
                $compare = $value >= $target;
                break;
            case '<=':
                $compare = $value <= $target;
                break;
            case '>':
                $compare = $value > $target;
                break;
            case '<':
                $compare = $value < $target;
                break;
            default:
                $compare = false;
                break;
        }

        return $compare;
    }

    public static function structDepth( $input ){

        if (is_string($input)) {
            return [$input=>1];
        }else if( count($input)===1 ){
            return [$input[0]=>1];
        }

        $a = [];

        $a[$input[0]] = Mixer::structDepth(array_splice($input,1));

        return $a;
    }

    public static function buildPreg( $pregParams ){



    }

    public static function negate( bool $judge , bool $mode = true ){

        return $mode ? $judge : !$judge;

    }

    public static function debug( $any, bool $withTypeMode = false ){

        echo "<pre><code>";
        if ($withTypeMode) {
            var_dump($any);
        }else{
            print_r($any);
        }
        echo "\n\n";
        echo "</code></pre>";
        _ASError()->debug();

    }

}


