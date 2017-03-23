<?php

$layout = 'layout/page.html.php';

?>
<html>
<head>
    <script src="/web/js/cam.js" type="text/javascript" charset="UTF-8"></script>
</head>
<body>
<div class="camagru">
    <video id="video" autoplay></video>
    <br>
    <input type="file" accept="image/*;capture=camera">
    <button onclick="changeFilter()">Filters</button>
    <button onclick="snapshot()">Take Snapshot</button>
</div>
<video autoplay></video>
<img src="">
<canvas id="canvas" style="display:none;"></canvas>
<script type="text/javascript" src="../js/cam.js"></script>
</body>
</html>