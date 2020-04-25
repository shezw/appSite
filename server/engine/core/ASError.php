<?php
/*

name:           Error 错误机制
version:        1.0.0
author:         Sprite
copyright:      动息科技,DonseeTec
website:        https://donsee.cn
date:           2020.3.18

*/

namespace APS;

/**
 * 错误处理机制
 * Error
 * @package APS\core
 */
class ASError{

	private $stack;

	function __construct(){
		$this->stack = [];
	}

	public static function shared():ASError{

		if ( !isset($GLOBALS['ASError']) ){
			$GLOBALS['ASError'] = new ASError();
		}
		return $GLOBALS['ASError'];
	}
	
	public function add( ASResult $operation ):void{
		$this->stack[] = $operation;
	}

	public function debug( bool $htmlMode = true ){

	    if( !empty($this->stack) ){
            echo $htmlMode ? "\n<pre>" : "\n";
            print_r($this->stack);
            echo $htmlMode ? "\n</pre>" : "\n";
        }
    }

}