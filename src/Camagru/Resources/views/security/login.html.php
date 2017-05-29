<?php

$layout = 'layout/page.html.php';

?>
<html>
<body>
<div id="connexion">
    <button class="nouveau" onclick="document.getElementById('create').style.display='block'"><span>Nouveau?</span></button>
    <?php include __DIR__."/fragment/_modal-create.html.php" ?>
    <button class="login" onclick="document.getElementById('auth').style.display='block'" style="width:auto;"><span>Login</span></button>
    <?php include __DIR__."/fragment/_modal-log.html.php" ?>
</div>
</body>
</html>