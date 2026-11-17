<?php
class LtCookie
{
	public $configHandle;
	private $secretKey;

	public function __construct()
	{
		if (! $this->configHandle instanceof LtConfig)
		{
			if (class_exists("LtObjectUtil", false))
			{
				$this->configHandle = LtObjectUtil::singleton("LtConfig");
			}
			else
			{
				$this->configHandle = new LtConfig;
			}
		}
	}

	public function init()
	{ 
		$this->secretKey = $this->configHandle->get("cookie.secret_key");
		if(empty($this->secretKey))
		{
			trigger_error("cookie.secret_key empty");
		}
	}

	/**
	 * Decrypt the encrypted cookie
	 * 
	 * @param string $encryptedText 
	 * @return string 
	 */
	protected function decrypt($encryptedText)
	{
		$key = substr(str_pad($this->secretKey, 32, "\0"), 0, 32);
		$cryptText = base64_decode($encryptedText);
		$decryptText = openssl_decrypt($cryptText, 'aes-256-ecb', $key, OPENSSL_RAW_DATA);
		return trim((string)$decryptText);
	}

	/**
	 * Encrypt the cookie
	 * 
	 * @param string $plainText 
	 * @return string 
	 */
	protected function encrypt($plainText)
	{
		$key = substr(str_pad($this->secretKey, 32, "\0"), 0, 32);
		$encryptText = openssl_encrypt($plainText, 'aes-256-ecb', $key, OPENSSL_RAW_DATA);
		return trim(base64_encode((string)$encryptText));
	}

	/**
	 * Set cookie value to deleted with $name
	 * 
	 * @param array $args 
	 * @return boolean 
	 */
	public function delCookie($name, $path = '/', $domain = null)
	{
		if (isset($_COOKIE[$name]))
		{
			if (is_array($_COOKIE[$name]))
			{
				foreach($_COOKIE[$name] as $k => $v)
				{
					setcookie($name . '[' . $k . ']', '', time() - 86400, $path, $domain);
				}
			}
			else
			{
				setcookie($name, '', time() - 86400, $path, $domain);
			}
		}
	}

	/**
	 * Get cookie value with $name
	 * 
	 * @param string $name 
	 * @return mixed 
	 */
	public function getCookie($name)
	{
		$ret = null;
		if (isset($_COOKIE[$name]))
		{
			if (is_array($_COOKIE[$name]))
			{
				$ret = array();
				foreach($_COOKIE[$name] as $k => $v)
				{
					$v = $this->decrypt($v);
					$ret[$k] = $v;
				}
			}
			else
			{
				$ret = $this->decrypt($_COOKIE[$name]);
			}
		}
		return $ret;
	}

	/**
	 * Set cookie
	 * 
	 * @param array $args 
	 * @return boolean 
	 */
	public function setCookie($name, $value = '', $expire = null, $path = '/', $domain = null, $secure = 0)
	{
		if (is_array($value))
		{
			foreach($value as $k => $v)
			{
				$v = $this->encrypt($v);
				setcookie($name . '[' . $k . ']', $v, $expire, $path, $domain, $secure);
			}
		}
		else
		{
			$value = $this->encrypt($value);
			setcookie($name, $value, $expire, $path, $domain, $secure);
		}
	}
}
