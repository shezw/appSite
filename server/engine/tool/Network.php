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

	public static function getAllHeaderParams(){

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

	public function get($url, array $params = null, $encode = false){

		$this->setParams($params);
		return $this->_request('get', $url, $encode);
	}

	public function post($url, array $params = null){

		$this->setParams($params);
		return $this->_request('post', $url);
	}

	public function put($url, array $params = null){

		$this->setParams($params);
		return $this->_request('put', $url);
	}

	public function delete($url, array $params = null){

		$this->setParams($params);
		return $this->_request('delete', $url);
	}

    public function getJson( string $url, array $params = null, $encode = false){
        $this->_decodeResponse = true;
        return $this->get($url,$params,$encode);
    }

    public function putJson( string $url, array $params = null, $encode = false){
        $this->_decodeResponse = true;
        return $this->post($url,$params);
    }

	public function useCert($certPath, $keyPath){

		$this->_useCert = true;
		$this->_certPath = $certPath;
		$this->_keyPath = $keyPath;
	}

	private function _request($method, $url, $encode = false){
		
		if (empty($url)) {
			return false;
		}
		$method = in_array($method, array('get', 'post', 'put', 'delete')) ? $method : 'get';
		$ch = curl_init();
		if (!empty($this->_header)) {
			$header = [];
			foreach ($this->_header as $k => $v) {
				$header[] = trim(trim($k, ' '), ':') . ': ' . $v;
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		if (!empty($this->_userAgent)) {
			curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
		}
		if (!empty($this->_refrence)) {
			curl_setopt($ch, CURLOPT_REFERER, $this->_refrence);
		}
		if ($method == 'get' && !empty($this->_params)) {
			$param = http_build_query($this->_params);
			$param = $encode ?: urldecode($param);
			$url = $url . '?' . $param;
		} elseif ($method == 'post') {
			if (!empty($this->_params)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_params);
			}
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		} elseif ($method == 'put') {
			if (!empty($this->_params)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_params);
			}
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		} elseif ($method == 'delete') {
			if (!empty($this->_params)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_params);
			}
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}

		if ($this->_useCert == true) {
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLCERT, $this->_certPath);
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, $this->_keyPath);
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