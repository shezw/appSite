<?php

namespace APS;

/**
 * 抽象基础类
 * ASObject
 *
 * 使得所有子类获得上下通讯能力
 * Provide sub-class communication capabilities
 *
 * @package APS\core
 * @mark    初始化 > 执行 > 获取结果 ? 设置状态和消息 > 返回结果
 * Initialize > Enforcement > Get Result ? Set Status / Message > Return Result
 */
abstract class ASObject{

    /**
     * 结果包装器
     * @var ASResult
     */
    public $result;

    /**
     * ASObject constructor.
     */
    function __construct(){
		$this->result = new ASResult();
	}

    /**
     * 设置主体别称
     * Alias of setContent
     * @param $content
     * @return $this
     */
	public function take( $content ): ASObject
    {
		$this->setContent( $content );
		return $this;
	}

    /**
     * 设置结果包装中的主数据
     * set content of result
     * @param $content
     */
	public function setContent( $content ){
        $this->result->setContent( $content );
    }

    /**
     * 设置方法签名
     * Set method sign
     * @param $sign
     */
    public function sign( String $sign ){
        $this->result->setSign($sign);
    }

    /**
     * 返回结果 通用
     * Returning result to the caller
     * @param Int|null $status
     * @param String|null $message
     * @param String|null $sign
     * @return ASResult
     */
	public function feedback( int $status = null , string $message = null, string $sign = null ):ASResult{
        if( isset($status) ){ $this->result->setStatus($status); }
		if( isset($message) ){ $this->result->setMessage($message); }
		if( isset($sign) ){ $this->result->setSign($sign); }

		$result = $this->result;
		$this->result = new ASResult();
		if( !$result->isSucceed() ){ _ASError()->add($result); }
		return $result;
	}

    /**
     * 返回错误
     * Returning Error result
     * @param Int $status
     * @param String $message
     * @param String|null $sign
     * @return ASResult
     */
	public function error( int $status, string $message, string $sign = null ):ASResult{

		return $this->feedback($status, $message, $sign);
	}

    /**
     * 返回成功
     * Return Success result
     * @param String|null $message
     * @param String|null $sign
     * @return ASResult
     */
	public function success( string $message = null , string $sign = null ):ASResult{

		$this->result->setStatus(0);

		return $this->feedback(0,$message,$sign);
	}

}
