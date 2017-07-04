<?php
namespace Home\Controller;

class ReplyController extends CommonController
{
    //消息类型是text 回复的是单文本
    public function replyOneText($postObj,$content)
    {
        $toUser       = $postObj->FromUserName;
        $FromUserName = $postObj->ToUserName;
//        $CreateTime   = time();
//        $msgType      = 'text';
//        $content      = '欢迎关注张安源的公众号!';
        $template     = '<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                </xml>';
        $info        =  sprintf($template,$toUser,$FromUserName, time(),'text',$content);
        echo $info;
    }

    //消息类型是text 回复的是多图文
    public function replyTextAndPicture($postObj,$dataArr)
    {
        $toUser       = $postObj->FromUserName;
        $FromUserName = $postObj->ToUserName;
        $template     = '<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>'.count($dataArr).'</ArticleCount>
                        <Articles> ';
        foreach($dataArr as $v){
            $template     .='<item>
                        <Title><![CDATA['.$v["title"].']]></Title>
                        <Description><![CDATA['.$v["description"].']]></Description>
                        <PicUrl><![CDATA['.$v["picurl"].']]></PicUrl>
                        <Url><![CDATA['.$v["url"].']]></Url>
                        </item>';
        }
        $template     .= '</Articles>
                        </xml>';
        echo sprintf($template,$toUser,$FromUserName,time(),'news');
        exit;
    }

    //回复模板消息
    public function replayTemplateMsg($toUser,$template_id,$data,$jump_url ='')
    {
        $access_token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token.'';

        /*'  {
           "touser":"OPENID",
           "template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
           "url":"http://weixin.qq.com/download",
           "miniprogram":{
             "appid":"xiaochengxuappid12345",
             "pagepath":"index?foo=bar"
           },
           "data":{
                   "first": {
                       "value":"恭喜你购买成功！",
                       "color":"#173177"
                   },
                   "keynote1":{
                       "value":"巧克力",
                       "color":"#173177"
                   },
           }
        }'*/

        $array = [
            'touser'      => $toUser,
            'template_id' => $template_id,
            'url'         => $jump_url,
            'data'        => $data,
        ];

        $postJson     = json_encode($array);
        $res          = $this->httpCurlRequest($url,'post',$postJson);
        dump($res);
    }

}