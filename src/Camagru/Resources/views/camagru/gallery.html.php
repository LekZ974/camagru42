<?php

$layout = 'layout/page.html.php';

?>
<html>
<body>
<div class="gallery">
    <?php include __DIR__."/fragment/_pictures.html.php" ?>
    <?php include __DIR__."/fragment/_modales.html.php" ?>
    <?php
    echo 'Page : ';
    for ($i = 1 ; $i <= $pages['nbPages'] ; $i++)
    {
        echo '<a href="/gallery?page=' . $i . '">' . $i . '</a> ';
    }
    ?>
</div>
</body>
</html>

<script>

</script>