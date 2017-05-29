<?php

$layout = 'layout/page.html.php';

?>
<h1>Bienvenu sur CAMAGRU <?= $_SESSION['user'] ?>!!</h1>
<p><?= $date->format('D-d-M-Y H:i:s') ?></p>
