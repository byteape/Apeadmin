<?php

/**
 * 加密与解密类
 * +------------------------------------说明------------------------------------+
 * 对称加密与解密
 * +------------------------------------说明------------------------------------+
 * +------------------------------------使用------------------------------------+
 * $str="abc";//待加密字符串
 * $key="fangwei";//加密密钥
 * $encrypt= new Encrypt($key);
 * dump($encrypt->encrypt($str));//加密
 * dump($encrypt->decrypt($encrypt->encryptStr));//解密
 * +------------------------------------使用------------------------------------+
 * Class FileTemp
 */
class  Encrypt{
    public $str='';//原字符串
    public $encryptStr='';//加密后的字符串
    public $key='';//加密钥匙

    /**
     * 构造函数
     * @param $key 密钥
     */
    public function __construct($key){
        $this->key=$key;
    }

    /**
     * 加密函数
     * @param $data
     * @return string
     */
    function encrypt($data) {
        $key=$this->key;
        $prep_code = serialize($data);
        $block = mcrypt_get_block_size('des', 'ecb');
        if (($pad = $block - (strlen($prep_code) % $block)) < $block) {
            $prep_code .= str_repeat(chr($pad), $pad);
        }
        $encrypt = mcrypt_encrypt(MCRYPT_DES, $key, $prep_code, MCRYPT_MODE_ECB);
        $encryptData=base64_encode($encrypt);

        $this->str=$data;
        $this->encryptStr=$encryptData;
        return $encryptData;
    }

    /**
     * 解密函数
     * @param $str
     * @return mixed
     */
    function decrypt($str) {
        $key=$this->key;
        $str = base64_decode($str);
        $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
        $block = mcrypt_get_block_size('des', 'ecb');
        $pad = ord($str[($len = strlen($str)) - 1]);
        if ($pad && $pad < $block && preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str)) {
            $str = substr($str, 0, strlen($str) - $pad);
        }
        $decryptStr=unserialize($str);
        $this->str=$decryptStr;
        $this->encryptStr=$str;
        return unserialize($str);
    }
}
?>