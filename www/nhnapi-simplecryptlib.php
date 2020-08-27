<?php

/*

Requirement
1. php 5.1.2(because of sha256) or higher
2. libmcrypt 2.4.x(because of MCRYPT_RIJNDAEL_128) or higher

config example
./configure --with-mhash --with-mcrypt --with-dom --with-zlib-dir --with-apxs2=/usr/local/apache2/bin/apxs --with-config-file-path=./php.ini-recommended --enable-soap --enable-bcmath 

*/


//AES128 암호알고리즘의 블록사이즈
define('NHNAPISCL_BLOCK_SIZE', 16);

//AES128 암호알고리즘에 사용할 고정 Initial Vector
define('NHNAPISCL_IV', "c437b4792a856233c183994afe10a6b2");

class NHNAPISCL
{

        /**
         * HMAC-SHA256 서명 생성
         * @param data 서명할 데이터(UTF-8)
         * @param key 서명에 사용할 서명키
         * @return base64인코딩한 서명값 또는 Exception
         */
        function generateSign($data, $key){

           if(strlen($data)==0){
                 throw new Exception('invalid data');
           }

           if(strlen($key)==0){
                 throw new Exception('invalid key');
           }

           $signature = hash_hmac('sha256', $data, $key, true);

           return base64_encode($signature);
        }

        /**
         * AES128 암호알고리즘에 사용할 암호키 생성
         * @param timestamp 암호키생성에 사용할 데이터
         * @param key 암호키생성에 사용할 secret
         * @return hex인코딩한 암호키 또는 Exception
         */
        function generateKey($timestamp, $key){

           if(strlen($timestamp)==0){
                 throw new Exception('invalid timestamp');
           }

           if(strlen($key)==0){
                 throw new Exception('invalid key');
           }

	    $signature = hash_hmac('sha256', $timestamp, $key, true);

           for($i = 0; $i < 16; $i++){
                        $secretkey .= substr($signature,$i,1) ^ substr($signature,$i+16,1);
           }
         
           return bin2hex($secretkey);

        }

        /**
         * NHN API에 사용되는 타임스탬프 생성
         * @return 포맷에 맞춘 타임스탬프
         */
        function getTimestamp(){

           $timestamp=date("Y-m-d\TH:i:s",strtotime("-9hour"));

           $microtime=substr(microtime(),2,3);

           return $timestamp.".".$microtime."Z".rand(1000,9999);

        }

        /**
         * PKCS7 패딩생성
         * @param data 패딩할 데이터
         * @param block 암호생성기의 블록사이즈
         * @return pkcs7패딩을 추가한 데이터
         */
        function p7padding($data, $block){

                $len = strlen($data);

                $padding = $block - ($len % $block);

                return $data . str_repeat(chr($padding),$padding);

        }

        /**
         * PKCS7 패딩제거
         * @param text 패딩제거할 데이터
         * @return pkcs7패딩을 제거한 데이터 또는 Exception
         */
        function p7unpadding($text) {

                $pad = ord($text{strlen($text)-1});

                if ($pad > strlen($text)){
                        return ('invalid padding');
                }

                if (strspn($text, chr($pad), strlen($text) - $pad) != $pad){
                         throw new Exception('invalid padding');
		 }

                return substr($text, 0, -1 * $pad);

        }

        /**
         * AES128 암호화
         * @param secret hex인코딩한 암호키
         * @param text 암호화할 평문(UTF-8)
         * @return base64인코딩한 암호값 또는 Exception
         */
        function encrypt($secret, $text){

           if(strlen($secret)==0){
                 throw new Exception('invalid secret');
           }
         
           if(strlen($text)==0){
                 throw new Exception('invalid text');
           }

           $padded = $this->p7padding($text, NHNAPISCL_BLOCK_SIZE);

           $iv=pack('H*',NHNAPISCL_IV);

           $key=pack('H*',$secret);

           $ctext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $padded, MCRYPT_MODE_CBC, $iv);

           return base64_encode($ctext);
        }

        /**
         * AES128 복호화
         * @param secret hex인코딩한 암호키
         * @param text base64인코딩한 암호값
         * @return 복호화된 평문(UTF-8) 또는 Exception
         */
        function decrypt($secret, $text){

           if(strlen($secret)==0){
                 throw new Exception('invalid secret');
           }

           if(strlen($text)==0){
                 throw new Exception('invalid text');
           }

           $ctext = base64_decode($text);

           $iv=pack('H*',NHNAPISCL_IV);

           $key=pack('H*',$secret);

           $dtext = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ctext, MCRYPT_MODE_CBC, $iv);

           return $this->p7unpadding($dtext);
       
        }

        /**
         * SHA256 해쉬 생성
         * @param data 해쉬할 데이터(UTF-8)
         * @return hex인코딩한 해쉬값
         */
        function sha256($data){
          
           return hash('sha256', $data);

        }

		/**
         * hex인코딩한 데이터를 숫자로 변환(bcmath가 필요함)
         * @param hex hex인코딩한 데이터
         * @return 숫자형 문자열        
        */
        function bchexdec($hex){
           $len = strlen($hex);
           for ($i = 1; $i <= $len; $i++)       
                $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
           return $dec;
        }
}