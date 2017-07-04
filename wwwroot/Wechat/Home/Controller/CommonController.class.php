<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2017/6/28
 * Time: 17:44
 */
namespace Home\Controller;

use think\Controller;

class CommonController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    //httpCurl 请求
    public function httpCurlRequest($url,$type='get',$arr='',$resType='json')
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        if( $type == 'post' ){
            curl_setopt($ch,CURLOPT_POST);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        if($resType == 'json'){
            if( curl_errno($ch) ){
                return curl_errno($ch);
            }else{
                return json_decode($result,true);
            }
        }
    }

    //获取access token
    public function getAccessToken()
    {
        //请求地址
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='. C('APP_ID').'&secret='.C('SECRET').'';
        //加 true 转换成数组，不加转换成对象
        if(session('access_token') && session('expire_time')>time()){
            return session('access_token');
        }else{
            $res = $this->httpCurlRequest($url);
            session('access_token',$res['access_token']);
            session('expire_time',time()+6000);
            return $res['access_token'];
        }

    }

    //获取微信服务器 列表
    public function getWechatService()
    {
//        $access_token = 'RUxMbzvbH1Pi5jBx53FWJtNPyyCQdku_Qu9QS5ys8TGzMg8FEuATOoYBtTAyAibDe-bkFNwRlMSgbuYWEw1F0QzDjrOVQBU8akzO3eZ4aIQprPEPy5YZgUpYnUKyw-ZGEIFjAHANRO';
        $access_token = $this->getAccessToken();
        $url   = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$access_token.'';
        $res = $this->httpCurlRequest($url);
//        $arr = json_decode($res,true);
        dump($res);
    }

}