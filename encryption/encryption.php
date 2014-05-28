<?php
/**
*********************Encryption and Decryption library*******************
*-----includes-----
*-----> String Controller
*-----> String Utilities
*-----@author------
*------*ahmedali5530*------
**/
class Encryption extends Loader
{
	private $key,$iv_size,$iv;

	/**
	 * constructor
	 * @param $key (string:'TheKey')
	 * @return void
	 */
	public function __construct($key='ahmedali'){
		$this->iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$this->iv = mcrypt_create_iv($this->iv_size, MCRYPT_RAND);
		$this->key = trim($key);
	}

	public function encrypt($string){
		$string=trim($string);
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $string, MCRYPT_MODE_ECB, $this->iv));
	}

	public function decrypt($string){
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256,$this->key,base64_decode($string),MCRYPT_MODE_ECB,$this->iv));
	}
}
