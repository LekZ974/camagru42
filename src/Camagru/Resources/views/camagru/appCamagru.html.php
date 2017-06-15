<?php

$layout = 'layout/page.html.php';

?>
<html>
<body>
<div class="my-gallery" id="mini-gallery">
<?php include __DIR__."/fragment/_mini-gallery.html.php" ?>
</div>
<div class="camagru" id="camagru">
    <?php include __DIR__."/fragment/_tools.html.php" ?>
    <?php include __DIR__."/fragment/_photo.html.php" ?>
</div>

<div class="side-frame">
</div>
<script type="text/javascript" src="/js/cam.js"></script>

</body>
</html>
