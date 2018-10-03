<?php

namespace common\helpers;

class Encrypt
{
    // Hàm mã hóa
    public  static function encryptCode($input, $key_seed){
        $input = trim($input);
        $block = mcrypt_get_block_size('tripledes', 'ecb');
        $len = strlen($input);
        $padding = $block - ($len % $block);
        $input .= str_repeat(chr($padding),$padding);
        $key = substr(md5($key_seed),0,24);
        $iv_size = mcrypt_get_iv_size(MCRYPT_TRIPLEDES,
            MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_data = mcrypt_encrypt(MCRYPT_TRIPLEDES, $key,
            $input,
            MCRYPT_MODE_ECB, $iv);
        return base64_encode($encrypted_data);
    }

    // Hàm giải mã
    public static function decryptCode($input, $key_seed)
    {
        $input = base64_decode($input);
        $key = substr(md5($key_seed),0,24);
        $text=mcrypt_decrypt(MCRYPT_TRIPLEDES, $key, $input,
            MCRYPT_MODE_ECB,'12345678');
        $block = mcrypt_get_block_size('tripledes', 'ecb');
        $packing = ord($text{strlen($text) - 1});
        if($packing and ($packing < $block)){
            for($P = strlen($text) - 1; $P >= strlen($text) - $packing; $P--){
                if(ord($text{$P}) != $packing){
                    $packing = 0;
                }
            }
        }
        $text = substr($text,0,strlen($text) - $packing);
        return $text;
    }

}

?>
