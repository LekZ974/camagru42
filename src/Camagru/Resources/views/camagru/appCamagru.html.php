<?php

$layout = 'layout/page.html.php';

?>
<html>
<body>
<div class="my-gallery">
    <p>Tes derniers snap :</p>
    <?php include __DIR__."/fragment/_pictures.html.php" ?>
    <?php include __DIR__."/fragment/_modales.html.php" ?>
</div>
<div class="camagru">
    <?php include __DIR__."/fragment/_tools.html.php" ?>
    <?php include __DIR__."/fragment/_photo.html.php" ?>
</div>

<div class="side-frame">
</div>
<script type="text/javascript" src="/js/cam.js"></script>

</body>
</html>
