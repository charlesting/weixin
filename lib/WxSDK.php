<?php

namespace CT\Wxpay\lib;

require_once dirname(__FILE__) . "/CTCurl.php";
require_once dirname(__FILE__) . "WxPayConfig.php";

use CT\Wxpay\lib\CTCurl;

class WxSDK
{
    private $appId;
    private $appSecret;

    public function __construct($appId=NULL, $appSecret=NULL)
    {

        $this->appId = (!$appId) ? $appId : WxPayConfig::APPID;
        $this->appSecret = (!$appSecret) ? $appSecret : WxPayConfig::APPSECRET;
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }


    public function createmenu($menu){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$menu);

        return $return_data;
    }

    public function menuget(){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$accessToken;
        $mycurl = new CTCurl();

        $return_data = $mycurl->get($url);

        return $return_data;
    }

    public function postlogo($logo){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$accessToken;
        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$logo);

        return $return_data;
    }

    public function postcard($card){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/card/create?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$card);

        return $return_data;
    }

    public function checkcard($cardinfo){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/card/code/get?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$cardinfo);

        return $return_data;
    }

    public function getallcard(){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/card/user/getcardlist?access_token='.$accessToken;
        $mycurl = new CTCurl();
        $code = '{"openid": "12312313","card_id":""}';

        $return_data = $mycurl->post($url,$code);
        return $return_data;
    }

    public function hexiaocode(){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/card/code/consume?access_token='.$accessToken;
        $code = '{"code": "840587563572"}';
        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$code);

        return $return_data;
    }

    /************************************************************************
     * 微信用户管理接口
     */


    /**
     * 创建用户分组
     * @param $group_json
     * {"group":{"name":"test"}}
     * 一个公众账号，最多支持创建100个分组。
     * @return mixed
     */

    public function creategroup($group_json){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$group_json);

        return $return_data;
    }

    /**
     * 获取所有分组信息
     * @return mixed
     * {"groups": [
     *  {
     *      "id": 0,
     *      "name": "未分组",
     *      "count": 72596
     *  },
     *  {
     *      "id": 1,
     *      "name": "黑名单",
     *      "count": 36
     *  },
     *  {
     *      "id": 2,
     *      "name": "星标组",
     *      "count": 8
     *  },
     *  {
     *      "id": 104,
     *      "name": "华东媒",
     *      "count": 4
     *  },
     *  {
     *      "id": 106,
     *      "name": "★不测试组★",
     *      "count": 1
     *  }
     *  ]
     * }
     */
    public function getgroups(){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url);

        return $return_data;
    }

    /**
     * 获取单一用户分组
     * @param $userid_json
     * {"openid":"od8XIjsmk6QdVTETa9jLtGWA6KBc"}
     *
     * @return mixed
     * { "groupid": 102 }
     */
    public function getusergroup($userid_json){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/getid?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$userid_json);

        return $return_data;
    }

    /**
     * 更新分组信息
     * @param $groupid_json
     *
     * {"group":{"id":108,"name":"test2_modify2"}}
     *
     * @return mixed
     */
    public function updategroupinfo($groupid_json){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/update?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$groupid_json);

        return $return_data;
    }

    /**
     *
     * 更新用户分组
     *
     * @param $usergroup_json
     *
     * {"openid":"oDF3iYx0ro3_7jD4HFRDfrjdCM58","to_groupid":108}
     *
     * @return mixed
     */
    public function updateusergroup($usergroup_json){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$usergroup_json);

        return $return_data;
    }

    /**
     *
     * 批量修改用户分组
     * @param $userlist_json
     *
     * {"openid_list":["oDF3iYx0ro3_7jD4HFRDfrjdCM58","oDF3iY9FGSSRHom3B-0w5j4jlEyY"],"to_groupid":108}
     *
     * @return mixed
     */
    public function updateusersgroup($userlist_json){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$userlist_json);

        return $return_data;
    }

    /**
     *
     * 删除分组
     * @param $groupid_json
     * {"group":{"id":108}}
     * @return mixed
     */
    public function deletegroup($groupid_json){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/delete?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$groupid_json);

        return $return_data;
    }

    /**
     * @param $user_json
     * {"openid":"oDF3iY9ffA-hqb2vVvbr7qxf6A0Q", "remark":"pangzi"}
     * @return mixed
     */
    public function setuserremark($user_json){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/delete?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$user_json);

        return $return_data;
    }

    /**
     *
     * 获取用户基本信息
     * @param $user_openid
     * @param string $lang
     * @return mixed
     * {
     *  "subscribe": 1,
     *  "openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M",
     *  "nickname": "Band",
     *  "sex": 1,
     *  "language": "zh_CN",
     *  "city": "广州",
     *  "province": "广东",
     *  "country": "中国",
     *  "headimgurl":    "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
     *  "subscribe_time": 1382694957,
     *  "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
     *  "remark": "",
     *  "groupid": 0
     * }
     */
    public function getuserbaseinfo($user_openid, $lang='zh_CN'){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$accessToken.'&openid='.$user_openid.'&lang='.$lang;

        $mycurl = new CTCurl();
        $return_data = $mycurl->get($url);

        return $return_data;
    }

    /**
     *  批量获取用户基本信息
     * @param $userlist_json
     * {
     *      "user_list": [
     *          {
     *              "openid": "otvxTs4dckWG7imySrJd6jSi0CWE",
     *              "lang": "zh-CN"
     *          },
     *          {
     *              "openid": "otvxTs_JZ6SEiP0imdhpi50fuSZg",
     *              "lang": "zh-CN"
     *          }
     *      ]
     * }
     * @return mixed
     * 最多支持一次拉取100条
     * {
     *      "user_info_list": [
     *          已经关注用户
     *          {
     *              "subscribe": 1,
     *              "openid": "otvxTs4dckWG7imySrJd6jSi0CWE",
     *              "nickname": "iWithery",
     *              "sex": 1,
     *              "language": "zh_CN",
     *              "city": "Jieyang",
     *              "province": "Guangdong",
     *              "country": "China",
     *              "headimgurl": "http://wx.qlogo.cn/mmopen/xbIQx1GRqdvyqkMMhEaGOX802l1CyqMJNgUzKP8MeAeHFicRDSnZH7FY4XB7p8XHXIf6uJA2SCunTPicGKezDC4saKISzRj3nz/0",
     *              "subscribe_time": 1434093047,
     *              "unionid": "oR5GjjgEhCMJFyzaVZdrxZ2zRRF4",
     *              "remark": "",
     *              "groupid": 0
     *          },
     *          未关注用户(没有信息)
     *          {
     *              "subscribe": 0,
     *              "openid": "otvxTs_JZ6SEiP0imdhpi50fuSZg",
     *              "unionid": "oR5GjjjrbqBZbrnPwwmSxFukE41U",
     *          }
     *      ]
     *  }
     *
     *
     */
    public function getusersbaseinfo($userlist_json){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$userlist_json);

        return $return_data;
    }


    /**
     *
     * 获取所有用户列表 默认数量1W,分次获取,起步开始id $next_openid
     * @param string $next_openid
     * @return mixed
     *  {
     *      "total": 33061,
     *      "count": 10000,
     *      "data": {
     *          "openid": [
     *              "oqPm3uExjGC0Rvby3tmtoXy9XQqk",
     *              "oqPm3uAWkKxDb6vuYdpp7i8RStVQ",
     *              ......
     *          ]
     *      },
     *      "next_openid": "oqPm3uHypMDjHZrNMWeG-MhQk-tI"
     *  }
     *
     */

    public function getuserslist($next_openid=''){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$accessToken.'&next_openid='.$next_openid;

        $mycurl = new CTCurl();
        $return_data = $mycurl->get($url);

        return $return_data;
    }

    /************************************************************************
     * 微信门店接口
     */

    /**
     * @param $img_url
     * @return mixed
     */
    public function uploadimg($img_url){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$img_url);

        return $return_data;
    }

    /**
     *
     * 创建门店
     * @param $poi_data ={
     *      "business":{
     *         "base_info":{
     *             "sid":"33788392",
     *             "business_name":"麦当劳",
     *             "branch_name":"艺苑路店",
     *             "province":"广东省",
     *             "city":"广州市",
     *             "district":"海珠区",
     *             "address":"艺苑路11 号",
     *             "telephone":"020-12345678",
     *             "categories":["美食,小吃快餐"],
     *             "offset_type":1,
     *             "longitude":115.32375,
     *             "latitude":25.097486,
     *             "photo_list":[{"photo_url":"https:// XXX.com"}，{"photo_url":"https://XXX.com"}],
     *             "recommend":"麦辣鸡腿堡套餐，麦乐鸡，全家桶",
     *             "special":"免费wifi，外卖服务",
     *             "introduction":"麦当劳是全球大型跨国连锁餐厅，1940 年创立于美国，在世界上
     *             大约拥有3 万间分店。主要售卖汉堡包，以及薯条、炸鸡、汽水、冰品、沙拉、 水果等
     *             快餐食品",
     *             "open_time":"8:00-20:00",
     *             "avg_price":35
     *             }
     *       }
     * }
     * @return mixed
     */
    public function addpoi($poi_data){
        $accessToken = $this->getAccessToken();
        $url = 'http://api.weixin.qq.com/cgi-bin/poi/addpoi?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$poi_data);

        return $return_data;

    }

    /**
     * 查询单一门店信息
     * @param $poi_id =
     *  {
     *      "poi_id":"271262077"
     *  }
     * @return mixed
     */
    public function getpoi($poi_id){
        $accessToken = $this->getAccessToken();
        $url = 'http://api.weixin.qq.com/cgi-bin/poi/getpoi?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$poi_id);

        return $return_data;
    }

    /**
     *
     * 批量查询门店信息
     * @param $poi_seach_info =
     *{
     *   "begin":0, //开始数
     *   "limit":10 //最大允许50，默认为20
     *}
     *
     * @return mixed
     */
    public function getpoilist($poi_seach_info){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/poi/getpoilist?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$poi_seach_info);

        return $return_data;
    }

    /**
     * 更新门店信息
     * @param $poi_data =
     * {
     *  "business ":{
     *      "base_info":{
     *        "poi_id ":"271864249"
     *        "telephone ":"020-12345678"
     *        "photo_list":[{"photo_url":"https:// XXX.com"}，{"photo_url":"https://XXX.com"}],
     *        "recommend":"麦辣鸡腿堡套餐，麦乐鸡，全家桶",
     *        "special":"免费wifi，外卖服务",
     *        "introduction":"麦当劳是全球大型跨国连锁餐厅，1940 年创立于美国，在世界上大约拥有3 万间分店。主要售卖汉堡包，以及薯条、炸鸡、汽水、冰品、沙拉、水果等快餐食品",
     *        "open_time":"8:00-20:00",
     *        "avg_price":35
     *        }
     *      }
     *  }
     *
     * 只能覆盖修改七个信息,人工审核
     *
     * @return mixed
     */
    public function updatepoi($poi_data){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/poi/update?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$poi_data);

        return $return_data;
    }

    /**
     *
     * 删除单个门店
     * @param $poi_del_info =
     * {
     *  "poi_id": "271262077"
     * }
     * @return mixed
     */
    public function delpoi($poi_del_info){
        $accessToken = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/poi/delpoi?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url,$poi_del_info);

        return $return_data;
    }


    /**
     * 获取门店分类
     * @return mixed
     */
    public function getwxcategory(){
        $accessToken = $this->getAccessToken();
        $url = 'http://api.weixin.qq.com/cgi-bin/api_getwxcategory?access_token='.$accessToken;

        $mycurl = new CTCurl();
        $return_data = $mycurl->post($url);

        return $return_data;

    }

    /************************************************************************
     * 微信卡券接口
     */

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public function returnApiTicket(){
        return $this->getJsApiTicket();
    }

    private function getJsApiTicket()
    {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("jsapi_ticket.json"));
        if ($data->expire_time < time()) {
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=wx_card&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen("jsapi_ticket.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }

        return $ticket;
    }

    private function getAccessToken()
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("access_token.json"));
        if ($data->expire_time < time()) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen("access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    private function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

}

