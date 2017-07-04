<?php
namespace Home\Controller;

use Home\Controller\ReplyController;

class IndexController extends CommonController  {

    public function index()
    {
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $token  = C('WEIXIN_TOKEN');
        $arr = array($token,$timestamp,$nonce);
        sort($arr);
        $temstr = implode( $arr );
        $tmpstr = sha1($temstr);

        if($tmpstr == $signature && $_GET['echostr']){
            echo $_GET['echostr'];
            exit;
        }else{
            $this->reponseMsg();
        }
    }

    //实例化回复控制器对象
    private function getReplyObject()
    {
        return new ReplyController();
    }

    //判断消息类型并调用相应类型回复方法
    private function reponseMsg()
    {
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        /*<xml>
         <ToUserName><![CDATA[toUser]]></ToUserName>
         <FromUserName><![CDATA[fromUser]]></FromUserName>
         <CreateTime>1348831860</CreateTime>
         <MsgType><![CDATA[text]]></MsgType>
         <Content><![CDATA[this is a test]]></Content>
         <MsgId>1234567890123456</MsgId>
         </xml>    消息推送的xml数据结构*/

        $postObj = simplexml_load_string($postArr);
        //判断消息类型
        if(strtolower($postObj->MsgType == 'event')){
            $this->eventSubscribe($postObj);
        }else if(strtolower($postObj->MsgType) == 'text'){
            $this->textTypeMsg($postObj);
        }

    }

    //消息类型是event
    private function eventSubscribe($postObj)
    {
        $repalyObj = $this->getReplyObject();
        $event  = strtolower($postObj->Event);
        //判断是否是订阅事件
        if($event == 'subscribe'){
            $content      = '欢迎关注张安源的公众号!目前正在开发中...';
            $repalyObj->replyOneText($postObj,$content);
        }else if ( $event == 'click' ){
            if($postObj->EventKey == 'sendAll'){
                $this->sendMsgAll($postObj);exit;
            }else if($postObj->EventKey == 'introduceBlog'){
                $content = 'toUser:|| '.$postObj->FromUserName.'|| FromUserName:'.$postObj->ToUserName;
            }else if($postObj->EventKey == 'pageCheck'){
                $this->getDetailInfo();exit;
            }else if($postObj->EventKey == 'tempTest'){
                $this->sendTemplateMsg();exit;
            }else if($postObj->EventKey == 'textAndPicture'){
                $this->getReplyObject()->replyTextAndPicture($postObj,$this->getArrayData());exit;
            }
//            $content = 'this is click event  ---- '.$postObj->EventKey;
            $repalyObj->replyOneText($postObj,$content);
        }else if($event == 'view'){

        }
    }

    //消息类型是text
    private function textTypeMsg($postObj)
    {
        $repalyObj = $this->getReplyObject();
        if($postObj->Content == '图文'){

            $repalyObj->replyTextAndPicture($postObj,$this->getArrayData());
        } else{
            switch ($postObj->Content){
                case '你好':
                    $content = '你好';
                    break;
                case '名字':
                    $content = '张安源';
                    break;
                case '博客':
                    $content = '<a href="http://www.echophp.tpo">这是我的博客哦</a>';
                    break;
                case '再见':
                    $content = '欢迎下次光临';
                    break;
                case '账单':
                    $this->sendTemplateMsg($postObj);
                    exit;
                    break;
                default :
                    $content = '啦啦啦，我是卖报的小行家！';
                    break;
            }
            $repalyObj->replyOneText($postObj,$content);
        }


    }

    //回复模板信息
    public function sendTemplateMsg()
    {
        $openId  = 'oeWizwSdYbf1sbQsY0ONiXkhUtww';
//          $openId  = $postObj->FromUserName;
//        $content = 'openId:'.$postObj->FromUserName;
//        $this->getReplyObject()->replyOneText($postObj,$content);exit;

        //微信自定义添加模板后的一个id
        $template_id = 't99dp_H6iZD-XE7sV_HRw6W6JY2M5H_rxv5ITtamubQ';

        $data = [
            'name'=>['value'=>'Anyuan','color'=>'#abcdef'],
            'money'=>['value'=>100,'color'=>'#173177'],
            'time'=>['value'=>date('Y-m-d H:i:s'),'color'=>'#666']
        ];
        $jump_url = 'http://www.echophp.top';
        $this->getReplyObject()->replayTemplateMsg($openId,$template_id,$data,$jump_url);

    }

