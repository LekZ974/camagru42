<?php

$layout = 'layout/page.html.php';

?>
<html>
<body>
<div class="resetPassword">
    <form class="form" action="" method="POST">
        <div class="containerForm">
            <p>Entres ton nouveau mot de passe</p>
            <label><b>Password</b></label>
            <input id="newPassword" type="password" placeholder="Password" name="newPassword" required onchange="checkResetForm()">
            <label><b>Confirm Password</b></label>
            <input id="confirmPassword" type="password" placeholder="Password" name="confirmPassword" required onchange="checkResetForm()">
            <div id="divcomp"></div>
            <button id="createBtn" type="submit"><span>Reset password</span></button>
        </div>
        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="window.location.href='/login'" class="cancelbtn"><span>Cancel</span></button>
        </div>
    </form>
</div>
</body>
</html>