<?php
namespace Home\Model;

use Think\Model;

class IndexModel extends Model
{
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
}