<?php
session_start();
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no, width=device-width">
    <title>甄阁地图显示</title>
    <link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
    <script src="http://cache.amap.com/lbs/static/es5.min.js"></script>
    <script src="http://webapi.amap.com/maps?v=1.3&key=4c296ef5a4a7c4f0f1b6252d4fac2008"></script>
    <script type="text/javascript" src="http://cache.amap.com/lbs/static/addToolbar.js"></script>
</head>
<body>
<div id="container"></div>

<script>
    var map = new AMap.Map('container', {
        resizeEnable: true,
        zoom:11,
        center:  [<?php echo $_SESSION['x'].','.$_SESSION['y']; ?>]

    });
    var marker = new AMap.Marker({
        position: [<?php echo $_SESSION['x'].','.$_SESSION['y']; ?>]
    });
    marker.setMap(map);
    var circle = new AMap.Circle({
        center: [<?php echo $_SESSION['x'].','.$_SESSION['y']; ?>],
        radius: 100,
        fillOpacity:0.2,
        strokeWeight:1
    })
    circle.setMap(map);
    map.setFitView()
    var info = new AMap.InfoWindow({
        content:'<?php echo $_SESSION['store_name'] ?>',
        offset:new AMap.Pixel(0,-28)
    })
    info.open(map,marker.getPosition())

</script>
</body>
</html>