<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\Core;

class Url {
	/**
	 * @var string[]
	 */
	private $values = array();

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var string
	 */
	private $host;

	/**
	 * @param string $path
	 * @param string $host
	 */
	public function __construct($path, $host = null)
	{
		$this->path = $path;
		$this->host = $host;
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @return $this
	 */
	public function setValue($key, $value){
		$this->values[$key] = $value;
		return $this;
	}



	public function addPath($path) {
		while(substr($this->path, strlen($this->path)-1) == '/') {
			$this->path = substr($this->path, 0, strlen($this->path)-1);
		}

		$realPath = '';
		foreach(explode('/', $path) as $c => $pathPiece) {
			if($c > 0) {
				$realPath .= '/';
			}
			$realPath .= urlencode($pathPiece);
		}
		while(substr($path, 0, 1) == '/') {
			$path = substr($path, 1);
		}

		$this->path .= '/'.$path;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		$p = $this->path;
		if(count($this->values) > 0) {
			$s = http_build_query($this->values);
			if(strlen($s) > 0) {
				$p .= '?'.$s;
			}
		}

		return $p;
	}


	function __toString() {
		return $this->getPath();
	}

	/**
	 * @return string
	 */
	public function getFullPath() {
		return $this->host.(substr($this->getPath(), 0, 1) == '/' ? '' : '/').$this->getPath();
	}

	public static function parametersToArray($urlParameter)
	{
		$result = array();

		while(strlen($urlParameter) > 0) {
			// name
			$keypos= strpos($urlParameter,'=');
			$keyval = substr($urlParameter,0,$keypos);
			// value
			$valuepos = strpos($urlParameter,'&') ? strpos($urlParameter,'&'): strlen($urlParameter);
			$valval = substr($urlParameter,$keypos+1,$valuepos-$keypos-1);

			// decoding the respose
			$result[$keyval] = urldecode($valval);
			$urlParameter = substr($urlParameter,$valuepos+1,strlen($urlParameter));
		}

		return $result;
	}
}
