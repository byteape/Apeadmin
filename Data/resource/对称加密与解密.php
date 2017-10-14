<?php 
$str="abc";//待加密字符串
$key="fangwei";//加密密钥
import("Org.Util.Encrypt");
$encrypt= new \Encrypt($key);
dump($encrypt->encrypt($str));//加密
dump($encrypt->decrypt($encrypt->encryptStr));//解密
dump($encrypt->str);//获取未加密密的字符串