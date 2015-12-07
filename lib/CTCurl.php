<?php

namespace CT\Wxpay\lib;

use Symfony\Component\Validator\Constraints\False;

class CTCurl {
    var $headers;
    var $user_agent;
    var $compression;
    var $cookie_file;
    var $proxy;

    function __construct($cookies=TRUE,$cookie='cookies.txt',$compression='gzip',$proxy='') {
        $this->headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $this->headers[] = 'Connection: Keep-Alive';
        $this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $this->user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36';
        $this->compression=$compression;
        $this->proxy=$proxy;
        $this->cookies=$cookies;
        if ($this->cookies == TRUE) $this->cookie($cookie);
    }

    function cookie($cookie_file) {
        if (file_exists($cookie_file)) {
            $this->cookie_file=$cookie_file;
        } else {
            fopen($cookie_file,'w') or $this->error('The cookie file could not be opened. Make sure this directory has the correct permissions');
            $this->cookie_file=$cookie_file;
            fclose($this->cookie_file);
        }
    }

    function get($url) {
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);

        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);

        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
        curl_setopt($process, CURLOPT_SSL_VERIFYHOST, false); //不验证证书

        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);

        curl_setopt($process,CURLOPT_ENCODING , $this->compression);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        $output = curl_exec($process);
        curl_close($process);

        return $output;
    }
    function post($url,$data) {
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($process, CURLOPT_HEADER, 1);
        curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
        curl_setopt($process, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt($process, CURLOPT_ENCODING , $this->compression);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($process, CURLOPT_POST, 1);
        $output = curl_exec($process);

        curl_close($process);
        return $output;
    }
    function error($error) {
        echo "<center><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;font-family: verdana; font-size: 10px'><b>cURL Error</b><br>$error</div></center>";
        die;
    }
} 