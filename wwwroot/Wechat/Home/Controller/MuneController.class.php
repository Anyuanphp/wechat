<?php
namespace Home\Controller;

class MuneController extends CommonController
{
    //创建微信菜单
    public function createMune()
    {

        $access_token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token.'';
        $postArr = [
            'button'=>[
                [
                    //第yi个一级菜单
                    'name'=>urlencode('点击'),
                    'sub_button'=>[
                        [
                            'name'=>urlencode('群发测试'),
                            'type'=>'click',
                            'key' => 'sendAll',
                        ],
                        [
                            'name'=>urlencode('模板测试'),
                            'type'=>'click',
                            'key' => 'tempTest',
                        ],
                        [
                            'name'=>urlencode('网页授权'),
                            'type'=>'click',
                            'key' => 'pageCheck',
                        ],
                        [
                            'name'=>urlencode('图文测试'),
                            'type'=>'click',
                            'key' => 'textAndPicture',
                        ]
                    ]
                ],
                [
                    //第二个一级菜单
                    'name'=>urlencode('博客站'),
                    'sub_button'=>[
                        [
                            'name'=>urlencode('介绍博客'),
                            'type'=>'click',
                            'key' =>'introduceBlog',
                        ],
                        [
                            'name'=>urlencode('旅行日志'),
                            'type'=>'view',
                            'url' =>'http://zay.echophp.top',
                        ]
                    ]
                ],
                [
                    //第三个一级菜单
                    'name'=>urlencode('遇见你'),
                    'type'=>'view',
                    'url' =>'http://www.echophp.top',
                ],

            ],
        ];
        dump($postArr);
        echo '<hr/>';
        $postJson = urldecode( json_encode($postArr) );
        dump($postJson);
        echo '<hr/>';

        $result = $this->httpCurlRequest($url,'post',$postJson);
        dump($result);
    }
}