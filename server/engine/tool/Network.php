<?php


namespace APS;

/**
 * 网络
 * Network
 * @package APS\tool
 */
class Network{

	private $_header    = [];
	private $_params    = [];
	private $_userAgent = '';
	private $_reference = '';
	private $_certPath  = '';
	private $_keyPath   = '';
	private $_useCert   = false;

	private $_decodeResponse = false;

	const POST_REQUEST = "POST";
	const GET_REQUEST  = "GET";
	const PUT_REQUEST  = "PUT";
	const DELETE_REQUEST = "DELETE";

	public function __construct(){

		$this->setHeader('X-Requested-With', 'XMLHttpRequest');
	}

	public function setReference($url){

		if (is_string($url) && !empty($ua)) {
			$this->_reference = $url;
		}
	}

	public function setUserAgent($ua){

		if (is_string($ua) && !empty($ua)) {
			$this->_userAgent = $ua;
		}
	}

	public function setHeader($name, $value = ''){

		if (is_string($name)) {
			$this->_header[$this->getHeaderParamKey($name)] = $value;
		} else if (is_array($name)) {
			foreach ($name as $key => $value) {
				$this->_header[$this->getHeaderParamKey($key)] = $value;
			}
		}
	}

	public function getHeaderParamKey( $key ){

		$key = ucwords(str_replace('_', ' ', strtolower($key)));
		$key = str_replace(' ', '-', $key);
		return $key;
	}

	public static function getAllHeaderParams(): array
    {

		$headerParams = [];

		foreach ($_SERVER as $k => $value) {
			if(substr($k, 0, 5)==='HTTP_'){

				$key = strtolower(str_replace('HTTP_', '', $k));
				$headerParams[$key] = $value;
			}
		}
		return $headerParams;
	}

	public static function getHeaderParam( $key ){

		$key = 'HTTP_'.strtoupper($key);
		return $_SERVER[$key] ?? NULL;
	}

	public function setParam( string $key, $value ){
	    $this->_params[$key] = $value;
    }

	public function setParams( array $keyValueArray = null ){
        if( !isset($keyValueArray) ){  return; }
        foreach ($keyValueArray as $key => $value) {
            $this->_params[$key] = $value;
        }
	}

	public function get($url, array $params = null){

		$this->setParams($params);
		return $this->_request(static::GET_REQUEST,$url);
	}

	public function post($url, array $params = null){

		$this->setParams($params);
		return $this->_request(static::POST_REQUEST,$url);
	}

	public function put($url, array $params = null){

		$this->setParams($params);
		return $this->_request(static::PUT_REQUEST,$url);
	}

	public function delete($url, array $params = null){

		$this->setParams($params);
		return $this->_request(static::DELETE_REQUEST,$url);
	}

    public function getJson( string $url, array $params = null){
        $this->_decodeResponse = true;
        return $this->get($url,$params);
    }

    public function putJson( string $url, array $params = null ){
        $this->_decodeResponse = true;
        return $this->post($url,$params);
    }

	public function useCert($certPath, $keyPath){

		$this->_useCert = true;
		$this->_certPath = $certPath;
		$this->_keyPath = $keyPath;
	}

	private function _request( $method, $url ){
		
		if (empty($url)) {
			return false;
		}
		$method = in_array($method, array(static::GET_REQUEST, static::POST_REQUEST, static::PUT_REQUEST, static::DELETE_REQUEST)) ? $method : static::GET_REQUEST;
		$ch = curl_init();

		switch ( $method ){

            case static::GET_REQUEST:
                if(!empty($this->_params)){
                    $url .= '?'.urlencode( http_build_query($this->_params) );
                }
            break;

            case static::POST_REQUEST:
            $this->_header['Content-Type'] = "application/json";
            case static::PUT_REQUEST:
            case static::DELETE_REQUEST:

            if (!empty($this->_params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_params);
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            break;
        }

		if ($this->_useCert == true) {
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLCERT, $this->_certPath);
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, $this->_keyPath);
		}
        if (!empty($this->_header)) {
            $header = [];
            foreach ($this->_header as $k => $v) {
                $header[] = "{$k}: {$v}";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if (!empty($this->_userAgent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
        }
        if (!empty($this->_refrence)) {
            curl_setopt($ch, CURLOPT_REFERER, $this->_refrence);
        }

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);

		$this->_header = $this->_params = [];

		curl_close($ch);

		return $this->_decodeResponse ? json_decode($output,true) : $output;
	}
	
}