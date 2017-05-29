<?php

$layout = 'layout/page.html.php';

?>
<html>
<body>
<div class="forgotPassword">
    <form class="form" action="" method="POST">
        <div class="containerForm">
            <p>Pour récupérer ton mot de passe renseignes ces informations!</p>
            <label><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="login" required>
            <label><b>Email</b></label>
            <input type="email" placeholder="Enter email" name="mail" required>
            <button type="submit"><span>Go</span></button>
        </div>
        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="window.location.href='/login'" class="cancelbtn"><span>Cancel</span></button>
        </div>
    </form>
</div>
</body>
</html>