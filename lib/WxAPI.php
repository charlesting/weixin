<?php

namespace CT\Wxpay\lib;

use CT\Wxpay\lib\WxSDK;
use CT\Wxpay\lib\WxPayConfig;
/**
 * Created by PhpStorm.
 * User: cs
 * Date: 15/11/25
 * Time: 下午9:04
 */

class WxAPI {
    private $AppID;
    private $AppSecret;

    public function __construct(){
        $this->AppID = WxPayConfig::APPID;
        $this->AppSecret = WxPayConfig::APPSECRET;

    }
}