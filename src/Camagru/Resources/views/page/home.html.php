<?php

$layout = 'layout/page.html.php';

?>
<p>@todo Home Page <?= $date->format('c') ?>, for <?= $_request->get('firstName', 'Unknown') ?></p>
