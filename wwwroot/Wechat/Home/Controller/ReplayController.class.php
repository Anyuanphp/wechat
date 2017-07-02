<?php
namespace Home\Controller;

class ReplayController extends CommonController
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
        $template     = '</Articles>
                        </xml>';
        echo sprintf($template,$toUser,$FromUserName,time(),'news');
        exit;
    }

}