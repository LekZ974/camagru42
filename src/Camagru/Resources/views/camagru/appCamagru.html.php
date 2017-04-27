<?php

$layout = 'layout/page.html.php';

?>
<html>
<head>
</head>
<body>
<div class="camagru">
    <video id="video" autoplay></video>
    <img class="image" id="img" src="">
    <canvas style="display:none;"></canvas>
    <br>
    <input type="file" accept="image/png, image/jpg, image/jpeg" onchange="loadFile(event)">
    <div class="warning">
    <p>*only files png/jpg/jpeg</p>
    </div>
    <button onclick="changeFilter()">Filters</button>
    <button onclick="snapshot()">Take Snapshot</button>
    <button onclick="saveImg()">Save</button>

</div>
<script type="text/javascript" src="/js/cam.js"></script>
</body>
</html>
<script>
    var loadFile = function(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('img');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    };
</script>