    //群发接口
    public function sendMsgAll()
    {
//        $content = "$postObj->FromUserName";
//        $this->getReplyObject()->replyOneText($postObj,$content);exit;
        //获取全局access_token；

        $access_token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.$access_token.'';
//        $touser = $postObj->FromUserName;
        $touser = 'oeWizwSdYbf1sbQsY0ONiXkhUtww';
        //组装群发数据接口
        $array  =  [
            'touser' => $touser,
            'text'   =>['content'=>'This is for all people to test'],
            "msgtype"=>"text"
        ];
        $res = $this->httpCurlRequest($url,'post',json_encode($array));
        dump($res);

        //将数据转json 然后调用curl

    }


    //微信基本授权  -- 获取用户同意授权的code
    public function getBaseInfo()
    {
        $app_id = C('APP_ID');

        //请求回调的方法 微信会将获取到的信息传递到该方法里面
        $redirect_uri = urlencode('http://weixin.echophp.top/index/getUserOpenId');

        //拼凑url
        /* scope的参数有两个
            snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），
            snsapi_userinfo （弹出授权页面  可通过openid拿到昵称、性别、所在地)*/

        $url =  'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$app_id.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';

        //跳转
        header('location:'.$url);

    }

    //微信基本授权  -- 获取网页授权的 access_token
    public function getUserOpenId()
    {
        //获取用户同意授权后的code
        $code   = $_GET['code'];
        $state   = $_GET['state'];

        echo $code;
        echo $state;
        //拼凑请求用户信息的url
        $url    = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.C('APP_ID').'&secret='.C('SECRET').'&code='.$code.'&grant_type=authorization_code';

//        可以在此显示活动页面
//        $this->display('activity.html');
//           注：     **活动页面地址应该是 getBaseInfo() 方法，由该方法跳转到此方法

        //调用方法请求用户信息
        $info   = $this->httpCurlRequest($url,'get');

        /*
        返回数据格式：
            { "access_token":"ACCESS_TOKEN",
             "expires_in":7200,
             "refresh_token":"REFRESH_TOKEN",
             "openid":"OPENID",
             "scope":"SCOPE" }
        */
        dump($info);


    }


    //微信详细授权  -- 获取用户同意授权的code
    public function getDetailInfo()
    {
        $app_id = C('APP_ID');

        //请求回调的方法 微信会将获取到的信息传递到该方法里面
        $redirect_uri = urlencode('http://weixin.echophp.top/index/getUserInfo');

        //拼凑url
        /* scope的参数有两个
            snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），
            snsapi_userinfo （弹出授权页面  可通过openid拿到昵称、性别、所在地)*/

        $url =  'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$app_id.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state='.C('USER_NAME').'#wechat_redirect';

        //跳转
        header('location:'.$url);
    }

    //微信详细授权  -- 获取用户详细信息
    public function getUserInfo()
    {
        //获取用户同意授权后的code
        $code   = $_GET['code'];
        $state  = $_GET['state'];

        echo '同意授权的code:'.$code;
        echo '传递的参数state:'.$state;

        //拼凑请求access_token的url
        $url    = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.C('APP_ID').'&secret='.C('SECRET').'&code='.$code.'&grant_type=authorization_code';

        //获取基本参数
        $result = $this->httpCurlRequest($url);

        $openid       = $result['openid'];
        $access_token = $result['access_token'];

        //拼凑获取详细信息的url
        $url    = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $user_info = $this->httpCurlRequest($url);

//        可以在此显示活动页面
//        $this->display('activity.html');
//           注：     **活动页面地址应该是 getBaseInfo() 方法，由该方法跳转到此方法


        /*
        返回数据格式：
           {    "openid":" OPENID",
                 " nickname": NICKNAME,
                 "sex":"1",
                 "province":"PROVINCE"
                 "city":"CITY",
                 "country":"COUNTRY",
                 "headimgurl":    "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ
                4eMsv84eavHiaiceqxibJxCfHe/46",
                "privilege":[ "PRIVILEGE1" "PRIVILEGE2"     ],
                 "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
                }

        */
        dump($user_info);
    }

    //获取一个二维数组
    private function getArrayData()
    {
        $data = [
            [
                'title'=>'直道相思了无益',
                'description'=>'直道相思了无益 未妨惆怅是清狂',
                'picurl'=>'http://zay.echophp.top/images/weixinewm.png',
                'url'  => 'http://www.echophp.top/'
            ],
            [
                'title'=>'zay.echophp.top博客',
                'description'=>'只要你不断走向远方， 它便一路相随。',
                'picurl'=>'http://zay.echophp.top/images/weixinewm.png',
                'url'  => 'http://zay.echophp.top/'
            ],

        ];
        return $data;
    }


}











