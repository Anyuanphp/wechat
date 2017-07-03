<?php
namespace Home\Controller;

class JsSdkController extends CommonController
{
    //获取jsapi_ticket
    private function getJsapiTicket()
    {
        //获取access_token
        $access_token = $this->getAccessToken();
        //拼凑获取jsapi_ticket的url
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';

        //缓存并返回获取到的jsapi_ticket
        if(session('jsapi_ticket') && session('jsapi_ticket_expire_time')>time()){
            return session('jsapi_ticket');
        }else{
            $res = $this->httpCurlRequest($url);
            session('jsapi_ticket',$res['ticket']);
            session('jsapi_ticket_expire_time',time()+7000);
            return $res['ticket'];
        }
    }

    //获取随机字符串
    private function getRandString($max)
    {
        $strArr = [
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','S','Y','Z',
            'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','s','y','z',
            '0','1','2','3','4','5','6','7','8','9'
        ];
        $tempStr = '';
        for ($i=0;$i<$max;$i++){
            $key      = rand(0,count($strArr)-1);
            $tempStr .= $strArr[$key];
        }
        return $tempStr;
    }

    //分享逻辑部分
    public function getShareLink()
    {
        $jsapi_ticket = $this->getJsapiTicket();
        $noncestr     = $this->getRandString(16);
        $temptime     = time();
        $current_url  = 'http://weixin.echophp.top/JsSdk/getShareLink';
        dump($jsapi_ticket);
        dump($noncestr);
        dump($temptime);
        dump($current_url);
        $tempStr      = 'jsapi_ticket='.$jsapi_ticket.'&noncestr='.$noncestr.'&timestamp='.$temptime.'&url='.$current_url.'';

        //生成JS-SDK权限验证的签名
        $signature    = sha1($tempStr);
        dump($signature);
        $this->assign(
            [
                'introduce' =>'AnYuan wechat',
                'appId'     =>C('APP_ID'),
                'noncestr'  =>$noncestr,
                'temptime'  =>$temptime,
                'signature' =>$signature,
            ]
        );
        $this->display();
    }
}