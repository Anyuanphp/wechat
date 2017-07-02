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
            if($postObj->EventKey == 'clickMe'){
                $this->sendMsgAll($postObj);
            }else if($postObj->EventKey== 'introduceBlog'){
                $content = 'This is introduceBlog event'.$postObj->FromUserName;
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
            $data = [
                [
                    'title'=>'我的博客',
                    'description'=>'直道相思了无益 未妨惆怅是清狂',
                    'picurl'=>'http://zay.echophp.top/photo/wo2.jpg',
                    'url'  => 'http://www.echophp.top/'
                ],
                [
                    'title'=>'zay.echophp.top博客',
                    'description'=>'只要你不断走向远方， 它便一路相随。',
                    'picur'=>'http://zay.echophp.top/photo/hai3.jpg',
                    'url'  => 'http://zay.echophp.top/'
                ],

            ];
            $repalyObj->replyTextAndPicture($postObj,$data);
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
                default :
                    $content = '啦啦啦，我是可爱的卖报家！';
                    break;
            }
            $repalyObj->replyOneText($postObj,$content);
        }


    }

    //回复模板信息
    public function templateMsg($postObj)
    {
        $openId  = 'oeWizwSdYbf1sbQsY0ONiXkhUtww';
//        $content = 'openId:'.$postObj->FromUserName;
//        $this->getReplyObject()->replyOneText($postObj,$content);exit;
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
        $touser = 'oeWizwSdYbf1sbQsY0ONiXkhUtww';
        //组装群发数据接口
        $array  =  [
            'touser' => $touser,
            'text'   =>['content'=>'welcome zay wechat'],
            'msgtype'=>'text',
        ];
        $res = $this->httpCurlRequest($url,'post',json_encode($array));
        dump($res);

        //将数据转json 然后调用curl

    }

}











