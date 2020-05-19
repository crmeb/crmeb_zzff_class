<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"   content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"/>
    <title>直播回放</title>
    <link rel="stylesheet" href="https://g.alicdn.com/de/prismplayer/2.8.2/skins/default/aliplayer-min.css" />
    <script charset="utf-8" type="text/javascript" src="https://g.alicdn.com/de/prismplayer/2.8.2/aliplayer-min.js"></script>
    <style>
        .player-box{margin: 10px;}
    </style>
</head>
<body>
<div class="player-box">
    <div  class="prism-player" id="J_prismPlayer"></div>
</div>
<script>
    var player = new Aliplayer({
        id: 'J_prismPlayer',
        width: '100%',
        height:'380px',
        autoplay:false,
        //支持播放地址播放,此播放优先级最高
        // isLive:true,
        source : '{$record_url}',
    },function(player){
        console.log('播放器创建好了。')
    });
</script>
</body>
</html>
