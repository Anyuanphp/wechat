<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>微信js接口分享</title>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
</head>
<body>
    <?php echo ($introduce); ?>
<script>
    wx.config({
        debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '<?php echo ($appId); ?>', // 必填，公众号的唯一标识
        timestamp: '<?php echo ($temptime); ?>', // 必填，生成签名的时间戳
        nonceStr: '<?php echo ($noncestr); ?>', // 必填，生成签名的随机串
        signature: '<?php echo ($signature); ?>',// 必填，签名，见附录1
        jsApiList: [
                'onMenuShareTimeline',       //分享到朋友圈
                'onMenuShareAppMessage',    //“分享给朋友”
        ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });

    wx.ready(function(){
        // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。

        //分享到朋友圈
        wx.onMenuShareTimeline({
            title: 'This is interesting blog', // 分享标题
            link: 'http://www.echophp.top', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://echophp.top/wp-content/uploads/2017/04/DSC_0107-2000x1200.jpg', // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                alert('share success');
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
                alert('cancel success');
            }
        });

        //分享给朋友
        wx.onMenuShareAppMessage({
            title: 'AnyYuan blog', // 分享标题
            desc: 'This is My blog', // 分享描述
            link: 'http://zay.echophp.top', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://echophp.top/wp-content/uploads/2017/04/DSC_0107-2000x1200.jpg', // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                alert('Success share to friend');
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                alert('Cancel share to friend');
                // 用户取消分享后执行的回调函数
            }
        });

    });

    wx.error(function(res){
        // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
    });
</script>
</body>
</html>