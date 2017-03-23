<?php

$layout = 'layout/page.html.php';

?>
<html>
<body>
<div id="connexion">

    <button class="nouveau" onclick="document.getElementById('create').style.display='block'" style="width:auto;"><span>Nouveau?</span></button>
    <div id="create" class="modal">
        <form class="form animate" action="signUp.php" method="POST">
            <div class="imgcontainer">
                <span onclick="document.getElementById('create').style.display='none'" class="close" title="Close Modal">&times;</span>
            </div>
            <div class="containerForm">
                <h3>Login :</h3> <input type="text" name="createLogin" required>
                <h3>Mail :</h3> <input type="email" name="mail" required>
                <h3>Confirmer mail :</h3> <input type="email" name="mail2" required>
                <h3>Mot de passe :</h3> <input type="password" name="createPasswd" required>
                <h3>Confirmer mot de passe :</h3> <input type="password" name="createPasswd2" required>
                <button class="login" type="submit" name="create" id="create"><span>Creer</span></button>
            </div>
            <div class="container" style="background-color:#f1f1f1">
                <button class="cancelbtn" type="button" onclick="document.getElementById('create').style.display='none'" class="cancelbtn"><span>Cancel</span></button>
            </div>
        </form>
    </div>

    <button class="login" onclick="document.getElementById('auth').style.display='block'" style="width:auto;"><span>Login</span></button>
    <div id="auth" class="modal">
        <form class="form animate" action="signIn.php" method="POST">
            <div class="imgcontainer">
                <span onclick="document.getElementById('auth').style.display='none'" class="close" title="Close Modal">&times;</span>
            </div>
            <div class="containerForm">
                <label><b>Username</b></label>
                <input type="text" placeholder="Enter Username" name="login" required>
                <label><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="passwd" required>
                <button type="submit"><span>Login</span></button>
                <input type="checkbox" checked="checked"> Remember me
            </div>
            <div class="container" style="background-color:#f1f1f1">
                <button type="button" onclick="document.getElementById('auth').style.display='none'" class="cancelbtn"><span>Cancel</span></button>
                <span class="psw">Forgot <a href="#">password?</a></span>
            </div>
        </form>
    </div>
</div>
</body>
</html>