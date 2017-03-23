<?php

$layout = 'layout/page.html.php';

?>
<h1>Bienvenu sur CAMAGRU</h1>
<p>@todo Home Page <?= $date->format('c') ?>, for <?= $_request->get('firstName', 'Unknown') ?></p>
