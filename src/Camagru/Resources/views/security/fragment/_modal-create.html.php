<div id="create" class="modal">
    <form class="form animate" action="" method="POST">
        <div class="imgcontainer">
            <span onclick="document.getElementById('create').style.display='none'" class="close" title="Close Modal">&times;</span>
        </div>
        <div class="containerForm">
            <h3>Login :</h3> <input type="text" name="createLogin" required>
            <h3>Mail :</h3> <input type="email" name="mail" required>
            <h3>Confirmer mail :</h3> <input type="email" name="confirmMail" required>
            <h3>Mot de passe :</h3> <input type="password" name="createPassword" required>
            <h3>Confirmer mot de passe :</h3> <input type="password" name="confirmPassword" required>
            <button class="login" type="submit" name="create" id="create"><span>Creer</span></button>
        </div>
        <div class="container" style="background-color:#f1f1f1">
            <button class="cancelbtn" type="button" onclick="document.getElementById('create').style.display='none'" class="cancelbtn"><span>Cancel</span></button>
        </div>
    </form>
</div>