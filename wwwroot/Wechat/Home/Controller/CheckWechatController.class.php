<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2017/6/28
 * Time: 17:47
 */
namespace Home\Controller;

class CheckWechatController extends CommonController
{
    public function checkToken()
    {
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $token  = C('WEIXIN_TOKEN');
        $arr = array($token,$timestamp,$nonce);
        sort($arr);
        $temstr = implode( $arr );
        $tmpstr = sha1($temstr);

        if($tmpstr == $signature){
            echo $_GET['echostr'];
            exit;
        }
    }
}